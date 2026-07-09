<?php

use App\Http\Controllers\Api\MetwGo\AuthController as MetwGoAuthController;
use App\Http\Controllers\Api\MetwGo\HomeController as MetwGoHomeController;
use App\Http\Controllers\Api\MetwGo\LookupController as MetwGoLookupController;
use App\Http\Controllers\Api\MetwGo\NotificationController as MetwGoNotificationController;
use App\Http\Controllers\Api\MetwGo\OrderController as MetwGoOrderController;
use App\Http\Controllers\Api\MetwGo\ProfileController as MetwGoProfileController;
use App\Http\Controllers\Api\MetwGo\RegistrationController as MetwGoRegistrationController;
use App\Http\Controllers\Api\MetwGo\WalletController as MetwGoWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('metwgo')->group(function () {
    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [MetwGoAuthController::class, 'login']);
        Route::post('forgot-password/send-otp', [MetwGoAuthController::class, 'sendForgotPasswordOtp']);
        Route::post('otp/verify', [MetwGoAuthController::class, 'verifyOtp']);
        Route::post('otp/resend', [MetwGoAuthController::class, 'resendOtp']);
        Route::post('password/reset', [MetwGoAuthController::class, 'resetPassword']);

        // Registration
        Route::post('register/step-1', [MetwGoRegistrationController::class, 'stepOne']);
        Route::post('register/step-2', [MetwGoRegistrationController::class, 'stepTwo'])->middleware('auth:sanctum');
        Route::post('register/step-3', [MetwGoRegistrationController::class, 'stepThree'])->middleware('auth:sanctum');
        Route::post('register/step-4', [MetwGoRegistrationController::class, 'stepFour'])->middleware('auth:sanctum');
        Route::post('register/step-5', [MetwGoRegistrationController::class, 'stepFive'])->middleware('auth:sanctum');
        Route::get('registration/status', [MetwGoRegistrationController::class, 'registrationStatus'])->middleware('auth:sanctum');

        // Authenticated auth routes
        Route::post('logout', [MetwGoAuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('password/change', [MetwGoAuthController::class, 'changePassword'])->middleware('auth:sanctum');
    });

    // Public lookup routes
    Route::get('warehouses', [MetwGoLookupController::class, 'warehouses']);

    Route::prefix('lookups')->group(function () {
        Route::get('transport-types', [MetwGoLookupController::class, 'transportTypes']);
        Route::get('governorates', [MetwGoLookupController::class, 'governorates']);
        Route::get('cities', [MetwGoLookupController::class, 'cities']);
    });

    // Protected routes (access_token required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('home', [MetwGoHomeController::class, 'index']);
        Route::post('courier/availability', [MetwGoHomeController::class, 'setAvailability']);

        Route::prefix('orders')->group(function () {
            Route::get('incoming', [MetwGoOrderController::class, 'incoming']);
            Route::get('active', [MetwGoOrderController::class, 'active']);
            Route::get('{orderId}', [MetwGoOrderController::class, 'show'])->whereNumber('orderId');
            Route::post('{orderId}/start', [MetwGoOrderController::class, 'start'])->whereNumber('orderId');
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [MetwGoProfileController::class, 'show']);
            Route::put('/', [MetwGoProfileController::class, 'update']);
            Route::put('work-info', [MetwGoProfileController::class, 'updateWorkInfo']);
            Route::put('transport', [MetwGoProfileController::class, 'updateTransport']);
            Route::put('service-areas', [MetwGoProfileController::class, 'updateServiceAreas']);
            Route::post('documents', [MetwGoProfileController::class, 'uploadDocuments']);
        });

        Route::prefix('notifications')->group(function () {
            Route::get('count', [MetwGoNotificationController::class, 'count']);
        });

        Route::prefix('wallet')->group(function () {
            Route::get('summary', [MetwGoWalletController::class, 'summary']);
            Route::post('withdrawals', [MetwGoWalletController::class, 'withdrawals']);
        });
    });
});
