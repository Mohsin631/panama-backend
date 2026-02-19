<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Checkout\Session as CheckoutSession;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig,
                config('services.stripe.webhook_secret')
            );
        } catch (\Throwable $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') { // :contentReference[oaicite:3]{index=3}
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            $localId = $session->metadata->local_subscription_id ?? null;
            $planId  = $session->metadata->plan_id ?? null;

            if ($localId) {
                $sub = UserSubscription::find($localId);
            } else {
                $sub = UserSubscription::where('stripe_checkout_session_id', $session->id)->first();
            }

            if ($sub) {
                $plan = $planId ? Plan::find($planId) : $sub->plan;

                $sub->stripe_customer_id = $session->customer ?? $sub->stripe_customer_id;
                $sub->starts_at = now();

                if (!empty($session->subscription)) {
                    $sub->stripe_subscription_id = $session->subscription;
                    $sub->status = 'active';
                } else {
                    $days = ($plan->validity_unit === 'day') ? (int) $plan->validity_value : 1;
                    $sub->expires_at = now()->addDays($days);
                    $sub->status = 'active';
                }

                if($plan->validity_unit === 'month') {
                    $sub->expires_at = now()->addMonths($plan->validity_value);
                    $sub->current_period_end = $sub->expires_at;
                } else if($plan->validity_unit === 'year') {
                    $sub->expires_at = now()->addYears($plan->validity_value);
                    $sub->current_period_end = $sub->expires_at;
                }

                $sub->save();
            }
        }

        if ($event->type === 'customer.subscription.created' || $event->type === 'customer.subscription.updated' || $event->type === 'customer.subscription.deleted') { // :contentReference[oaicite:4]{index=4}
            $subscription = $event->data->object;

            $sub = UserSubscription::where('stripe_subscription_id', $subscription->id)->first();

            if ($sub) {
                $sub->status = match ($subscription->status) {
                    'active' => 'active',
                    'trialing' => 'active',
                    'past_due' => 'past_due',
                    'incomplete' => 'incomplete',
                    'canceled' => 'canceled',
                    'unpaid' => 'past_due',
                    default => $sub->status,
                };

                if (!empty($subscription->items->data[0]->current_period_end)) {
                    $sub->current_period_end = \Carbon\Carbon::createFromTimestamp($subscription->items->data[0]->current_period_end);
                    $sub->expires_at = $sub->current_period_end;
                }

                if (!empty($subscription->canceled_at)) {
                    $sub->canceled_at = \Carbon\Carbon::createFromTimestamp($subscription->canceled_at);
                }

                $sub->save();
            }
        }

        if ($event->type === 'invoice.payment_failed') {
            $invoice = $event->data->object;
            $sub = UserSubscription::where('stripe_subscription_id', $invoice->subscription ?? null)->first();
            if ($sub) {
                $sub->status = 'past_due';
                $sub->save();
            }
        }

        return response('ok', 200);
    }
}
