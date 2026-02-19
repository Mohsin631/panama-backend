<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Vendor\VendorPasswordController;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Panama API']);
});

Route::get('/login', function () {
    return response()->json(['message' => 'unauthenticated'], 401);
})->name('login');

Route::get('/forgot-password', [VendorPasswordController::class, 'forgotPassword'])->name('password.reset');

Route::get('/test-view', function () {
    return view('test');
});
