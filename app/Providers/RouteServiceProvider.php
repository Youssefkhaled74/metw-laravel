<?php

namespace App\Providers;

use App\Models\Bannar;
use App\Models\Brand;
use App\Models\CancelReason;
use App\Models\Category;
use App\Models\City;
use App\Models\ConsignmentType;
use App\Models\ContactAdmin;
use App\Models\Country;
use App\Models\DeliveryType;
use App\Models\MainCategory;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ShipmentCompany;
use App\Models\Size;
use App\Models\State;
use App\Models\Vendor;
use App\Models\Zone;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::bind('brand', function ($value) {
            return Brand::withoutGlobalScopes()->findOrFail($value);
        });

        Route::bind('mainCategory', function ($value) {
            return MainCategory::withoutGlobalScopes()->findOrFail($value);
        });

        Route::bind('category', function ($value) {
            return Category::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('color', function ($value) {
            return ProductColor::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('cancel-reason', function ($value) {
            return CancelReason::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('productSize', function ($value) {
            return ProductSize::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('banner', function ($value) {
            return Bannar::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('contactAdmin', function ($value) {
            return ContactAdmin::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('country', function ($value) {
            return Country::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('state', function ($value) {
            return State::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('city', function ($value) {
            return City::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('zone', function ($value) {
            return Zone::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('consignmentType', function ($value) {
            return ConsignmentType::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('deliveryType', function ($value) {
            return DeliveryType::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('page', function ($value) {
            return Page::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('size', function ($value) {
            return Size::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('shipmentCompany', function ($value) {
            return ShipmentCompany::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('vendor', function ($value) {
            return Vendor::withoutGlobalScopes()->findOrFail($value);
        });
        Route::bind('product', function ($value) {
            return Product::withoutGlobalScopes()->findOrFail($value);
        });
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
