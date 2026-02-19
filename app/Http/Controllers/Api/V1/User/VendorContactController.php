<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Str;

class VendorContactController extends Controller
{
    public function whatsapp($productId)
    {
        $product = Product::with('vendor')
            ->where('status', 'published')
            ->where('is_active', true)
            ->findOrFail($productId);

        $vendor = $product->vendor;

        if (!$vendor || $vendor->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not available.'
            ], 404);
        }

        if (!$vendor->whatsapp_no) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor WhatsApp number not available.'
            ], 404);
        }

        $digits = preg_replace('/\D+/', '', $vendor->whatsapp_no);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $frontend = rtrim(config('app.frontend_url'), '/');
        $productUrl = $frontend . "/products/" . $product->id;

        $message = "Hi, I am interested in your product \"{$product->title}\".\n\nYou can find it here:\n{$productUrl}";

        $encodedMessage = urlencode($message);

        $waLink = "https://wa.me/{$digits}?text={$encodedMessage}";

        return response()->json([
            'success' => true,
            'data' => [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->business_name,
                'product_id' => $product->id,
                'product_name' => $product->title,
                'whatsapp_no' => $vendor->whatsapp_no,
                'whatsapp_link' => $waLink,
            ]
        ]);
    }
}
