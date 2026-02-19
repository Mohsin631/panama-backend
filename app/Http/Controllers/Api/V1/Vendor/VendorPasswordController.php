<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorForgotPasswordRequest;
use App\Http\Requests\VendorResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class VendorPasswordController extends Controller
{
    public function forgot(VendorForgotPasswordRequest $request)
    {
        $status = Password::broker('vendors')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => __($status)])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }

    public function reset(VendorResetPasswordRequest $request)
    {
        $status = Password::broker('vendors')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($vendor) use ($request) {
                $vendor->password = Hash::make($request->password);
                $vendor->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ($request->expectsJson()
                ? response()->json(['success' => true, 'message' => __($status)])
                : redirect('/forgot-password')->with('status', 'Password reset successfully. Please login.'))
            : ($request->expectsJson()
                ? response()->json(['success' => false, 'message' => __($status)], 400)
                : back()->withInput()->withErrors(['email' => __($status)]));
    }

    public function forgotPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        return view('auth.passwords.reset', ['token' => $token, 'email' => $email]);
    }
}
