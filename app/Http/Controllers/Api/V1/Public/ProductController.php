<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function vendorProducts(Request $request, $vendorId)
    {
        $request->validate([
            'search' => ['nullable','string','max:255'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'moq_min' => ['nullable','integer','min:1'],
            'moq_max' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        // vendor must be approved
        $vendor = Vendor::where('status', 'approved')->findOrFail($vendorId);

        $query = Product::query()
            ->with(['category', 'images', 'videos'])
            ->where('vendor_id', $vendor->id)
            ->where('status', 'published')
            ->where('is_active', true);

        // search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%");
            });
        }

        // category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // MOQ filters
        if ($request->filled('moq_min')) {
            $query->where('moq', '>=', (int) $request->moq_min);
        }
        if ($request->filled('moq_max')) {
            $query->where('moq', '<=', (int) $request->moq_max);
        }

        $products = $query->latest()->paginate($request->per_page ?? 12);

        // Add cover image url (first image)
        $products->getCollection()->transform(function ($p) {
            $p->cover_image = optional($p->images->first())->url ?? null;
            return $p;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'vendor' => [
                    'id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'location' => $vendor->location,
                    'image' => $vendor->image_path ? asset('storage/'.$vendor->image_path) : null,
                ],
                'products' => $products
            ]
        ]);
    }

    // GET /api/v1/public/products/{id}
    public function show($id)
    {
        $product = Product::with([
                'category',
                'vendor:id,business_name,location,image_path,status',
                'images',
                'videos'
            ])
            ->where('status', 'published')
            ->where('is_active', true)
            ->findOrFail($id);

        // block if vendor not approved
        if (!$product->vendor || $product->vendor->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Product not available'], 404);
        }

        // append public URLs
        $product->vendor_image = $product->vendor->image_path
            ? asset('storage/'.$product->vendor->image_path)
            : null;

        $product->cover_image = optional($product->images->first())->url ?? null;

        // optional: map media urls nicely
        $product->images_list = $product->images->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->url,
            'sort_order' => $m->sort_order,
        ])->values();

        $product->videos_list = $product->videos->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->url,
            'sort_order' => $m->sort_order,
        ])->values();

        return response()->json(['success' => true, 'data' => $product]);
    }
}
