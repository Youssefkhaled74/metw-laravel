<?php

use App\Http\Controllers\Dashboard\Admin\CommissionController;
use App\Http\Controllers\Dashboard\Admin\NotificationController;
use App\Http\Controllers\Dashboard\Admin\AdminDashboardController;
use App\Http\Controllers\Dashboard\Admin\ShipmentRequestController;
use App\Http\Controllers\Dashboard\Admin\Settings\ConfigController;
use App\Http\Controllers\Dashboard\Admin\ShipmentController;
use App\Http\Controllers\Dashboard\Admin\RoleController;
use App\Http\Controllers\Dashboard\Admin\PermissionController;
use App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentCompanyNotificationController;
use App\Http\Controllers\Dashboard\Vendor\VendorBranchController;
use App\Http\Controllers\Dashboard\Vendor\VendorNotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Language switcher
Route::get('/lang/{locale}', function (Request $request, string $locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        $locale = config('app.locale', 'en');
    }
    $request->session()->put('locale', $locale);
    return back();
})->name('lang.switch');

// Admin Dashboard Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

    // Admin Dashboard Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Dashboard\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/monthly-revenue', [\App\Http\Controllers\Dashboard\Admin\AdminDashboardController::class, 'monthlyRevenue'])->name('dashboard.monthly-revenue');

        // Shipment Management
        Route::prefix('shipment')->name('shipment-')->group(function () {
            Route::get('/orders', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'showOrder'])->name('orders.show');
            Route::patch('/orders/{id}/status', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'updateOrderStatus'])->name('orders.update-status');
            Route::patch('/orders/{id}/assign', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'assignCompany'])->name('orders.assign-company');
            Route::patch('/orders/{orderId}/items/{itemId}/status', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'updateItemStatus'])->name('orders.update-item-status');

            // Phase 2 - Shipment Requests
            Route::get('/requests', [\App\Http\Controllers\Dashboard\Admin\ShipmentRequestController::class, 'index'])->name('requests.index');
            Route::get('/requests/{id}', [\App\Http\Controllers\Dashboard\Admin\ShipmentRequestController::class, 'show'])->name('requests.show');

            Route::get('/companies', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'companies'])->name('companies');
            Route::get('/companies/create', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'createCompany'])->name('companies.create');
            Route::post('/companies', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'storeCompany'])->name('companies.store');
            Route::get('/companies/{id}', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'showCompany'])->name('companies.show');
            Route::patch('/companies/{id}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'toggleCompanyStatus'])->name('companies.toggle-status');
        });

        Route::post(
            '/shipments/{shipmentCompany}/commission',
            [CommissionController::class, 'CustomShipmentStore']
        )->name('shipments.commission.store');

        Route::patch(
            '/shipments/{shipmentCompany}/commission',
            [CommissionController::class, 'CustomShipmentUpdate']
        )->name('shipments.commission.update');

        Route::get(
            'shipment-companies/{shipmentCompany}/export',
            [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'exportReport']
        )->name('shipment-companies.export');


        // Ecommerce Orders Management
        Route::prefix('ecommerce')->name('ecommerce-')->group(function () {
            Route::get('/orders', [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'showOrder'])->name('orders.show');
            Route::post('/orders/{order}/wallet', [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'addOrderToWallet'])
                ->name('orders.add-to-wallet');
            Route::patch('/orders/{id}/status', [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'updateOrderStatus'])->name('orders.update-status');
            Route::patch('/orders/{id}/assign-shipping', [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'assignShippingCompany'])->name('orders.assign-shipping');

                Route::post('/orders/{order}/add-payment', [\App\Http\Controllers\Dashboard\Admin\OrderPaymentController::class, 'addOrderPayment'])
                    ->name('orders.add-payment');
                Route::post('/orders/{order}/items/{item}/add-payment', [\App\Http\Controllers\Dashboard\Admin\OrderPaymentController::class, 'addItemPayment'])
                    ->name('orders.items.add-payment');
            Route::get('orders/{order}/whatsapp',
                [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'sendWhatsapp']
            )->name('orders.whatsapp');
        });


        Route::get('/custom-notifications', [\App\Http\Controllers\Dashboard\Admin\CustomNotificationController::class, 'index'])
            ->name('custom-notifications');

        Route::post('/custom-notifications', [\App\Http\Controllers\Dashboard\Admin\CustomNotificationController::class, 'send'])
            ->name('custom-notifications.send');

        Route::get('commissions', [CommissionController::class, 'index'])->name('commissions');
        Route::post('commissions/vendor', [CommissionController::class, 'storeVendor'])->name('commissions.vendor.store');
        Route::post('commissions/shipment', [CommissionController::class, 'storeShipment'])->name('commissions.shipment.store');
        Route::post('commissions/administrative', [CommissionController::class, 'storeAdministrative'])->name('commissions.administrative.store');

        Route::post(
            '/vendors/{vendor}/commission',
            [CommissionController::class, 'CustomVendorStore']
        )->name('vendors.commission.store');

        Route::patch('/vendors/{vendorId}/business-profile/approve', [\App\Http\Controllers\Dashboard\Admin\VendorBusinessProfileApprovalController::class, 'approve'])
            ->name('vendors.business-profile.approve');
        Route::patch('/vendors/{vendorId}/business-profile/reject', [\App\Http\Controllers\Dashboard\Admin\VendorBusinessProfileApprovalController::class, 'reject'])
            ->name('vendors.business-profile.reject');

        Route::patch(
            '/vendors/{vendor}/commission',
            [CommissionController::class, 'CustomVendorUpdate']
        )->name('vendors.commission.update');

        Route::get(
            'vendors/{vendor}/export',
            [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'exportReport']
        )->name('vendors.export');
        Route::get('/vendors/{vendor}/orders',
            [\App\Http\Controllers\Dashboard\Admin\EcommerceController::class, 'vendorOrders']
        )->name('vendors.orders');

        // Vendors Management
        Route::get('/vendors', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'index'])->name('vendors');
        Route::get('/vendors/create', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'create'])->name('vendors.create');
        Route::post('/vendors', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'store'])->name('vendors.store');
        Route::get('/vendors/{vendor}', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'show'])->name('vendors.show');
        Route::get('/vendors/{vendor}/edit', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'edit'])->name('vendors.edit');
        Route::patch('/vendors/{vendor}', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'update'])->name('vendors.update');
        Route::get('/vendors/{vendor}/products', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'products'])->name('vendors.products');
        Route::patch('/vendors/{vendor}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
        // Shipment Companies Management
        Route::get('/shipment-companies', [ShipmentController::class, 'companies'])->name('shipment-companies');
        Route::get('/shipment-companies/create', [ShipmentController::class, 'createCompany'])->name('shipment-companies.create');
        Route::post('/shipment-companies', [ShipmentController::class, 'storeCompany'])->name('shipment-companies.store');
        Route::get('/shipment-companies/{shipmentCompany}', [ShipmentController::class, 'showCompany'])->name('shipment-companies.show');
        Route::patch('/shipment-companies/{shipmentCompany}/toggle-status', [ShipmentController::class, 'toggleCompanyStatus'])->name('shipment-companies.toggle-status');

        // Users Management
        Route::get('/users', [\App\Http\Controllers\Dashboard\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/{id}', [\App\Http\Controllers\Dashboard\Admin\UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{id}/wallet/balance', [\App\Http\Controllers\Dashboard\Admin\UserController::class, 'updateWalletBalance'])
            ->name('users.wallet.balance.update');

        // Products Management
        Route::get('/products', [\App\Http\Controllers\Dashboard\Admin\ProductController::class, 'index'])->name('products');
        Route::get('/products/{product}', [\App\Http\Controllers\Dashboard\Admin\ProductController::class, 'show'])->name('products.show');
        Route::patch('/products/{id}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');

        //return Requests
        Route::get('/return-requests', [\App\Http\Controllers\Dashboard\Admin\ReturnRequestController::class, 'index'])->name('return-requests');
        Route::get('/return-requests/{id}', [\App\Http\Controllers\Dashboard\Admin\ReturnRequestController::class, 'showOrder'])->name('return-requests.show');
        Route::patch('/return-requests/{id}/status', [\App\Http\Controllers\Dashboard\Admin\ReturnRequestController::class, 'updateOrderStatus'])->name('return-requests.update-status');
        Route::patch('/return-requests/{id}/reason', [\App\Http\Controllers\Dashboard\Admin\ReturnRequestController::class, 'updateOrderReason'])->name('return-requests.update-reason');

        // Employee Management
        Route::get('/employees', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'edit'])->name('employees.edit');
        Route::patch('/employees/{employee}', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [\App\Http\Controllers\Dashboard\Admin\EmployeeController::class, 'destroy'])->name('employees.destroy');

        // Roles & Permissions Management (employee guard)
        Route::resource('roles', RoleController::class)->names('roles');
        Route::resource('permissions', PermissionController::class)->names('permissions');

        //Notifications
        Route::get('/notifications', [NotificationController::class, 'json'])->name('notifications');
        Route::get('/all-notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

        // Settings Management
        Route::prefix('settings')->name('settings.')->group(function () {
            // Banner Management
            Route::get('/banners', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'index'])->name('banners.index');
            Route::get('/banners/create', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'create'])->name('banners.create');
            Route::post('/banners', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'store'])->name('banners.store');
            Route::get('/banners/{banner}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'edit'])->name('banners.edit');
            Route::patch('/banners/{banner}', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'update'])->name('banners.update');
            Route::delete('/banners/{banner}', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'destroy'])->name('banners.destroy');
            Route::patch('/banners/{banner}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

            // Contact Admin Management
            Route::get('/contact-admins', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'index'])->name('contact-admins.index');
            Route::get('/contact-admins/create', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'create'])->name('contact-admins.create');
            Route::post('/contact-admins', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'store'])->name('contact-admins.store');
            Route::get('/contact-admins/{contactAdmin}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'edit'])->name('contact-admins.edit');
            Route::patch('/contact-admins/{contactAdmin}', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'update'])->name('contact-admins.update');
            Route::delete('/contact-admins/{contactAdmin}', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'destroy'])->name('contact-admins.destroy');
            Route::patch('/contact-admins/{contactAdmin}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\ContactAdminController::class, 'toggleStatus'])->name('contact-admins.toggle-status');

            // Banner Management
            Route::get('/brands', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'index'])->name('brands.index');
            Route::get('/brands/create', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'create'])->name('brands.create');
            Route::post('/brands', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'store'])->name('brands.store');
            Route::get('/brands/{brand}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'edit'])->name('brands.edit');
            Route::patch('/brands/{brand}', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'update'])->name('brands.update');
            Route::delete('/brands/{brand}', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'destroy'])->name('brands.destroy');
            Route::patch('/brands/{brand}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\BrandController::class, 'toggleStatus'])->name('brands.toggle-status');

            // Main Category Management
            Route::get('/main-categories', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'index'])->name('main-categories.index');
            Route::get('/main-categories/create', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'create'])->name('main-categories.create');
            Route::post('/main-categories', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'store'])->name('main-categories.store');
            Route::get('/main-categories/{mainCategory}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'edit'])->name('main-categories.edit');
            Route::patch('/main-categories/{mainCategory}', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'update'])->name('main-categories.update');
            Route::delete('/main-categories/{mainCategory}', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'destroy'])->name('main-categories.destroy');
            Route::patch('/main-categories/{mainCategory}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\MainCategoryController::class, 'toggleStatus'])->name('main-categories.toggle-status');


            // Category Management
            Route::get('/categories', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'edit'])->name('categories.edit');
            Route::patch('/categories/{category}', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::patch('/categories/{category}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

            Route::get('/promo_codes', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'index'])->name('promo_codes.index');
            Route::get('/promo_codes/create', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'create'])->name('promo_codes.create');
            Route::post('/promo_codes', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'store'])->name('promo_codes.store');
            Route::get('/promo_codes/{promo_code}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'edit'])->name('promo_codes.edit');
            Route::patch('/promo_codes/{promo_code}', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'update'])->name('promo_codes.update');
            Route::delete('/promo_codes/{promo_code}', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'destroy'])->name('promo_codes.destroy');
            Route::patch('/promo_codes/{promo_code}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\PromoCodeController::class, 'toggleStatus'])->name('promo_codes.toggle-status');

            Route::prefix('cities')->name('cities.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'store'])->name('store');
                Route::get('/{city}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'edit'])->name('edit');
                Route::patch('/{city}', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'update'])->name('update');
                Route::delete('/{city}', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'destroy'])->name('destroy');
                Route::patch('/{city}/toggle', [\App\Http\Controllers\Dashboard\Admin\Settings\CityController::class, 'toggle'])->name('toggle-status');
            });


            Route::prefix('countries')->name('countries.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'store'])->name('store');
                Route::get('/{country}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'edit'])->name('edit');
                Route::patch('/{country}', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'update'])->name('update');
                Route::delete('/{country}', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'destroy'])->name('destroy');
                Route::patch('/{country}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\CountryController::class, 'toggleStatus'])->name('toggle-status');
            });

            Route::prefix('cancel-reasons')->name('cancel-reasons.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'store'])->name('store');
                Route::get('/{cancel_reason}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'edit'])->name('edit');
                Route::patch('/{cancel_reason}', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'update'])->name('update');
                Route::delete('/{cancel_reason}', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'destroy'])->name('destroy');
                Route::patch('/{cancel_reason}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\CancelReasonsController::class, 'toggleStatus'])->name('toggle-status');
            });

            Route::prefix('states')->name('states.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'store'])->name('store');
                Route::get('/{state}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'edit'])->name('edit');
                Route::patch('/{state}', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'update'])->name('update');
                Route::delete('/{state}', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'destroy'])->name('destroy');
                Route::patch('/{state}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\StateController::class, 'toggle'])->name('toggle-status');
            });

            // routes/admin.php (أو اللي عندك بتسجل فيه routes بتاعت الـ admin)
            Route::prefix('zones')->name('zones.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'store'])->name('store');
                Route::get('/{zone}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'edit'])->name('edit');
                Route::patch('/{zone}', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'update'])->name('update');
                Route::delete('/{zone}', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'destroy'])->name('destroy');
                Route::patch('/{zone}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\ZoneController::class, 'toggle'])->name('toggle-status');
            });



            // Company Coverage Management
            Route::get('/company-coverages', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'index'])->name('company-coverages.index');
            Route::get('/company-coverages/create', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'create'])->name('company-coverages.create');
            Route::post('/company-coverages', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'store'])->name('company-coverages.store');
            Route::get('/company-coverages/{companyCoverage}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'edit'])->name('company-coverages.edit');
            Route::patch('/company-coverages/{companyCoverage}', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'update'])->name('company-coverages.update');
            Route::delete('/company-coverages/{companyCoverage}', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'destroy'])->name('company-coverages.destroy');
            Route::patch('/company-coverages/{companyCoverage}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\CompanyCoverageController::class, 'toggleStatus'])->name('company-coverages.toggle-status');

            // Consignment Type Management
            Route::get('/consignment-types', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'index'])->name('consignment-types.index');
            Route::get('/consignment-types/create', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'create'])->name('consignment-types.create');
            Route::post('/consignment-types', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'store'])->name('consignment-types.store');
            Route::get('/consignment-types/{consignmentType}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'edit'])->name('consignment-types.edit');
            Route::patch('/consignment-types/{consignmentType}', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'update'])->name('consignment-types.update');
            Route::delete('/consignment-types/{consignmentType}', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'destroy'])->name('consignment-types.destroy');
            Route::patch('/consignment-types/{consignmentType}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\ConsignmentTypeController::class, 'toggleStatus'])->name('consignment-types.toggle-status');

            // Delivery Type Management
            Route::get('/delivery-types', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'index'])->name('delivery-types.index');
            Route::get('/delivery-types/create', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'create'])->name('delivery-types.create');
            Route::post('/delivery-types', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'store'])->name('delivery-types.store');
            Route::get('/delivery-types/{deliveryType}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'edit'])->name('delivery-types.edit');
            Route::patch('/delivery-types/{deliveryType}', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'update'])->name('delivery-types.update');
            Route::delete('/delivery-types/{deliveryType}', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'destroy'])->name('delivery-types.destroy');
            Route::patch('/delivery-types/{deliveryType}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\DeliveryTypeController::class, 'toggleStatus'])->name('delivery-types.toggle-status');

            // Page Management
            Route::get('/pages', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'index'])->name('pages.index');
            Route::get('/pages/create', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'create'])->name('pages.create');
            Route::post('/pages', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'store'])->name('pages.store');
            Route::get('/pages/types/{type}/history', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'history'])->name('pages.history');
            Route::get('/pages/{page}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'edit'])->name('pages.edit');
            Route::patch('/pages/{page}', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'update'])->name('pages.update');
            Route::delete('/pages/{page}', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'destroy'])->name('pages.destroy');
            Route::patch('/pages/{page}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\PageController::class, 'toggleStatus'])->name('pages.toggle-status');

            // WhatsApp Templates Management
            Route::get('/whatsapp-templates', [\App\Http\Controllers\Dashboard\Admin\Settings\WhatsappTemplateController::class, 'index'])->name('whatsapp-templates.index');
            Route::patch('/whatsapp-templates', [\App\Http\Controllers\Dashboard\Admin\Settings\WhatsappTemplateController::class, 'update'])->name('whatsapp-templates.update');

            // Product Size Management
            Route::get('/product-sizes', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'index'])->name('product-sizes.index');
            Route::get('/product-sizes/create', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'create'])->name('product-sizes.create');
            Route::post('/product-sizes', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'store'])->name('product-sizes.store');
            Route::get('/product-sizes/{productSize}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'edit'])->name('product-sizes.edit');
            Route::patch('/product-sizes/{productSize}', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'update'])->name('product-sizes.update');
            Route::delete('/product-sizes/{productSize}', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'destroy'])->name('product-sizes.destroy');
            Route::patch('/product-sizes/{productSize}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductSizeController::class, 'toggleStatus'])->name('product-sizes.toggle-status');

            // Size Management
            Route::get('/sizes', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'index'])->name('sizes.index');
            Route::get('/sizes/create', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'create'])->name('sizes.create');
            Route::post('/sizes', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'store'])->name('sizes.store');
            Route::get('/sizes/{size}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'edit'])->name('sizes.edit');
            Route::patch('/sizes/{size}', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'update'])->name('sizes.update');
            Route::delete('/sizes/{size}', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'destroy'])->name('sizes.destroy');
            Route::patch('/sizes/{size}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\SizeController::class, 'toggleStatus'])->name('sizes.toggle-status');

            // Colors Management
            Route::get('/colors', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'index'])->name('colors.index');
            Route::get('/colors/create', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'create'])->name('colors.create');
            Route::post('/colors', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'store'])->name('colors.store');
            Route::get('/colors/{color}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'edit'])->name('colors.edit');
            Route::patch('/colors/{color}', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'update'])->name('colors.update');
            Route::delete('/colors/{color}', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'destroy'])->name('colors.destroy');
            Route::patch('/colors/{color}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\ProductColorController::class, 'toggleStatus'])->name('colors.toggle-status');


            // Price Per KM Settings Management
            Route::prefix('price-per-km')->name('price-per-km.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\SettingController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\SettingController::class, 'store'])->name('store');
                Route::patch('/{setting}', [\App\Http\Controllers\Dashboard\Admin\Settings\SettingController::class, 'update'])->name('update');
            });
            // routes/admin.php أو web.php داخل مجموعة admin
            Route::prefix('warehouses')->name('warehouses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'store'])->name('store');
                Route::get('/{warehouse}/edit', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'edit'])->name('edit');
                Route::patch('/{warehouse}', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'update'])->name('update');
                Route::patch('/{warehouse}/business-profile', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseBusinessProfileController::class, 'upsert'])->name('business-profile.upsert');
                Route::patch('/{warehouse}/business-profile/approve', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseBusinessProfileController::class, 'approve'])->name('business-profile.approve');
                Route::patch('/{warehouse}/business-profile/reject', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseBusinessProfileController::class, 'reject'])->name('business-profile.reject');
                Route::delete('/{warehouse}', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'destroy'])->name('destroy');
                Route::patch('/{warehouse}/toggle-status', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'toggleStatus'])->name('toggle-status');
            });
            Route::get('/get-states/{country}', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'getStates'])->name('get-states');
            Route::get('/get-cities/{state}', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'getCities'])->name('get-cities');
            Route::get('/get-zones/{city}', [\App\Http\Controllers\Dashboard\Admin\Settings\WarehouseController::class, 'getZones'])->name('get-zones');
        });

        // Shipment Company Price Management
        Route::prefix('shipment-companies')->name('shipment-companies.')->group(function () {
            Route::patch('/{shipmentCompany}/update-price-per-km', [\App\Http\Controllers\Dashboard\Admin\ShipmentController::class, 'updateCompanyPricePerKm'])->name('update-price-per-km');
        });

        // Reports
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');

        Route::prefix('configs')->name('configs.')->group(function () {
            Route::get('/', [ConfigController::class, 'index'])->name('index');
            Route::get('/create', [ConfigController::class, 'create'])->name('create');
            Route::post('/', [ConfigController::class, 'store'])->name('store');
            Route::get('/{config}/edit', [ConfigController::class, 'edit'])->name('edit');
            Route::patch('/{config}', [ConfigController::class, 'update'])->name('update');
            Route::delete('/{config}', [ConfigController::class, 'destroy'])->name('destroy');
            Route::patch('/{config}/toggle-status', [ConfigController::class, 'toggle'])->name('toggle-status');
        });
    });
});

