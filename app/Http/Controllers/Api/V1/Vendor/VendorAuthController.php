<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorLoginRequest;
use App\Http\Requests\VendorRegisterStep1Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\VendorChangePasswordRequest;

class VendorAuthController extends Controller
{
    public function register(VendorRegisterStep1Request $request)
    {
        $vendor = Vendor::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'onboarding_step' => 1,
        ]);

        $token = $vendor->createToken('vendor-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered. Continue onboarding.',
            'data' => [
                'vendor' => $vendor,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(VendorLoginRequest $request)
    {
        $vendor = Vendor::where('email', $request->email)->first();

        if (!$vendor || !Hash::check($request->password, $vendor->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $token = $vendor->createToken('vendor-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'vendor' => $vendor,
                'token' => $token,
            ],
        ]);
    }

    public function me()
    {
        return response()->json(['success' => true, 'data' => auth('vendor')->user()]);
    }

    public function logout()
    {
        $vendor = auth('vendor')->user();
        $vendor->currentAccessToken()?->delete();

        return response()->json(['success' => true, 'message' => 'Logged out']);
    }

    public function changePassword(VendorChangePasswordRequest $request)
    {
        $vendor = auth('vendor')->user();

        if (!Hash::check($request->current_password, $vendor->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $vendor->password = Hash::make($request->password);
        $vendor->save();

        $vendor->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.'
        ]);
    }
}
