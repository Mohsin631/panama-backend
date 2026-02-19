<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable','string','max:255'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'location' => ['nullable','string','max:255'],
            'per_page' => ['nullable','integer','min:1','max:50'],
            'sort' => ['nullable','in:latest,oldest'],
        ]);

        $query = Vendor::query()
            ->with('category')
            ->where('status', 'approved')
            ->where('onboarding_step', '>=', 3);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('about', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->filled('language')) {
            $query->whereJsonContains('languages', $request->language);
        }

        if ($request->filled('export_market')) {
            $query->whereJsonContains('export_markets', $request->export_market);
        }

        if ($request->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $perPage = $request->per_page ?? 12;

        $vendors = $query->paginate($perPage);

        foreach ($vendors as $vendor) {
            $vendor->whatsapp_no = 'hidden';
            $vendor->email = 'hidden';
        }

        return response()->json([
            'success' => true,
            'data' => $vendors
        ]);
    }

    public function show($id)
    {
        $vendor = Vendor::with('category')->where('id', $id)->where('status', 'approved')->where('onboarding_step', '>=', 3)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found or not approved yet.'
            ], 404);
        }

        $vendor->whatsapp_no = 'hidden';
        $vendor->email = 'hidden';

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }
}
