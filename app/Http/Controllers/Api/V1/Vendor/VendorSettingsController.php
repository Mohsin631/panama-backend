<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorSettingsUpdateRequest;
use App\Models\VendorSetting;

class VendorSettingsController extends Controller
{
    public function show()
    {
        $vendor = auth('vendor')->user();

        $settings = VendorSetting::firstOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'new_order_received' => true,
                'order_status_updates' => true,
                'order_cancelled' => true,
                'new_customer_message' => true,
                'admin_messages' => true,
            ]
        );

        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function update(VendorSettingsUpdateRequest $request)
    {
        $vendor = auth('vendor')->user();

        $settings = VendorSetting::firstOrCreate(['vendor_id' => $vendor->id]);

        $settings->fill($request->validated());
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated.',
            'data' => $settings
        ]);
    }
}
