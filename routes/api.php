<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\PlanController;
use App\Http\Controllers\Api\V1\Vendor\VendorAuthController;
use App\Http\Controllers\Api\V1\Vendor\VendorOnboardingController;
use App\Http\Controllers\Api\V1\Vendor\VendorPasswordController;
use App\Http\Controllers\Api\V1\Vendor\VendorProductController;
use App\Http\Controllers\Api\V1\Vendor\VendorProductMediaController;
use App\Http\Controllers\Api\V1\Vendor\VendorSettingsController;
use App\Http\Controllers\Api\V1\User\UserAuthController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\V1\User\UserSubscriptionController;
use App\Http\Controllers\Api\V1\Public\VendorController;
use App\Http\Controllers\Api\V1\Public\ProductController;
use App\Http\Controllers\Api\V1\User\VendorContactController;
use App\Http\Controllers\Api\V1\User\QuoteRequestController;
use App\Http\Controllers\Api\V1\Vendor\VendorQuoteRequestController;

Route::prefix('v1')->group(function () {




    // Public Endpoints - No Authentication Required

    Route::get('/public/categories', [CategoryController::class, 'index']);
    Route::get('/public/plans', [PlanController::class, 'index']);
    Route::get('/public/vendors', [VendorController::class, 'index']);
    Route::get('/public/vendors/{id}', [VendorController::class, 'show']);
    Route::get('/public/vendors/{vendorId}/products', [ProductController::class, 'vendorProducts']);
    Route::get('/public/products/{id}', [ProductController::class, 'show']);


    // Vendor Endpoints

    Route::prefix('vendor')->group(function () {
        Route::post('/auth/register', [VendorAuthController::class, 'register']);
        Route::post('/auth/login', [VendorAuthController::class, 'login']);
        Route::post('/auth/forgot-password', [VendorPasswordController::class, 'forgot']);
        Route::post('/auth/reset-password', [VendorPasswordController::class, 'reset'])->name('password.update');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/auth/me', [VendorAuthController::class, 'me']);
            Route::post('/auth/logout', [VendorAuthController::class, 'logout']);
            Route::post('/onboarding/step-2', [VendorOnboardingController::class, 'step2']);
            Route::post('/onboarding/step-3', [VendorOnboardingController::class, 'step3']);
            Route::post('/auth/change-password', [VendorAuthController::class, 'changePassword']);

            ////////////////////////////////////////////////////////////////////////
            //////// Vendor Settings //////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////

            Route::get('/settings', [VendorSettingsController::class, 'show']);
            Route::put('/settings', [VendorSettingsController::class, 'update']);

            ////////////////////////////////////////////////////////////////////////
            //////// Product Management - CRUD + Status + Media Management ////////
            //////////////////////////////////////////////////////////////////////

            Route::get('/products', [VendorProductController::class, 'index']);
            Route::post('/products', [VendorProductController::class, 'store']);
            Route::get('/products/{id}', [VendorProductController::class, 'show']);
            Route::put('/products/{id}', [VendorProductController::class, 'update']);
            Route::delete('/products/{id}', [VendorProductController::class, 'destroy']);

            // Publish/Unpublish/Archive

            Route::post('/products/{id}/status', [VendorProductController::class, 'changeStatus']);

            // Media
            Route::post('/products/{productId}/media', [VendorProductMediaController::class, 'upload']);
            Route::get('/products/{productId}/media', [VendorProductMediaController::class, 'list']);
            Route::delete('/products/{productId}/media/{mediaId}', [VendorProductMediaController::class, 'delete']);
            Route::post('/products/{productId}/media/reorder', [VendorProductMediaController::class, 'reorder']);

            // Quote Requests

            Route::get('/quotes', [VendorQuoteRequestController::class, 'index']);
            Route::get('/quotes/{id}', [VendorQuoteRequestController::class, 'show']);
            Route::post('/quotes/{id}/messages', [VendorQuoteRequestController::class, 'sendMessage']);
            Route::post('/quotes/{id}/set-quote', [VendorQuoteRequestController::class, 'setQuote']);
            Route::post('/quotes/{id}/status', [VendorQuoteRequestController::class, 'updateStatus']);
            Route::get('/quotes/{id}/messages', [VendorQuoteRequestController::class, 'messages']);
            Route::post('/quotes/{id}/messages/seen', [VendorQuoteRequestController::class, 'markSeen']);
        });
    });




    // User Endpoints

    Route::prefix('user')->group(function () {
        Route::post('/auth/register', [UserAuthController::class, 'register']);
        Route::post('/auth/login', [UserAuthController::class, 'login']);
        Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/auth/me', [UserAuthController::class, 'me']);
            Route::post('/auth/logout', [UserAuthController::class, 'logout']);
            Route::post('/subscriptions/checkout', [UserSubscriptionController::class, 'checkout']);
            Route::post('/subscriptions/upgrade', [UserSubscriptionController::class, 'upgrade']);
            Route::get('/subscriptions/status', [UserSubscriptionController::class, 'status']);
            Route::get('/subscriptions/me', [UserSubscriptionController::class, 'me']);
            Route::get('/products/{productId}/whatsapp', [VendorContactController::class, 'whatsapp'])->middleware('subscribed');

            // Quote Requests
            Route::post('/quotes', [QuoteRequestController::class, 'store'])->middleware('subscribed');
            Route::get('/quotes', [QuoteRequestController::class, 'index'])->middleware('subscribed');
            Route::get('/quotes/{id}', [QuoteRequestController::class, 'show'])->middleware('subscribed');
            Route::post('/quotes/{id}/messages', [QuoteRequestController::class, 'sendMessage'])->middleware('subscribed');
            Route::post('/quotes/{id}/cancel', [QuoteRequestController::class, 'cancel'])->middleware('subscribed');
            Route::post('/quotes/{id}/confirm', [QuoteRequestController::class, 'confirm'])->middleware('subscribed');
            Route::get('/quotes/{id}/messages', [QuoteRequestController::class, 'messages']);
            Route::post('/quotes/{id}/messages/seen', [QuoteRequestController::class, 'markSeen']);
        });
    });

});
