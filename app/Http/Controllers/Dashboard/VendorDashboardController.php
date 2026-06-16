<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductMedia;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('vendor');
    }

    public function dashboard()
    {
        $vendor = Auth::user();

        $stats = [
            'total_products' => Product::where('vendor_id', $vendor->id)->count(),
            'active_products' => Product::where('vendor_id', $vendor->id)->where('is_active', true)->count(),
            'total_orders' => EcommerceOrderItem::whereHas('product', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->count(),
            'pending_orders' => EcommerceOrderItem::whereHas('product', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->whereHas('order', function ($query) {
                $query->where('status', 'pending');
            })->count(),
            'total_revenue' => EcommerceOrderItem::whereHas('product', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->sum('total_price'),
            'total_sold' => Product::where('vendor_id', $vendor->id)->sum('sold_count'),
        ];

        // Recent orders
        $recent_orders = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
            ->with(['user', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();

        // Top selling products
        $top_products = Product::where('vendor_id', $vendor->id)
            ->orderBy('sold_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.vendor.dashboard', compact(
            'stats',
            'recent_orders',
            'top_products'
        ));
    }

    // Products Management
    public function products()
    {
        $vendor = Auth::user();

        $products = Product::where('vendor_id', $vendor->id)
            ->with(['category', 'media'])
            ->latest()
            ->paginate(20);

        return view('dashboard.vendor.products', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        $sizes = ProductSize::all();
        $colors = ProductColor::all();

        return view('dashboard.vendor.create-product', compact('categories', 'sizes', 'colors'));
    }

    public function storeProduct(Request $request)
    {
        $vendor = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:product_sizes,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:product_colors,id',
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'price' => $request->price,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'is_active' => true,
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products', 'public');
                ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url' => $imagePath,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        // Handle sizes
        if ($request->sizes) {
            $product->sizes()->attach($request->sizes);
        }

        // Handle colors
        if ($request->colors) {
            $product->colors()->attach($request->colors);
        }

        return redirect()->route('vendor.products')->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $vendor = Auth::user();

        $product = Product::where('vendor_id', $vendor->id)
            ->with(['category', 'media', 'sizes', 'colors'])
            ->findOrFail($id);

        $categories = Category::all();
        $sizes = ProductSize::all();
        $colors = ProductColor::all();

        return view('dashboard.vendor.edit-product', compact('product', 'categories', 'sizes', 'colors'));
    }

    public function updateProduct(Request $request, $id)
    {
        $vendor = Auth::user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:product_sizes,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:product_colors,id',
        ]);

        $product->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'category_id' => $request->category_id,
            'price' => $request->price,
            'short_description' => $request->short_description,
            'description' => $request->description,
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url' => $imagePath,
                    'is_primary' => false,
                ]);
            }
        }

        // Update sizes
        if ($request->sizes) {
            $product->sizes()->sync($request->sizes);
        }

        // Update colors
        if ($request->colors) {
            $product->colors()->sync($request->colors);
        }

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully.');
    }

    public function deleteProductImage($productId, $imageId)
    {
        $vendor = Auth::user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);
        $image = ProductMedia::where('product_id', $product->id)->findOrFail($imageId);

        Storage::disk('public')->delete($image->url);
        $image->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    public function toggleProductStatus($id)
    {
        $vendor = Auth::user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Product {$status} successfully.");
    }

    // Orders Management
    public function orders()
    {
        $vendor = Auth::user();

        $orders = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(20);

        return view('dashboard.vendor.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $vendor = Auth::user();

        $order = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
            ->with(['user', 'userAddress', 'items.product'])
            ->findOrFail($id);

        // Get only items from this vendor
        $vendor_items = $order->items->filter(function ($item) use ($vendor) {
            return $item->product->vendor_id === $vendor->id;
        });

        return view('dashboard.vendor.order-details', compact('order', 'vendor_items'));
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $vendor = Auth::user();

        $request->validate([
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $order = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->findOrFail($orderId);

        $updateData = ['status' => $request->status];

        if ($request->tracking_number) {
            $updateData['tracking_number'] = $request->tracking_number;
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    // Profile Management
    public function profile()
    {
        $vendor = Auth::user();
        return view('dashboard.vendor.profile', compact('vendor'));
    }

    public function updateProfile(Request $request)
    {
        $vendor = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country_code' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $logoPath = $request->file('logo')->store('vendors/logos', 'public');
            $data['logo'] = $logoPath;
        }

        $vendor->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $vendor = Auth::user();

        if (!Hash::check($request->current_password, $vendor->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $vendor->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    // Reports
    public function reports()
    {
        $vendor = Auth::user();

        $reports = [
            'monthly_sales' => $this->getMonthlySales($vendor->id),
            'top_products' => $this->getTopProducts($vendor->id),
            'order_status_distribution' => $this->getOrderStatusDistribution($vendor->id),
            'revenue_by_month' => $this->getRevenueByMonth($vendor->id),
        ];

        return view('dashboard.vendor.reports', compact('reports'));
    }

    private function getMonthlySales($vendorId)
    {
        return EcommerceOrderItem::whereHas('product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function getTopProducts($vendorId)
    {
        return Product::where('vendor_id', $vendorId)
            ->with(['media'])
            ->orderBy('sold_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getOrderStatusDistribution($vendorId)
    {
        return EcommerceOrder::whereHas('items.product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    private function getRevenueByMonth($vendorId)
    {
        return EcommerceOrderItem::whereHas('product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as revenue')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }
}
