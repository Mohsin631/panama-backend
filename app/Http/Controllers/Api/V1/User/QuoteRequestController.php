<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateQuoteRequest;
use App\Http\Requests\SendQuoteMessageRequest;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\QuoteMessage;
use App\Models\QuoteStatusHistory;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function store(UserCreateQuoteRequest $request)
    {
        $user = auth()->user();

        $product = Product::with('vendor')
            ->where('status','published')
            ->where('is_active', true)
            ->findOrFail($request->product_id);

        if (!$product->vendor || $product->vendor->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Vendor not available'], 404);
        }

        if($product->moq && $request->quantity < $product->moq) {
            return response()->json(['success' => false, 'message' => 'Minimum order quantity is '.$product->moq], 422);
        }

        $already = QuoteRequest::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->whereIn('status', ['open','quoted','negotiation'])
            ->first();

        if ($already) {
            return response()->json(['success' => false, 'message' => 'You already have an open quote request for this product'], 422);
        }

        // create thread
        $qr = QuoteRequest::create([
            'user_id' => $user->id,
            'vendor_id' => $product->vendor_id,
            'product_id' => $product->id,
            'product_title' => $product->title,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'shipping_country' => $request->shipping_country,
            'shipping_city' => $request->shipping_city,
            'note' => $request->note,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        // first message = user's note
        QuoteMessage::create([
            'quote_request_id' => $qr->id,
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'message' => $request->note,
        ]);

        QuoteStatusHistory::create([
            'quote_request_id' => $qr->id,
            'from_status' => null,
            'to_status' => 'open',
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
            'note' => 'Quote request created',
        ]);

        return response()->json(['success' => true, 'message' => 'Quote request sent.', 'data' => $qr], 201);
    }

    // GET /api/v1/user/quotes
    public function index()
    {
        $user = auth()->user();

        $quotes = QuoteRequest::with(['vendor:id,business_name,location,image_path','product:id,title'])
            ->where('user_id', $user->id)
            ->latest('last_message_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $quotes]);
    }

    // GET /api/v1/user/quotes/{id}
    public function show($id)
    {
        $user = auth()->user();

        $qr = QuoteRequest::with([
                'vendor:id,business_name,location,image_path',
                'product:id,title',
                'history'
            ])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $qr]);
    }

    // POST /api/v1/user/quotes/{id}/messages
    public function sendMessage(SendQuoteMessageRequest $request, $id)
    {
        $user = auth()->user();

        $qr = QuoteRequest::where('user_id', $user->id)->findOrFail($id);

        $msg = QuoteMessage::create([
            'quote_request_id' => $qr->id,
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'data' => $msg], 201);
    }

    // POST /api/v1/user/quotes/{id}/cancel
    public function cancel($id)
    {
        $user = auth()->user();
        $qr = QuoteRequest::where('user_id', $user->id)->findOrFail($id);

        if (in_array($qr->status, ['closed','shipped','cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel at this stage.'], 422);
        }

        $from = $qr->status;
        $qr->status = 'cancelled';
        $qr->save();

        QuoteStatusHistory::create([
            'quote_request_id' => $qr->id,
            'from_status' => $from,
            'to_status' => 'cancelled',
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
            'note' => 'Cancelled by user',
        ]);

        $qr->addSystemMessage(
            "User cancelled the quote request.",
            'status',
            ['from_status' => $from, 'to_status' => 'cancelled']
        );

        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Quote cancelled.']);
    }

    public function confirm($id)
    {
        $user = auth()->user();
        $qr = QuoteRequest::where('user_id', $user->id)->findOrFail($id);

        if ($qr->status !== 'quoted') {
            return response()->json(['success' => false, 'message' => 'Only quoted requests can be confirmed.'], 422);
        }

        $from = $qr->status;
        $qr->status = 'accepted';
        $qr->save();

        QuoteStatusHistory::create([
            'quote_request_id' => $qr->id,
            'from_status' => $from,
            'to_status' => 'accepted',
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
            'note' => 'Confirmed by user',
        ]);


        $qr->addSystemMessage(
            "Quote accepted by buyer.",
            'status',
            [
                'from_status' => $from,
                'to_status' => 'accepted',
                'accepted_by_user_id' => $user->id,
            ]
        );

        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Quote confirmed. Vendor will contact you soon.']);
    }

        public function messages(Request $request, $id)
    {
        $user = auth()->user();

        $request->validate([
            'before_id' => ['nullable','integer','min:1'],
            'limit' => ['nullable','integer','min:1','max:50'],
        ]);

        $qr = QuoteRequest::where('user_id', $user->id)->findOrFail($id);

        $limit = $request->input('limit', 15);

        $q = QuoteMessage::where('quote_request_id', $qr->id)
            ->orderByDesc('id');

        if ($request->filled('before_id')) {
            $q->where('id', '<', (int)$request->before_id);
        }

        $messages = $q->take($limit)->get();

        // return chronological order for UI (oldest -> newest)
        $messagesAsc = $messages->sortBy('id')->values();

        $nextBeforeId = $messages->count() ? $messages->last()->id : null; // last in DESC list = oldest id in batch

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messagesAsc,
                'next_before_id' => $nextBeforeId,
                'has_more' => $messages->count() === $limit
            ]
        ]);
    }

    public function markSeen(Request $request, $id)
{
    $user = auth()->user();

    $request->validate([
        'last_seen_message_id' => ['required','integer','min:1'],
    ]);

    $qr = QuoteRequest::where('user_id', $user->id)->findOrFail($id);

    QuoteMessage::where('quote_request_id', $qr->id)
        ->where('id', '<=', (int)$request->last_seen_message_id)
        ->whereNull('seen_by_user_at')
        ->whereIn('sender_type', ['vendor','system']) // only messages user receives
        ->update(['seen_by_user_at' => now()]);

    return response()->json(['success' => true, 'message' => 'Seen updated.']);
}
}
