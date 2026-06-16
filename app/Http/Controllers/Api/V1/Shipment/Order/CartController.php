<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Package;
use App\Services\PackageService;
use App\Services\PackageTrackingService;
use App\Enum\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Models\CartItemRoute;
use App\Models\PackageDetails;
use App\Models\ShipmentCompany;
use App\Services\CoverageService;
use App\Services\GoogleMapsService;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\PreviewFullPackageResource;
use App\Models\Category;
use App\Models\OrderItem;

class CartController extends Controller
{
    protected CoverageService $coverageService;

    public function __construct()
    {
        $this->coverageService = new CoverageService(new GoogleMapsService());
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;

        $carts = Cart::query()
            ->where('user_id', auth()->id())
            ->with([
                'items' => function ($query) {
                    $query->latest()->with([
                        'package.pickupAddress',
                        'package.dropoffAddress',
                        'package.packageDetails',
                        'route.pickupCompany', // 🔹 load pickup company
                        'route.dropoffCompany' // optional if needed
                    ]);
                },
            ]);

        $payload = paginate($carts, CartResource::class, $limit, $page);
        return responseJson(true, 'Cart fetched', $payload);

    }


    public function getItems(Request $request, $cartid)
    {
        $cart = Cart::with([
            'shipmentCompany',
            'items.shipmentCompany',
            // package relations
            'items.package',
            'items.package.pickupAddress',
            'items.package.dropoffAddress',
            'items.package.packageDetails',
            'items.package.type',
            'items.package.size',
            'items.package.deliveryType',
            'items.package.consignmentType',
            'items.package.category',
            'items.package.subCategory',
            'items.package.pickupAddress',
            'items.package.dropoffAddress',
            'items.package.packageDetails',
            'items.package.images',
        ])
        ->where('id', $cartid)
        ->where('user_id', auth()->id())
        ->first();


        if (!$cart) {
            return responseJson(false, 'Cart not found', null, 404);
        }

        return responseJson(true, 'Items fetched', new CartResource($cart));
    }


    public function getItem(Request $request, string $id)
    {
        try {
            // ======================
            // 1️⃣ Try CartItem first
            // ======================
            $cartItem = CartItem::with([
                'package.type',
                'package.size',
                'package.deliveryType',
                'package.consignmentType',
                'package.category',
                'package.subCategory',
                'package.pickupAddress',
                'package.dropoffAddress',
                'package.packageDetails',
                'package.images',
                'shipmentCompany',
            ])
            ->where('id', $id)
            ->whereHas('cart', fn ($q) =>
                $q->where('user_id', auth()->id())
            )
            ->first();

            if ($cartItem) {
                return responseJson(true, 'Item fetched', [
                    'item_id' => $cartItem->id,
                    'package' => new PreviewFullPackageResource($cartItem->package),
                ]);
            }

            // ======================
            // 2️⃣ Try OrderItem
            // ======================
            $orderItem = OrderItem::with([
                'package.type',
                'package.size',
                'package.deliveryType',
                'package.consignmentType',
                'package.category',
                'package.subCategory',
                'package.pickupAddress',
                'package.dropoffAddress',
                'package.packageDetails',
                'package.images',
                'shipmentCompany',
                'order',
            ])
            ->where('id', $id)
            ->first();

            if ($orderItem) {
                return responseJson(true, 'Item fetched', [
                    'item_id' => $orderItem->id,
                    'order_id' => $orderItem->order_id,
                    'package' => new PreviewFullPackageResource($orderItem->package),
                ]);
            }

            // ======================
            // 3️⃣ Not found
            // ======================
            return responseJson(false, 'Item not found', null, 404);

        } catch (\Throwable $e) {
            return responseJson(false, 'Failed to fetch item', $e->getMessage(), 500);
        }
    }


    // public function add(AddToCartRequest $request)
    // {
    //     $validated = $request->validated();

    //     $cart = Cart::firstOrCreate(['user_id' => auth()->id(), 'shipment_company_id' => $validated['shipment_company_id']??null], [
    //         //  'shipment_company_id' => $validated['shipment_company_id'],
    //     ]);

    //     // Optional: enforce one company per cart
    //     // if ($cart->shipment_company_id && $cart->shipment_company_id !== (int) $validated['shipment_company_id']) {
    //     //     // clear cart if different company
    //     //     $cart->items()->delete();
    //     //     $cart->shipment_company_id = $validated['shipment_company_id'];
    //     //     $cart->save();
    //     // }

