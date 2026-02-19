<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }
}
