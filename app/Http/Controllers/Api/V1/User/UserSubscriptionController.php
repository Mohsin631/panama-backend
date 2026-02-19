<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Subscription;

class UserSubscriptionController extends Controller
{

    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $request->validate([
            'plan_id' => ['required','integer','exists:plans,id'],
        ]);

        $user = auth()->user();
        $plan = \App\Models\Plan::where('is_active', true)->findOrFail($request->plan_id);

        if (!$plan->stripe_price_id) {
            return response()->json([
                'success' => false,
                'message' => 'This plan is not configured with Stripe yet.'
            ], 422);
        }

        $activeRecurring = \App\Models\UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('stripe_subscription_id')
            ->latest()
            ->first();

        if ($activeRecurring && $plan->validity_unit !== 'day') {

            if (!$user->stripe_customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not linked with Stripe.'
                ], 422);
            }

            $frontend = rtrim(config('app.frontend_url'), '/');

            $portal = PortalSession::create([
                'customer' => $user->stripe_customer_id,
                'return_url' => $frontend . '/settings/billing',
            ]);

            return response()->json([
                'success' => true,
                'redirect_type' => 'billing_portal',
                'data' => [
                    'billing_portal_url' => $portal->url
                ]
            ]);
        }

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
                'metadata' => [
                    'app' => 'panama',
                    'user_id' => (string) $user->id,
                ],
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        $sub = \App\Models\UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'stripe_customer_id' => $user->stripe_customer_id,
            'status' => 'pending',
        ]);

        $frontend = rtrim(config('app.frontend_url'), '/');

        $mode = ($plan->validity_unit === 'day') ? 'payment' : 'subscription';

        $session = CheckoutSession::create([
            'customer' => $user->stripe_customer_id,
            'mode' => $mode,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => $frontend . "/billing/success?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url'  => $frontend . "/billing/cancel?plan_id=" . $plan->id,

            'metadata' => [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
                'local_subscription_id' => (string) $sub->id,
                'plan_validity_unit' => (string) $plan->validity_unit,
                'plan_validity_value' => (string) $plan->validity_value,
            ],
        ]);

        $sub->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'checkout_url' => $session->url,
                'session_id' => $session->id,
                'stripe_customer_id' => $user->stripe_customer_id,
            ]
        ]);
    }

    public function status(Request $request)
    {
        $request->validate(['session_id' => ['required','string']]);

        $user = auth()->user();

        $sub = UserSubscription::where('user_id', $user->id)
            ->where('stripe_checkout_session_id', $request->session_id)
            ->latest()
            ->first();

        if (!$sub) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $sub]);
    }

    public function me()
    {
        $user = auth()->user();

        $active = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        return response()->json(['success' => true, 'data' => $active]);
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'new_plan_id' => ['required','integer','exists:plans,id'],
        ]);

        $user = auth()->user();

        $newPlan = Plan::where('is_active', true)->findOrFail($request->new_plan_id);

        if (!$newPlan->stripe_price_id || $newPlan->validity_unit === 'day') {
            return response()->json([
                'success' => false,
                'message' => 'Only recurring plans (month/year) can be upgraded to.'
            ], 422);
        }

        $localSub = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('stripe_subscription_id')
            ->latest()
            ->first();

        if (!$localSub) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found.'
            ], 404);
        }

        if($localSub->plan_id == $newPlan->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are already on this plan.'
            ], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $stripeSub = Subscription::retrieve($localSub->stripe_subscription_id);
        $itemId = $stripeSub->items->data[0]->id;
        $updated = Subscription::update($stripeSub->id, [
            'items' => [[
                'id' => $itemId,
                'price' => $newPlan->stripe_price_id,
            ]],
            'proration_behavior' => 'always_invoice',
        ]);

        $localSub->plan_id = $newPlan->id;
        $localSub->save();

        return response()->json([
            'success' => true,
            'message' => 'Upgrade initiated. Stripe will bill prorated difference if applicable.',
            'data' => [
                'stripe_subscription_id' => $updated->id,
                'stripe_status' => $updated->status,
            ]
        ]);
    }
}