// Shipment Company Dashboard Routes
Route::prefix('shipment')->name('shipment.')->group(function () {
    // Shipment Company Authentication Routes (you'll need to create these)
    Route::get('/login', [\App\Http\Controllers\Auth\ShipmentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\ShipmentAuthController::class, 'login']);

    Route::get('/register', [\App\Http\Controllers\Auth\ShipmentAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\ShipmentAuthController::class, 'register']);
    Route::post('/logout', [\App\Http\Controllers\Auth\ShipmentAuthController::class, 'logout'])->name('logout');

    // Shipment Company Dashboard Routes
    Route::middleware('auth:shipment')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'dashboard'])->name('dashboard');

        // Locations Management
        Route::get('/locations', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'index'])->name('locations.index');
        Route::get('/locations/create', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}/edit', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'edit'])->name('locations.edit');
        Route::patch('/locations/{location}', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'destroy'])->name('locations.destroy');
        Route::patch('/locations/{location}/toggle-status', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'toggleStatus'])->name('locations.toggle-status');


        // Shipment Locations Management
        Route::get('/shipment-locations', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'index'])->name('shipment-locations.index');
        Route::get('/shipment-locations/create', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'create'])->name('shipment-locations.create');
        Route::post('/shipment-locations', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'store'])->name('shipment-locations.store');
        Route::get('/shipment-locations/{shipmentLocation}/edit', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'edit'])->name('shipment-locations.edit');
        Route::patch('/shipment-locations/{shipmentLocation}', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'update'])->name('shipment-locations.update');
        Route::delete('/shipment-locations/{shipmentLocation}', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'destroy'])->name('shipment-locations.destroy');
        Route::patch('/shipment-locations/{shipmentLocation}/toggle-status', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'toggleStatus'])->name('shipment-locations.toggle-status');

        Route::get('/get-states-by-countries', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'getStatesByCountries'])
            ->name('get-states-by-countries');
        Route::get('/get-cities-by-states', [\App\Http\Controllers\Dashboard\ShipmentCompany\ShipmentLocationController::class, 'getCitiesByStates'])
            ->name('get-cities-by-states');

        Route::get('/get-states/{countryId}', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'getStates'])->name('locations.get-states');
        Route::get('/get-cities/{stateId}', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'getCities'])->name('locations.get-cities');
        Route::get('/get-zones/{cityId}', [\App\Http\Controllers\Dashboard\ShipmentCompany\LocationController::class, 'getZones'])->name('locations.get-zones');
        // Orders Management
        Route::get('/orders', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'orderDetails'])->name('orders.show');
        Route::patch('/orders/{id}/status', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
        Route::patch('/order-items/{item}/estimate',[\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateEstimate'])->name('order-items.update-estimate');
        // Return Requests Management
        Route::controller(\App\Http\Controllers\Dashboard\ShipmentCompany\ReturnRequestController::class)->group(function () {
            Route::get('/return-requests', 'index')->name('return-requests');
            Route::get('/return-requests/{returnRequest}/edit', 'show')->name('return-requests.show');
            Route::patch('/return-requests/items/{id}/toggle-status', 'toggleItemStatus')->name('return-requests.items.toggle-status');

        });
        // Ecommerce Orders Management (for shipment companies)
        Route::prefix('ecommerce')->name('ecommerce.')->group(function () {
            Route::get('/orders', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'ecommerceOrders'])->name('orders');
            Route::get('/orders/{id}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'ecommerceOrderDetails'])->name('orders.show');
            Route::post('/orders/{id}/accept', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'acceptEcommerceOrder'])->name('orders.accept');
            Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'cancelEcommerceOrder'])->name('orders.cancel');
            Route::patch('/orders/{id}/status', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateEcommerceOrderStatus'])->name('orders.update-status');
            Route::patch('/ecommerce/orders/{id}/update-item-status',[\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateEcommerceOrderItemStatus'])->name('orders.update-item-status');
            Route::post('/orders/{id}/send-otp',
                [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'sendDeliveryOtp']
            )->name('orders.send-otp');
            Route::post('/orders/{order}/direct-delivery',[\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'directDelivery']
            )->name('orders.directDelivery');

            Route::post('/orders/{id}/confirm-delivery',
                [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'confirmDeliveryOtp']
            )->name('orders.confirm-delivery');
        });

        //Notifications
        Route::get('/notifications', [ShipmentCompanyNotificationController::class, 'json'])->name('notifications');
        Route::get('/all-notifications', [ShipmentCompanyNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [ShipmentCompanyNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [ShipmentCompanyNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        // Package Tracking
        Route::post('/orders/{orderId}/items/{orderItemId}/tracking', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'addTracking'])->name('orders.add-tracking');

        // Packages Management
        Route::get('/packages', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'packages'])->name('packages');
        Route::get('/packages/{id}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'packageDetails'])->name('packages.show');

        // Profile Management
        Route::get('/profile', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'profile'])->name('profile');
        Route::patch('/profile', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/change-password', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'changePassword'])->name('change-password');

        // Price Per KM Management
        Route::get('/pricing', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'pricing'])->name('pricing');
        Route::patch('/update-price-per-km', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updatePricePerKm'])->name('update-price-per-km');
        Route::patch('/update-distance-factors', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'updateDistanceFactors'])
        ->name('update-distance-factors');
        Route::get('/subcategories/{category}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'getSubCategories'])->name('subcategories.get');
        Route::post('/subcategories-price', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'storeSubCategoryPrice'])->name('subcategories.store');
        Route::get('/subcategory-prices/{categoryPriceId}',
            [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'viewSubCategoryPrices'])
            ->name('subcategories.view');
        Route::put('/subcategories-price/{id}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'update'])->name('subcategories.update');
        Route::delete('/subcategories-price/{id}', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'destroy'])->name('subcategories.destroy');
        // Reports
        Route::get('/reports', [\App\Http\Controllers\Dashboard\ShipmentCompanyDashboardController::class, 'reports'])->name('reports');
    });
});

// Vendor Dashboard Routes
Route::prefix('vendor')->name('vendor.')->group(function () {
    // Vendor Authentication Routes
    // Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    // Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    // Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
    Route::get('/login', [\App\Http\Controllers\Auth\VendorAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\VendorAuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Auth\VendorAuthController::class, 'logout'])->name('logout');

    // Vendor Registration Routes
    Route::get('/register', [\App\Http\Controllers\Auth\VendorAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\VendorAuthController::class, 'register']);


    // Vendor Dashboard Routes
    Route::middleware('vendor')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Dashboard\Vendor\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [\App\Http\Controllers\Dashboard\Vendor\DashboardController::class, 'reports'])->name('reports');

        // Products Management
        Route::controller(\App\Http\Controllers\Dashboard\Vendor\ProductController::class)->group(function () {
            Route::get('/products', 'index')->name('products');
            Route::get('/products/create', 'create')->name('products.create');
            Route::post('/products', 'store')->name('products.store');
            Route::get('/products/{product}/edit', 'edit')->name('products.edit');
            Route::patch('/products/{product}', 'update')->name('products.update');
            Route::delete('/products/{product}/images/{media}', 'deleteImage')->name('products.delete-image');
            Route::patch('/products/{product}/toggle-status', 'toggleStatus')->name('products.toggle-status');
        });

        // Return Requests Management
        Route::controller(\App\Http\Controllers\Dashboard\Vendor\ReturnRequestController::class)->group(function () {
            Route::get('/return-requests', 'index')->name('return-requests');
            Route::get('/return-requests/{returnRequest}/edit', 'show')->name('return-requests.show');
            // Route::patch('/return-requests/{returnRequest}/toggle-status', 'toggleStatus')->name('return-requests.toggle-status');
            Route::patch('/return-requests/items/{id}/toggle-status', 'toggleItemStatus')->name('return-requests.items.toggle-status');

        });

        //Notifications
        Route::get('/notifications', [VendorNotificationController::class, 'json'])->name('notifications');
        Route::get('/all-notifications', [VendorNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [VendorNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [VendorNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

        // Products Reviews Management
        Route::controller(\App\Http\Controllers\Dashboard\Vendor\ProductController::class)->group(function () {
            Route::get('/product_reviews', 'reviews')->name('product_reviews');
        });
            // Vendor Branches Routes
        Route::get('/branches', [VendorBranchController::class, 'index'])->name('branches');
        Route::get('/branches/create', [VendorBranchController::class, 'create'])->name('branches.create');
        Route::post('/branches/store', [VendorBranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [VendorBranchController::class, 'edit'])->name('branches.edit');
        Route::get('/branches/{branch}/show', [VendorBranchController::class, 'show'])->name('branches.show');
        Route::patch('/branches/{branch}', [VendorBranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [VendorBranchController::class, 'destroy'])->name('branches.destroy');

        // API Endpoints for dynamic selects
        Route::get('/api/states/{country_id}', [VendorBranchController::class, 'getStates'])->name('api.states');
        Route::get('/api/cities/{state_id}', [VendorBranchController::class, 'getCities'])->name('api.cities');
        Route::get('/api/zones/{city_id}', [VendorBranchController::class, 'getZones'])->name('api.zones');

        // Orders Management
        Route::controller(\App\Http\Controllers\Dashboard\Vendor\OrderController::class)->group(function () {
            Route::get('/orders', 'index')->name('orders');
            Route::get('/orders/{order}', 'show')->name('orders.show');
            Route::patch('/orders/{order}/status', 'updateStatus')->name('orders.update-status');
            Route::patch('/orders/{order}/items/{item}/accept', 'acceptItem')->name('orders.items.accept');
            Route::patch('/orders/{order}/items/{item}/assign-shipment', 'assignItemShipment')->name('orders.items.assign-shipment');
            Route::patch('/orders/{order}/items/{item}/cancel', 'cancelItem')->name('orders.items.cancel');
        });

        // Profile Management
        Route::controller(\App\Http\Controllers\Dashboard\Vendor\ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile');
            Route::patch('/profile', 'update')->name('profile.update');
            Route::patch('/change-password', 'changePassword')->name('change-password');
        });
        Route::patch('/business-profile', [\App\Http\Controllers\Dashboard\Vendor\VendorBusinessProfileController::class, 'upsert'])
            ->name('business-profile.upsert');
    });
});
