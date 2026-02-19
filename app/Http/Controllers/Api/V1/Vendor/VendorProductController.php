<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorProductPublishRequest;
use App\Http\Requests\VendorProductStoreRequest;
use App\Http\Requests\VendorProductUpdateRequest;
use App\Models\Product;

class VendorProductController extends Controller
{
    public function index()
    {
        $vendor = auth('vendor')->user();

        $products = Product::with(['category','images','videos'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function store(VendorProductStoreRequest $request)
    {
        $vendor = auth('vendor')->user();

        // optional: only approved vendors can create
        if ($vendor->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Vendor not approved yet.'], 403);
        }

        $product = Product::create([
            'vendor_id' => $vendor->id,
            ...$request->validated(),
            'status' => 'draft',
        ]);

        return response()->json(['success' => true, 'message' => 'Product created (draft). Upload media next.', 'data' => $product], 201);
    }

    public function show($id)
    {
        $vendor = auth('vendor')->user();

        $product = Product::with(['category','images','videos'])
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $product]);
    }

    public function update(VendorProductUpdateRequest $request, $id)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $product->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Product updated.', 'data' => $product]);
    }

    public function destroy($id)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }

    public function changeStatus(VendorProductPublishRequest $request, $id)
    {
        $vendor = auth('vendor')->user();

        $product = Product::where('vendor_id', $vendor->id)->withCount([
            'media as images_count' => fn ($q) => $q->where('type','image')
        ])->findOrFail($id);

        // publishing rule: at least 3 images
        if ($request->status === 'published' && $product->images_count < 3) {
            return response()->json([
                'success' => false,
                'message' => 'At least 3 images are required to publish the product.'
            ], 422);
        }

        $product->status = $request->status;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Status updated.', 'data' => $product]);
    }
}