    //     // Ensure we have a package id (create inline if absent)
    //     $packageId = $validated['package_id'] ?? null;
    //     if (!$packageId) {
    //         $package = PackageService::createFromPayload($validated, $request);
    //         $packageId = $package->id;
    //     }

    //     $item = CartItem::create([
    //         'cart_id' => $cart->id,
    //         'package_id' => $packageId,
    //         'shipment_company_id' => $validated['shipment_company_id']??null,
    //         'est_date' => $validated['est_date'] ?? null,
    //         'est_price' => $validated['est_price'] ?? null,
    //     ]);

    //     // // create a tracking entry for draft/cart stage (optional)
    //     // PackageTrackingService::createStatus(
    //     //     packageId: $packageId,
    //     //     orderItemId: null,
    //     //     status: OrderStatus::PENDING,
    //     //     location: $item->package->pickupAddress->address ?? null,
    //     //     description: 'Added to cart',
    //     //     metadata: ['context' => 'cart']
    //     // );
    //     $cart->update([
    //         'items_count' => $cart->items()->count(),
    //         'item_total_price' => $cart->items()->sum('est_price'),
    //     ]);

    //     return responseJson(true, 'Added to cart', $item);
    // }


    public function add(AddToCartRequest $request)
    {
        $validated = $request->validated();

        // 1) Create cart
        $cart = Cart::create(['user_id' => auth()->id()]);

        $pickup = $validated['pickup_address'];
        $dropoff = $validated['dropoff_address'];
        $details = PackageDetails::create($validated['details']);

        $savedPackages = [];

        // Determine if pickup/dropoff are villages
        $isPickupVillage = $pickup['is_village'] ?? false;
        $isDropoffVillage = $dropoff['is_village'] ?? false;

        // Prepare suggestion payload for all packages at once
        $suggestionPayload = [
            'preferred_company_id' => $validated['preferred_company_id'] ?? null,
            'packages' => [],
            'pickup_address' => [
                'zone_id' => $pickup['zone_id'],
                'longitude' => (float) $pickup['longitude'],
                'latitude' => (float) $pickup['latitude'],
                'city_id' => $pickup['city_id'],
                'state_id' => $pickup['state_id'],
                'is_village' => $isPickupVillage
            ],
            'dropoff_address' => [
                'zone_id' => $dropoff['zone_id'],
                'longitude' => (float) $dropoff['longitude'],
                'latitude' => (float) $dropoff['latitude'],
                'city_id' => $dropoff['city_id'],
                'state_id' => $dropoff['state_id'],
                'is_village' => $isDropoffVillage
            ]
        ];

        // Add all packages to the payload
        foreach ($validated['packages'] as $index => $packageData) {
            $suggestionPayload['packages'][] = [
                'id' => $index, // Add index as package ID for reference
                'category_id' => $packageData['category_id'],
                'sub_category_id' => $packageData['sub_category_id'],
                'size' => $packageData['size'],
                'weight' => $packageData['weight'],
                'piece' => $packageData['piece'],
                'piece_type' => $packageData['piece_type'] ?? 'small',
                'pieces_per_package' => $packageData['pieces_per_package'] ?? 1,
            ];
        }

        // Get suggestions for all packages at once
        $suggestions = app(\App\Services\ShipmentSuggestionService::class)
            ->getSuggestions($suggestionPayload);

        if (!$suggestions['success']) {
            return responseJson(false, $suggestions['message'] ?? "No shipment suggestions found", null, 422);
        }

        // Select suggestion index if provided, fallback to first
        $index = $validated['selected_suggestion_index'] ?? 0;
        $selected = $suggestions['results'][$index] ?? null;

        if (!$selected) {
            return responseJson(false, "Invalid suggestion index", null, 422);
        }

        // Process each package with the selected suggestion
        foreach ($validated['packages'] as $packageIndex => $packageData) {
            $packageData['pickup_address'] = $pickup;
            $packageData['dropoff_address'] = $dropoff;
            $packageData['details'] = $validated['details'];

            // Find the package price from the selected suggestion
            $packagePrice = null;
            $packageLegA = null;
            $packageLegB = null;

            if ($selected['type'] === 'direct') {
                // Find price for this specific package
                foreach ($selected['prices'] as $priceData) {
                    if ($priceData['package_id'] === $packageIndex) {
                        $packagePrice = $priceData['price'];
                        break;
                    }
                }
                $totalPrice = $selected['total'];
            } else { // split
                // Find prices for this specific package in both legs
                foreach ($selected['legA_prices'] as $legAPriceData) {
                    if ($legAPriceData['package_id'] === $packageIndex) {
                        $packageLegA = $legAPriceData['price'];
                        break;
                    }
                }
                foreach ($selected['legB_prices'] as $legBPriceData) {
                    if ($legBPriceData['package_id'] === $packageIndex) {
                        $packageLegB = $legBPriceData['price'];
                        break;
                    }
                }
                $totalPrice = $selected['total'];
            }

            if (!$packagePrice && !($packageLegA && $packageLegB)) {
                // Fallback: try to find in legA/legB arrays
                if ($selected['type'] === 'split') {
                    foreach ($selected['legA'] as $legAItem) {
                        if ($legAItem['package_id'] === $packageIndex) {
                            $packageLegA = $legAItem['price'];
                            break;
                        }
                    }
                    foreach ($selected['legB'] as $legBItem) {
                        if ($legBItem['package_id'] === $packageIndex) {
                            $packageLegB = $legBItem['price'];
                            break;
                        }
                    }
                }

                if (!$packagePrice && !($packageLegA && $packageLegB)) {
                    return responseJson(false, "Could not find price for package #" . ($packageIndex + 1), null, 422);
                }
            }

            // Determine shipment company IDs
            if ($selected['type'] === 'direct') {
                $chosenShipmentCompanyId = $selected['company']['id'];
                $legs = [$packagePrice];
                $handoff = null;
            } else { // split
                $chosenShipmentCompanyId = $selected['pickup_company']['id']; // Use pickup company ID
                $legs = [$packageLegA, $packageLegB];
                $handoff = $selected['handoff_point'];
            }

            // Store package
            $packageData['preferred_company_id'] = $chosenShipmentCompanyId;

            $package = PackageService::createFromPayload($packageData, $request);

            // Calculate the actual price for this package
            $packagePriceValue = 0;
            if ($selected['type'] === 'direct') {
                $packagePriceValue = $packagePrice['client_total'] ?? 0;
            } else {
                $packagePriceValue = ($packageLegA['client_total'] ?? 0) + ($packageLegB['client_total'] ?? 0);
            }

            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'package_id' => $package->id,
                'shipment_company_id' => $chosenShipmentCompanyId,
                'est_date' => $validated['est_date'] ?? null,
                'est_price' => $packagePriceValue,
                'requires_split' => $selected['type'] === 'split',
                'package_index' => $packageIndex, // Store original index for reference
            ]);

