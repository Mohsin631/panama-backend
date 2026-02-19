<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorOnboardingStep2Request;
use App\Http\Requests\VendorOnboardingStep3Request;

class VendorOnboardingController extends Controller
{
    public function step2(VendorOnboardingStep2Request $request)
    {
        $vendor = auth('vendor')->user();

        if ($vendor->onboarding_step == 2) {
            return response()->json(['success' => false, 'message' => 'move to step-3'], 400);
        }

        if($vendor->onboarding_step == 3) {
            return response()->json(['success' => false, 'message' => 'pending approval'], 400);
        }

        $vendor->fill($request->validated());
        $vendor->onboarding_step = max($vendor->onboarding_step, 2);
        $vendor->save();

        return response()->json(['success' => true, 'message' => 'Step 2 saved', 'data' => $vendor]);
    }

    public function step3(VendorOnboardingStep3Request $request)
    {
        $vendor = auth('vendor')->user();

        if ($vendor->onboarding_step == 3) {
            return response()->json(['success' => false, 'message' => 'pending approval'], 400);
        }

        if($vendor->onboarding_step < 2) {
            return response()->json(['success' => false, 'message' => 'complete step-2 first'], 400);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vendors', 'public');
            $data['image_path'] = $path;
        }

        $vendor->fill($data);
        $vendor->onboarding_step = max($vendor->onboarding_step, 3);
        $vendor->save();

        return response()->json(['success' => true, 'message' => 'Onboarding completed. Await approval.', 'data' => $vendor]);
    }
}
