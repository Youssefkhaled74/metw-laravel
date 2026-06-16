<?php

use App\Http\Controllers\Api\V1\CancelReasonController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Ecommerce\BannarController;
use App\Http\Controllers\Api\V1\Ecommerce\Order\WalletController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\CategoryController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\ColorController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\ProductController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\ProductSizeController;
use App\Http\Controllers\Api\V1\Shipment\FavouriteController;
use App\Http\Controllers\Api\V1\Shipment\ConsignmentTypeController;
use App\Http\Controllers\Api\V1\Shipment\DeliveryTypeController;
use App\Http\Controllers\Api\V1\Shipment\Order\CartController;
use App\Http\Controllers\Api\V1\Shipment\Order\CheckoutController;
use App\Http\Controllers\Api\V1\Shipment\Order\OrderController;
use App\Http\Controllers\Api\V1\Shipment\PackageTypeController;
use App\Http\Controllers\Api\V1\Shipment\ShipmentCompanyController;
use App\Http\Controllers\Api\V1\Shipment\SizeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Ecommerce\VendorController;
use App\Http\Controllers\Api\V1\Ecommerce\CityController;
use App\Http\Controllers\Api\V1\Ecommerce\Order\EcommerceCartController;
use App\Http\Controllers\Api\V1\Ecommerce\User\UserAddressController;
use App\Http\Controllers\Api\V1\Ecommerce\Order\EcommerceOrderController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\FavouriteController as ProductFavouriteController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\ProductReviewController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\Ecommerce\Order\ReturnRequestController;
use App\Http\Controllers\Api\V1\Ecommerce\Product\BrandController;
use App\Http\Controllers\Api\V1\Ecommerce\HomeController;
use App\Http\Controllers\Api\V1\Shipment\Order\PackageAddressController;
use App\Http\Controllers\Api\V1\Shipment\Order\SavedAddressController;
use App\Http\Controllers\Api\V1\Shipment\Order\ShipmentCoverageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth:sanctum');
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:sanctum');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->withoutMiddleware('auth:sanctum');
        Route::post('resend-otp', [AuthController::class, 'resendOtp'])->withoutMiddleware('auth:sanctum');
        Route::post('forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp'])->withoutMiddleware('auth:sanctum');
        Route::post('forgot-password/resend-otp', [AuthController::class, 'resendForgotPasswordOtp'])->withoutMiddleware('auth:sanctum');
        Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyForgotPasswordOtp'])->withoutMiddleware('auth:sanctum');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->withoutMiddleware('auth:sanctum');
        Route::post('email-verification/send-otp', [AuthController::class, 'sendEmailVerificationOtp']);
        Route::post('email-verification/resend-otp', [AuthController::class, 'resendEmailVerificationOtp']);
        Route::post('email-verification/verify-otp', [AuthController::class, 'verifyEmailVerificationOtp']);
        Route::post('fcm-token', [AuthController::class, 'storeFcmToken']);
        Route::delete('fcm-token', [AuthController::class, 'removeFcmToken']);
        Route::delete('logout', [AuthController::class, 'logout']);
    });
    Route::prefix('user')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::put('update/profile', [UserController::class, 'update']);
        Route::post('delete-account', [UserController::class, 'destroy']);
        Route::patch('notifications', [UserController::class, 'toggleNotifications']);
        Route::patch('shipment/notifications', [UserController::class, 'toggleNotificationsshipment']);
        Route::get('notifications', [UserController::class, 'getNotifications']);
        Route::post('notifications/mark-as-read', [UserController::class, 'markAsRead']);
        Route::post('set-default-lang', [UserController::class, 'setDefaultLang']);
    });
    Route::prefix('shipment-companies')->group(function () {
        Route::get('/', [ShipmentCompanyController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [ShipmentCompanyController::class, 'store']);
        Route::get('/{id}', [ShipmentCompanyController::class, 'show'])->withoutMiddleware('auth:sanctum');
        Route::put('/{id}', [ShipmentCompanyController::class, 'update']);
        Route::delete('/{id}', [ShipmentCompanyController::class, 'destroy']);
    });
    Route::prefix('package-types')->group(function () {
        Route::get('/', [PackageTypeController::class, 'index']);
        Route::post('/', [PackageTypeController::class, 'store']);
        Route::get('/{id}', [PackageTypeController::class, 'show']);
        Route::put('/{id}', [PackageTypeController::class, 'update']);
        Route::delete('/{id}', [PackageTypeController::class, 'destroy']);
    });
    Route::prefix('consignment-types')->group(function () {
        Route::get('/', [ConsignmentTypeController::class, 'index']);
        Route::post('/', [ConsignmentTypeController::class, 'store']);
        Route::get('/{id}', [ConsignmentTypeController::class, 'show']);
        Route::put('/{id}', [ConsignmentTypeController::class, 'update']);
        Route::delete('/{id}', [ConsignmentTypeController::class, 'destroy']);
    });
    Route::prefix('delivery-types')->group(function () {
        Route::get('/', [DeliveryTypeController::class, 'index']);
        Route::post('/', [DeliveryTypeController::class, 'store']);
        Route::get('/{id}', [DeliveryTypeController::class, 'show']);
        Route::put('/{id}', [DeliveryTypeController::class, 'update']);
        Route::delete('/{id}', [DeliveryTypeController::class, 'destroy']);
    });
    Route::prefix('sizes')->group(function () {
        Route::get('/', [SizeController::class, 'index']);
        Route::post('/', [SizeController::class, 'store']);
        Route::get('/{id}', [SizeController::class, 'show']);
        Route::put('/{id}', [SizeController::class, 'update']);
        Route::delete('/{id}', [SizeController::class, 'destroy']);
    });
    Route::get('cancel-reasons', [CancelReasonController::class, 'index']);

    // Removed standalone package creation; package is created during add-to-cart or direct checkout

    // التحقق الشامل من التغطية مع كل الاقتراحات
    Route::post('shipment/check-coverage', [ShipmentCoverageController::class, 'checkCoverage']);

    // الحصول على أفضل اقتراح فقط
    Route::post('shipment/best-suggestion', [ShipmentCoverageController::class, 'getBestSuggestion']);
    Route::post('shipment/suggestions', [ShipmentCoverageController::class, 'getSuggestions']);

    // الحصول على نقطة الـ Handoff فقط
    Route::post('shipment/handoff-point', [ShipmentCoverageController::class, 'getHandoffPoint']);

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('', [CartController::class, 'index']);
        Route::get('items/{cartid}', [CartController::class, 'getItems']);
        Route::get('item/{id}', [CartController::class, 'getItem']);
        Route::post('add-items', [CartController::class, 'add']);
        Route::prefix('saved-addresses')->group(function () {
            Route::get('/', [SavedAddressController::class, 'index']);
            Route::delete('/{id}', [SavedAddressController::class, 'destroy']);

        });
        Route::put('{cartId}/update-all', [CartController::class, 'updateCart']);
        Route::delete('{cartid}/remove-items/{id}', [CartController::class, 'remove']);
    });
    Route::prefix('addresses')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [PackageAddressController::class, 'index']);
        Route::get('/{id}', [PackageAddressController::class, 'show']);
    });
    // Order Creation
    Route::get('checkout/preview/{orderId}', [CheckoutController::class, 'preview']);
    // Route::post('orders', [CheckoutController::class, 'create']); // Create order from cart
    Route::post('orders', [CheckoutController::class, 'checkoutSelectedItems']);

    Route::post('orders/direct', [CheckoutController::class, 'createDirect']); // Create order directly
    Route::post('checkout/apply-promo', [CheckoutController::class, 'applyPromo']);

    // Direct checkout preview
    Route::post('checkout/direct/preview', [CheckoutController::class, 'directPreview']);

    // Checkout (Payment Processing)
    Route::post('orders/{orderId}/checkout', [CheckoutController::class, 'checkout']);

    Route::get('orders', [OrderController::class, 'getOrders']);
    Route::post('orders/cancel/{id}', [OrderController::class, 'cancelOrder']);
    Route::post('order-item/{orderItemId}/update-status', [OrderController::class, 'updateOrderItemStatus']);
    Route::get('order-item/{orderItemId}', [OrderController::class, 'getOrderItem']);
    Route::post('order-item/{orderId}/review', [OrderController::class, 'reviewOrderItem']);
    Route::get('package/tracking', [OrderController::class, 'getTracking']);
    Route::get('package/{id}', [OrderController::class, 'getPackage']);

    Route::prefix('favourites')->group(function () {
        Route::get('/', [FavouriteController::class, 'index']);
        Route::post('/toggle/{shipmentCompanyId}', [FavouriteController::class, 'toggle']);
        Route::post('/add/{shipmentCompanyId}', [FavouriteController::class, 'addfavourite']);
        Route::post('/remove/{shipmentCompanyId}', [FavouriteController::class, 'removefavourite']);
    });
    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [PageController::class, 'store']);
        Route::get('/{id}', [PageController::class, 'show'])->withoutMiddleware('auth:sanctum');
        Route::put('/{id}', [PageController::class, 'update']);
        Route::delete('/{id}', [PageController::class, 'destroy']);
    });
});


