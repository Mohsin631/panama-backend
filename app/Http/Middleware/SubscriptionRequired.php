<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSubscription;

class SubscriptionRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $sub = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if (!$sub || ($sub->expires_at && $sub->expires_at->isPast())) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription required to access vendor contact details.',
                'code' => 'SUBSCRIPTION_REQUIRED',
            ], 403);
        }

        $request->attributes->set('active_subscription', $sub);

        return $next($request);
    }
}