            $categoryType = Category::find($package->category_id)->type ?? 'weight';

            // Create route information
            CartItemRoute::create([
                'cart_item_id' => $cartItem->id,
                'pickup_company_id' => $selected['type'] === 'split' ? $selected['pickup_company']['id'] : $chosenShipmentCompanyId,
                'dropoff_company_id' => $selected['type'] === 'split' ? $selected['dropoff_company']['id'] : $chosenShipmentCompanyId,
                'pickup_address' => $pickup,
                'dropoff_address' => $dropoff,
                'is_split' => $selected['type'] === 'split',
                'legs' => $legs,
                'total_cost' => $packagePriceValue,
                'handoff_point' => $handoff,
                'calculation_type' => $categoryType,
                'suggestion_data' => [
                    'suggestion_type' => $selected['type'],
                    'suggestion_index' => $index,
                    'package_index' => $packageIndex,
                    'total_shipment_price' => $totalPrice,
                    'calculation_type' => $categoryType
                ]
            ]);

            $savedPackages[] = $cartItem;
        }

        // Update cart totals
        $this->updateCartTotals($cart);

        return responseJson(true, "Packages added to cart", [
            "cart" => $cart->load(['items.package.packageDetails', 'items.package.pickupAddress', 'items.package.dropoffAddress']),
            "cart_items" => $savedPackages,
            "selected_suggestion" => [
                'type' => $selected['type'],
                'index' => $index,
                'total_price' => $totalPrice,
            ]
        ]);
    }


    public function updateCart(UpdateCartRequest $request, $cartId)
    {
        $cart = Cart::with([
            'items.package.pickupAddress',
            'items.package.dropoffAddress',
            'items.package.packageDetails',
            'items.route'
        ])->findOrFail($cartId);

        $validated = $request->validated();

        // If packages are provided, we need to completely rebuild the cart
        // Otherwise, we update existing items with new addresses/details
        if (isset($validated['packages'])) {
            // Store current items to delete them later
            $oldItems = $cart->items;

            // Extract necessary data from first existing item
            $firstItem = $oldItems->first();
            if ($firstItem) {
                $existingPickup = $firstItem->package->pickupAddress->toArray();
                $existingDropoff = $firstItem->package->dropoffAddress->toArray();
                $existingDetails = $firstItem->package->packageDetails->toArray();
            }

            // Prepare updated addresses - merge with existing if provided
            $pickup = isset($validated['pickup_address'])
                ? array_merge($existingPickup ?? [], $validated['pickup_address'])
                : $existingPickup;

            $dropoff = isset($validated['dropoff_address'])
                ? array_merge($existingDropoff ?? [], $validated['dropoff_address'])
                : $existingDropoff;

            // Prepare updated details
            $details = isset($validated['details'])
                ? array_merge($existingDetails ?? [], $validated['details'])
                : $existingDetails;

            // Save/update details if they exist
            if ($details) {
                $detailsModel = PackageDetails::updateOrCreate(
                    ['id' => $existingDetails['id'] ?? null],
                    $details
                );
            }

            // Determine if pickup/dropoff are villages
            $isPickupVillage = $pickup['is_village'] ?? false;
            $isDropoffVillage = $dropoff['is_village'] ?? false;

            // Prepare suggestion payload - similar to add method
            $suggestionPayload = [
                'preferred_company_id' => $validated['preferred_company_id'] ?? null,
                'packages' => [],
                'pickup_address' => [
                    'zone_id' => $pickup['zone_id'],
                    'longitude' => (float) $pickup['longitude'],
                    'latitude' => (float) $pickup['latitude'],
                    'city_id' => $pickup['city_id'],
                    'state_id' => $pickup['state_id'],
                    'is_village' => $isPickupVillage
                ],
                'dropoff_address' => [
                    'zone_id' => $dropoff['zone_id'],
                    'longitude' => (float) $dropoff['longitude'],
                    'latitude' => (float) $dropoff['latitude'],
                    'city_id' => $dropoff['city_id'],
                    'state_id' => $dropoff['state_id'],
                    'is_village' => $isDropoffVillage
                ]
            ];

            // Add all packages to the payload (from request)
            foreach ($validated['packages'] as $index => $packageData) {
                $suggestionPayload['packages'][] = [
                    'id' => $index,
                    'category_id' => $packageData['category_id'],
                    'sub_category_id' => $packageData['sub_category_id'],
                    'size' => $packageData['size'],
                    'weight' => $packageData['weight'],
                    'piece' => $packageData['piece']
                ];
            }

            // Get suggestions for all packages at once
            $suggestions = app(\App\Services\ShipmentSuggestionService::class)
                ->getSuggestions($suggestionPayload);

            if (!$suggestions['success']) {
                return responseJson(false, $suggestions['message'] ?? "No shipment suggestions found", null, 422);
            }

            // Select suggestion index if provided, fallback to first
            $index = $validated['selected_suggestion_index'] ?? 0;
            $selected = $suggestions['results'][$index] ?? null;

            if (!$selected) {
                return responseJson(false, "Invalid suggestion index", null, 422);
            }

            $savedPackages = [];

            // Delete old items and their related records
            foreach ($oldItems as $item) {
                if ($item->route) {
                    $item->route->delete();
                }
                if ($item->package) {
                    // Don't delete the package itself as it might be referenced elsewhere
                    // Just remove the cart item reference
                }
                $item->delete();
            }

            // Process each new package - similar to add method
            foreach ($validated['packages'] as $packageIndex => $packageData) {
                // Prepare package data with addresses and details
                $packageData['pickup_address'] = $pickup;
                $packageData['dropoff_address'] = $dropoff;
                $packageData['details'] = $details;

                // Find the package price from the selected suggestion
                $packagePrice = null;
                $packageLegA = null;
                $packageLegB = null;

                if ($selected['type'] === 'direct') {
                    // Find price for this specific package
                    foreach ($selected['prices'] as $priceData) {
                        if ($priceData['package_id'] === $packageIndex) {
                            $packagePrice = $priceData['price'];
                            break;
                        }
                    }
                    $totalPrice = $selected['total'];
                } else { // split
                    // Find prices for this specific package in both legs
                    foreach ($selected['legA_prices'] as $legAPriceData) {
                        if ($legAPriceData['package_id'] === $packageIndex) {
                            $packageLegA = $legAPriceData['price'];
                            break;
                        }
                    }
                    foreach ($selected['legB_prices'] as $legBPriceData) {
                        if ($legBPriceData['package_id'] === $packageIndex) {
                            $packageLegB = $legBPriceData['price'];
                            break;
                        }
                    }
                    $totalPrice = $selected['total'];
                }

                if (!$packagePrice && !($packageLegA && $packageLegB)) {
                    // Clean up: delete any packages we might have created
                    foreach ($savedPackages as $cartItem) {
                        if ($cartItem->package) {
                            $cartItem->package->delete();
                        }
                        $cartItem->delete();
                    }
                    return responseJson(false, "Could not find price for package #" . ($packageIndex + 1), null, 422);
                }

                // Determine shipment company IDs
                if ($selected['type'] === 'direct') {
                    $chosenShipmentCompanyId = $selected['company']['id'];
                    $legs = [$packagePrice];
                    $handoff = null;
                } else { // split
                    $chosenShipmentCompanyId = null;
                    $legs = [$packageLegA, $packageLegB];
                    $handoff = $selected['handoff_point'];
                }

                // Create new package (or update existing if ID provided)
                $packageData['preferred_company_id'] = $chosenShipmentCompanyId;

                // Check if this is updating an existing package
                $existingPackageId = $packageData['id'] ?? null;
                if ($existingPackageId && isset($packageData['id'])) {
                    // Update existing package
                    $package = Package::find($existingPackageId);
                    if ($package) {
                        // Update package attributes except id
                        $updateData = collect($packageData)->except(['id', 'pickup_address', 'dropoff_address', 'details'])->toArray();
                        $package->update($updateData);

                        // Update addresses if provided in package data
                        if (isset($packageData['pickup_address'])) {
                            $package->pickupAddress()->updateOrCreate(
                                ['package_id' => $package->id],
                                $packageData['pickup_address']
                            );
                        }
                        if (isset($packageData['dropoff_address'])) {
                            $package->dropoffAddress()->updateOrCreate(
                                ['package_id' => $package->id],
                                $packageData['dropoff_address']
                            );
                        }
                        if (isset($packageData['details'])) {
                            $package->packageDetails()->updateOrCreate(
                                ['package_id' => $package->id],
                                $packageData['details']
                            );
                        }
                    } else {
                        // Package not found, create new
                        $package = PackageService::createFromPayload($packageData, $request);
                    }
                } else {
                    // Create new package
                    $package = PackageService::createFromPayload($packageData, $request);
                }

                // Create cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'package_id' => $package->id,
                    'shipment_company_id' => $chosenShipmentCompanyId,
                    'est_date' => $validated['est_date'] ?? null,
                    'est_price' => $packagePrice['client_total'] ?? ($packageLegA['total'] + $packageLegB['total']),
                    'requires_split' => $selected['type'] === 'split',
                    'package_index' => $packageIndex,
                ]);

                // Create route
                CartItemRoute::create([
                    'cart_item_id' => $cartItem->id,
                    'pickup_company_id' => $selected['type'] === 'split' ? $selected['pickup_company']['id'] : $chosenShipmentCompanyId,
                    'dropoff_company_id' => $selected['type'] === 'split' ? $selected['dropoff_company']['id'] : $chosenShipmentCompanyId,
                    'pickup_address' => $pickup,
                    'dropoff_address' => $dropoff,
                    'is_split' => $selected['type'] === 'split',
                    'legs' => $legs,
                    'total_cost' => $packagePrice['client_total'] ?? ($packageLegA['total'] + $packageLegB['total']),
                    'handoff_point' => $handoff,
                    'suggestion_data' => [
                        'suggestion_type' => $selected['type'],
                        'suggestion_index' => $index,
                        'package_index' => $packageIndex,
                        'total_shipment_price' => $totalPrice,
                    ]
                ]);

                $savedPackages[] = $cartItem;
            }

        } else {
            // No packages provided in request - update existing items with new addresses/details
            // This handles the case where user only wants to update addresses or details

            $firstItem = $cart->items->first();
            if ($firstItem) {
                $existingPickup = $firstItem->package->pickupAddress->toArray();
                $existingDropoff = $firstItem->package->dropoffAddress->toArray();
                $existingDetails = $firstItem->package->packageDetails->toArray();
            }

            // Prepare updated addresses
            $pickup = isset($validated['pickup_address'])
                ? array_merge($existingPickup ?? [], $validated['pickup_address'])
                : $existingPickup;

            $dropoff = isset($validated['dropoff_address'])
                ? array_merge($existingDropoff ?? [], $validated['dropoff_address'])
                : $existingDropoff;

            // Prepare updated details
            $details = isset($validated['details'])
                ? array_merge($existingDetails ?? [], $validated['details'])
                : $existingDetails;

            // Update addresses and details for all packages in cart
            foreach ($cart->items as $item) {
                if ($item->package) {
                    if (isset($validated['pickup_address']) && $item->package->pickupAddress) {
                        $item->package->pickupAddress->update($validated['pickup_address']);
                    }
                    if (isset($validated['dropoff_address']) && $item->package->dropoffAddress) {
                        $item->package->dropoffAddress->update($validated['dropoff_address']);
                    }
                    if (isset($validated['details']) && $item->package->packageDetails) {
                        $item->package->packageDetails->update($validated['details']);
                    }
                }
            }

            // If addresses changed, we need to recalculate suggestions
            if (isset($validated['pickup_address']) || isset($validated['dropoff_address'])) {
                // Delete existing routes
                foreach ($cart->items as $item) {
                    if ($item->route) {
                        $item->route->delete();
                    }
                }

                // Determine if pickup/dropoff are villages
                $isPickupVillage = $pickup['is_village'] ?? false;
                $isDropoffVillage = $dropoff['is_village'] ?? false;

                // Prepare packages for suggestion from existing items
                $packagesForSuggestion = [];
                foreach ($cart->items as $index => $item) {
                    $package = $item->package;
                    $packagesForSuggestion[] = [
                        'id' => $index,
                        'category_id' => $package->category_id,
                        'sub_category_id' => $package->sub_category_id,
                        'size_id' => $package->size_id,
                        'weight' => $package->weight
                    ];
                }

                // Prepare suggestion payload
                $suggestionPayload = [
                    'preferred_company_id' => $validated['preferred_company_id'] ?? null,
                    'packages' => $packagesForSuggestion,
                    'pickup_address' => [
                        'zone_id' => $pickup['zone_id'],
                        'longitude' => (float) $pickup['longitude'],
                        'latitude' => (float) $pickup['latitude'],
                        'city_id' => $pickup['city_id'],
                        'state_id' => $pickup['state_id'],
                        'is_village' => $isPickupVillage
                    ],
                    'dropoff_address' => [
                        'zone_id' => $dropoff['zone_id'],
                        'longitude' => (float) $dropoff['longitude'],
                        'latitude' => (float) $dropoff['latitude'],
                        'city_id' => $dropoff['city_id'],
                        'state_id' => $dropoff['state_id'],
                        'is_village' => $isDropoffVillage
                    ]
                ];

                // Get new suggestions
                $suggestions = app(\App\Services\ShipmentSuggestionService::class)
                    ->getSuggestions($suggestionPayload);

                if (!$suggestions['success']) {
                    return responseJson(false, $suggestions['message'] ?? "No shipment suggestions found", null, 422);
                }

                // Select suggestion index if provided, fallback to first or find best match
                $index = $validated['selected_suggestion_index'] ?? 0;
                $selected = $suggestions['results'][$index] ?? null;

                if (!$selected) {
                    return responseJson(false, "Invalid suggestion index", null, 422);
                }

                // Process each cart item with new suggestion
                foreach ($cart->items as $itemIndex => $item) {
                    $package = $item->package;

                    // Find the package price from the selected suggestion
                    $packagePrice = null;
                    $packageLegA = null;
                    $packageLegB = null;

                    if ($selected['type'] === 'direct') {
                        foreach ($selected['prices'] as $priceData) {
                            if ($priceData['package_id'] === $itemIndex) {
                                $packagePrice = $priceData['price'];
                                break;
                            }
                        }
                        $totalPrice = $selected['total'];
                    } else {
                        foreach ($selected['legA_prices'] as $legAPriceData) {
                            if ($legAPriceData['package_id'] === $itemIndex) {
                                $packageLegA = $legAPriceData['price'];
                                break;
                            }
                        }
                        foreach ($selected['legB_prices'] as $legBPriceData) {
                            if ($legBPriceData['package_id'] === $itemIndex) {
                                $packageLegB = $legBPriceData['price'];
                                break;
                            }
                        }
                        $totalPrice = $selected['total'];
                    }

                    if (!$packagePrice && !($packageLegA && $packageLegB)) {
                        return responseJson(false, "Could not find price for package #" . ($itemIndex + 1), null, 422);
                    }

                    // Determine shipment company IDs
                    if ($selected['type'] === 'direct') {
                        $chosenShipmentCompanyId = $selected['company']['id'];
                        $legs = [$packagePrice];
                        $handoff = null;
                        $isSplit = false;
                        $pickupCompanyId = $chosenShipmentCompanyId;
                        $dropoffCompanyId = $chosenShipmentCompanyId;
                    } else {
                        $chosenShipmentCompanyId = null;
                        $legs = [$packageLegA, $packageLegB];
                        $handoff = $selected['handoff_point'];
                        $isSplit = true;
                        $pickupCompanyId = $selected['pickup_company']['id'];
                        $dropoffCompanyId = $selected['dropoff_company']['id'];
                    }

                    // Update cart item
                    $item->update([
                        'shipment_company_id' => $chosenShipmentCompanyId,
                        'est_date' => $validated['est_date'] ?? $item->est_date,
                        'est_price' => $totalPrice,
                        'requires_split' => $isSplit,
                    ]);

                    // Create new route
                    CartItemRoute::create([
                        'cart_item_id' => $item->id,
                        'pickup_company_id' => $pickupCompanyId,
                        'dropoff_company_id' => $dropoffCompanyId,
                        'pickup_address' => $pickup,
                        'dropoff_address' => $dropoff,
                        'is_split' => $isSplit,
                        'legs' => $legs,
                        'total_cost' => $totalPrice,
                        'handoff_point' => $handoff,
                        'suggestion_data' => [
                            'suggestion_type' => $selected['type'],
                            'suggestion_index' => $index,
                            'package_index' => $itemIndex,
                            'total_shipment_price' => $totalPrice,
                        ]
                    ]);
                }
            }
        }

        // Update cart totals
        $this->updateCartTotals($cart);

        // Reload cart with updated relationships
        $cart->refresh();

        return responseJson(true, 'Cart updated successfully', [
            "cart" => $cart->load(['items.package.packageDetails', 'items.package.pickupAddress', 'items.package.dropoffAddress', 'items.route']),
            "selected_suggestion" => isset($selected) ? [
                'type' => $selected['type'],
                'index' => $index,
                'total_price' => $selected['total'],
            ] : null
        ]);
    }

    /**
     * Check if we need to recalculate shipment suggestions
     */
    private function shouldRecalculateShipment(CartItem $item, array $validated): bool
    {
        $package = $item->package;

        // Always recalculate if shipment company changes
        if (isset($validated['preferred_company_id']) &&
            $validated['preferred_company_id'] != $item->shipment_company_id) {
            return true;
        }

        // Recalculate if package details that affect pricing change
        if (!empty($validated['packages'])) {
            $pkgData = collect($validated['packages'])->firstWhere('id', $package->id);
            if ($pkgData) {
                // Check if any pricing-related fields changed
                $pricingFields = ['weight', 'category_id', 'sub_category_id', 'size_id'];
                foreach ($pricingFields as $field) {
                    if (isset($pkgData[$field]) && $pkgData[$field] != $package->{$field}) {
                        return true;
                    }
                }
            }
        }

        // Recalculate if addresses change (affects distance)
        if (isset($validated['pickup_address']) || isset($validated['dropoff_address'])) {
            return true;
        }

        return false;
    }

    /**
     * Recalculate shipment suggestions for a cart item
     */
    private function recalculateShipmentForItem(CartItem $item, array $validated): array
    {
        $package = $item->package;

        // Get updated package data
        $packageData = [
            'category_id' => $package->category_id,
            'sub_category_id' => $package->sub_category_id,
            'size_id' => $package->size_id,
            'weight' => $package->weight,
        ];

        // Override with any updates from request
        if (!empty($validated['packages'])) {
            $pkgUpdate = collect($validated['packages'])->firstWhere('id', $package->id);
            if ($pkgUpdate) {
                foreach (['category_id', 'sub_category_id', 'size_id', 'weight'] as $field) {
                    if (isset($pkgUpdate[$field])) {
                        $packageData[$field] = $pkgUpdate[$field];
                    }
                }
            }
        }

        // Get addresses
        $pickup = $validated['pickup_address'] ?? $package->pickupAddress->toArray();
        $dropoff = $validated['dropoff_address'] ?? $package->dropoffAddress->toArray();

        // Add is_village (you might want to get this from a database field)
        $pickup['is_village'] = $pickup['is_village'] ?? 1;
        $dropoff['is_village'] = $dropoff['is_village'] ?? 1;

        // Fetch suggestions from service
        $suggestionPayload = [
            'preferred_company_id' => $validated['preferred_company_id'] ?? $item->shipment_company_id,
            'category_id' => $packageData['category_id'],
            'sub_category_id' => $packageData['sub_category_id'],
            'size_id' => $packageData['size_id'],
            'weight' => $packageData['weight'],
            'pickup' => $pickup,
            'dropoff' => $dropoff,
        ];

        $suggestions = app(\App\Services\ShipmentSuggestionService::class)
                            ->getSuggestions($suggestionPayload);

        if (!$suggestions['success']) {
            return [
                'success' => false,
                'message' => "No shipment suggestions found for updated data"
            ];
        }

        // Select suggestion - use existing if still valid, otherwise first
        $selectedIndex = $this->findBestSuggestionIndex($item, $suggestions['results']);
        $selected = $suggestions['results'][$selectedIndex] ?? $suggestions['results'][0] ?? null;

        if (!$selected) {
            return [
                'success' => false,
                'message' => "Invalid suggestion after recalculation"
            ];
        }

        return [
            'success' => true,
            'selected' => $selected,
            'selected_index' => $selectedIndex
        ];
    }

    /**
     * Find the best suggestion index based on existing item
     */
    private function findBestSuggestionIndex(CartItem $item, array $suggestions): int
    {
        $currentRoute = $item->route;

        if (!$currentRoute || empty($suggestions)) {
            return 0;
        }

        // Try to find matching suggestion based on current setup
        foreach ($suggestions as $index => $suggestion) {
            if ($currentRoute->is_split && $suggestion['type'] === 'split') {
                // Check if split companies match
                if ($currentRoute->pickup_company_id == $suggestion['pickup_company']['id'] &&
                    $currentRoute->dropoff_company_id == $suggestion['dropoff_company']['id']) {
                    return $index;
                }
            } elseif (!$currentRoute->is_split && $suggestion['type'] === 'direct') {
                // Check if direct company matches
                if ($currentRoute->pickup_company_id == $suggestion['company']['id']) {
                    return $index;
                }
            }
        }

        // No match found, return first suggestion
        return 0;
    }

    protected function addWithoutCompany(Cart $cart, int $packageId, array $validated)
    {
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'package_id' => $packageId,
            'shipment_company_id' => null,
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $validated['est_price'] ?? 0,
            'requires_split' => false,
        ]);
        CartItemRoute::create([
            'cart_item_id' => $cartItem->id,
            'pickup_company_id' => null,
            'dropoff_company_id' => null,
            'pickup_address' => $validated['pickup_address'],
            'dropoff_address' => $validated['dropoff_address'],
            'legs' => [],
            'total_cost' => $validated['est_price'] ?? 0,
            'is_split' => false,

        ]);


        $this->updateCartTotals($cart);

        return responseJson(true, 'Added to cart', $cartItem);
    }

    protected function updateCartTotals(Cart $cart): void
    {
        $cart->update([
            'items_count' => $cart->items()->count(),
            'item_total_price' => $cart->items()->sum('est_price'),
        ]);
    }

    public function remove(Request $request, string $cartid, string $id)
    {
        try {
            $item = CartItem::where('id', $id)->whereHas('cart', fn($q) => $q->where('user_id', auth()->id())->where('id', $cartid))->firstOrFail();
            $item->delete();
            $item->cart->update([
                'items_count' => $item->cart->items()->count(),
                'item_total_price' => $item->cart->items()->sum('est_price'),
            ]);
            if ($item->cart->items()->count() == 0) {
                $item->cart->delete();
            }
            return responseJson(true, 'Removed from cart');
        } catch (\Throwable $th) {
            return responseJson(false, 'Failed to remove from cart', $th->getMessage());
        }
    }
}