Route::prefix('v1/ecommerce')->middleware('auth:sanctum')->group(function () {

    //homw
    Route::get('/home', [HomeController::class, 'index'])->withoutMiddleware('auth:sanctum');
    Route::get('products/price-range', [HomeController::class, 'priceRange'])->withoutMiddleware('auth:sanctum');
    Route::get('products/search', [HomeController::class, 'search'])->withoutMiddleware('auth:sanctum');


    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [VendorController::class, 'store']);
        Route::get('/{id}', [VendorController::class, 'show'])->withoutMiddleware('auth:sanctum');
        Route::put('/{id}', [VendorController::class, 'update']);
        Route::delete('/{id}', [VendorController::class, 'destroy']);
    });
    Route::get('/main-categories', [CategoryController::class, 'mainCategories'])->withoutMiddleware('auth:sanctum');

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::get('/home', [CategoryController::class, 'homeCategories'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show'])->withoutMiddleware('auth:sanctum');
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::get('/{mainCategoryId}/sub-categories', [CategoryController::class, 'subCategories'])->withoutMiddleware('auth:sanctum');
    });
    Route::get('/brand/{id}/products', [BrandController::class, 'brandProducts'])->withoutMiddleware('auth:sanctum');

    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::get('/{id}', [BrandController::class, 'show'])->withoutMiddleware('auth:sanctum');
    });

    Route::prefix('cities')->group(function () {
        Route::get('/', [CityController::class, 'index']);
        Route::post('/', [CityController::class, 'store']);
        Route::get('/{id}', [CityController::class, 'show']);
        Route::put('/{id}', [CityController::class, 'update']);
        Route::delete('/{id}', [CityController::class, 'destroy']);
    });
    Route::prefix('countries')->group(function () {
        Route::get('/', [UserAddressController::class, 'country'])->withoutMiddleware('auth:sanctum');
    });
    Route::get('states', [UserAddressController::class, 'state']);
    Route::get('states/{stateId}/cities', [UserAddressController::class, 'city'])->whereNumber('stateId');
    Route::get('cities/{cityId}/zones', [UserAddressController::class, 'zone'])->whereNumber('cityId');

    Route::prefix('user-addresses')->group(function () {
        Route::get('/', [UserAddressController::class, 'index']);
        Route::post('/', [UserAddressController::class, 'store']);
        Route::get('/{id}', [UserAddressController::class, 'show']);
        Route::put('/{id}', [UserAddressController::class, 'update']);
        Route::delete('/{id}', [UserAddressController::class, 'destroy']);
        Route::post('{userAddressId}/is-default', [UserAddressController::class, 'setDefault']);
    });

    Route::prefix('bannars')->group(function () {
        Route::get('/', [BannarController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [BannarController::class, 'store']);
        Route::get('/{id}', [BannarController::class, 'show']);
        Route::put('/{id}', [BannarController::class, 'update']);
        Route::delete('/{id}', [BannarController::class, 'destroy']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::get('/{slug}', [ProductController::class, 'show'])->withoutMiddleware('auth:sanctum');
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);


    });

    Route::prefix('colors')->group(function () {
        Route::get('/', [ColorController::class, 'index']);
        Route::post('/', [ColorController::class, 'store']);
        Route::get('/{id}', [ColorController::class, 'show']);
        Route::put('/{id}', [ColorController::class, 'update']);
        Route::delete('/{id}', [ColorController::class, 'destroy']);
    });

    Route::prefix('sizes')->group(function () {
        Route::get('/', [ProductSizeController::class, 'index']);
        Route::post('/', [ProductSizeController::class, 'store']);
        Route::get('/{id}', [ProductSizeController::class, 'show']);
        Route::put('/{id}', [ProductSizeController::class, 'update']);
        Route::delete('/{id}', [ProductSizeController::class, 'destroy']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('', [EcommerceCartController::class, 'index']);
        Route::get('item/{cartItemId}', [EcommerceCartController::class, 'show']);
        Route::post('add-items', [EcommerceCartController::class, 'store']);
        Route::put('update-items/{cartItemId}', [EcommerceCartController::class, 'update']);
        Route::delete('remove-items/{cartItemId}', [EcommerceCartController::class, 'destroy']);
    });
    Route::prefix('orders')->group(function () {
        Route::get('', [EcommerceOrderController::class, 'index']);
        Route::post('create', [EcommerceOrderController::class, 'store']);
        Route::post('checkout/{orderId}', [EcommerceOrderController::class, 'checkout']);
        Route::get('/{orderId}', [EcommerceOrderController::class, 'show']);
        Route::get('/{orderId}/items', [EcommerceOrderController::class, 'showItems']);
        Route::put('/{orderId}', [EcommerceOrderController::class, 'update']);
        Route::delete('/{orderId}', [EcommerceOrderController::class, 'destroy']);
        Route::post('preview', [EcommerceOrderController::class, 'preview']);
        Route::post('apply-promo', [EcommerceOrderController::class, 'applyPromoCode']);
        Route::post('{orderId}/cancelled', [EcommerceOrderController::class, 'cancelOrder']);
        Route::post('items/{orderItemId}/cancel', [EcommerceOrderController::class, 'cancelOrderItem']);
    });
    Route::prefix('favourites')->group(function () {
        Route::get('/', [ProductFavouriteController::class, 'index']);
        Route::post('/add/{product_id}', [ProductFavouriteController::class, 'store']);
        Route::post('/remove/{product_id}', [ProductFavouriteController::class, 'destroy']);
    });
    Route::prefix('reviews')->group(function () {
        Route::get('products/{productId}', [ProductReviewController::class, 'index']);
        Route::post('/', [ProductReviewController::class, 'store']);
        Route::get('/{id}', [ProductReviewController::class, 'show']);
        Route::put('/{id}', [ProductReviewController::class, 'update']);
        Route::delete('/{id}', [ProductReviewController::class, 'destroy']);
    });

    // Return Requests
    Route::prefix('return-requests')->group(function () {
        Route::get('/', [ReturnRequestController::class, 'index']);
        Route::post('/', [ReturnRequestController::class, 'store']);
        Route::get('/order/{orderId}', [ReturnRequestController::class, 'getOrderForReturn']);
        Route::get('/{id}', [ReturnRequestController::class, 'show']);
        Route::put('/{id}', [ReturnRequestController::class, 'update']);
        Route::post('/{id}/cancel', [ReturnRequestController::class, 'cancel']);
    });
    Route::post('/return-cash-back', [ReturnRequestController::class, 'cashBack']);

    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });


    //notifications
    Route::prefix('notifications')->group(function () {
        Route::get('', [UserController::class, 'getNotificationsecommerce']);
        Route::post('/mark-as-read', [UserController::class, 'markAsRead']);
    });
});
