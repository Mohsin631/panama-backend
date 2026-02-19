<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductMediaUploadRequest;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Support\Facades\Storage;

class VendorProductMediaController extends Controller
{
    public function upload(ProductMediaUploadRequest $request, $productId)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);

        // current counts (to enforce max 10 images overall)
        $currentImages = ProductMedia::where('product_id', $product->id)->where('type','image')->count();
        $currentVideos = ProductMedia::where('product_id', $product->id)->where('type','video')->count();

        $uploaded = ['images' => [], 'videos' => []];

        // Images
        if ($request->hasFile('images')) {
            $incomingCount = count($request->file('images'));
            if (($currentImages + $incomingCount) > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can upload maximum 10 images per product.'
                ], 422);
            }

            $sortBase = ProductMedia::where('product_id', $product->id)->max('sort_order') ?? 0;

            foreach ($request->file('images') as $i => $file) {
                $path = $file->store("products/{$product->id}/images", 'public');

                $media = ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'path' => $path,
                    'sort_order' => $sortBase + $i + 1,
                ]);

                $uploaded['images'][] = [
                    'id' => $media->id,
                    'url' => asset('storage/' . $media->path),
                ];
            }
        }

        // Videos (optional)
        if ($request->hasFile('videos')) {
            // you can set your own max videos rule here
            $incomingCount = count($request->file('videos'));
            if (($currentVideos + $incomingCount) > 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can upload maximum 5 videos per product.'
                ], 422);
            }

            $sortBase = ProductMedia::where('product_id', $product->id)->max('sort_order') ?? 0;

            foreach ($request->file('videos') as $i => $file) {
                $path = $file->store("products/{$product->id}/videos", 'public');

                $media = ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => 'video',
                    'path' => $path,
                    'sort_order' => $sortBase + $i + 1,
                ]);

                $uploaded['videos'][] = [
                    'id' => $media->id,
                    'url' => asset('storage/' . $media->path),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Media uploaded.',
            'data' => $uploaded
        ]);
    }

    public function list($productId)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);

        $media = ProductMedia::where('product_id', $product->id)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'type' => $m->type,
                'url' => asset('storage/' . $m->path),
                'sort_order' => $m->sort_order,
            ]);

        return response()->json(['success' => true, 'data' => $media]);
    }

    public function delete($productId, $mediaId)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);

        $media = ProductMedia::where('product_id', $product->id)->findOrFail($mediaId);

        Storage::disk('public')->delete($media->path);
        if ($media->thumbnail_path) {
            Storage::disk('public')->delete($media->thumbnail_path);
        }

        $media->delete();

        return response()->json(['success' => true, 'message' => 'Media deleted.']);
    }

    public function reorder($productId)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);

        request()->validate([
            'orders' => ['required','array','min:1'],
            'orders.*.id' => ['required','integer','exists:product_media,id'],
            'orders.*.sort_order' => ['required','integer','min:0','max:9999'],
        ]);

        foreach (request('orders') as $item) {
            ProductMedia::where('product_id', $product->id)
                ->where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Media order updated.']);
    }
}
