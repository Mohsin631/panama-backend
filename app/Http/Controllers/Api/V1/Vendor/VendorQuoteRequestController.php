<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendQuoteMessageRequest;
use App\Http\Requests\VendorUpdateQuoteStatusRequest;
use App\Http\Requests\VendorSetQuoteRequest;
use App\Models\QuoteRequest;
use App\Models\QuoteMessage;
use App\Models\QuoteStatusHistory;
use Illuminate\Http\Request;

class VendorQuoteRequestController extends Controller
{
    // GET /api/v1/vendor/quotes
    public function index()
    {
        $vendor = auth('vendor')->user();

        $quotes = QuoteRequest::with(['user:id,name,email','product:id,title'])
            ->where('vendor_id', $vendor->id)
            ->latest('last_message_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $quotes]);
    }

    // GET /api/v1/vendor/quotes/{id}
    public function show($id)
    {
        $vendor = auth('vendor')->user();

        $qr = QuoteRequest::with(['user:id,name,email','product:id,title','history'])
            ->where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $qr]);
    }

    // POST /api/v1/vendor/quotes/{id}/messages
    public function sendMessage(SendQuoteMessageRequest $request, $id)
    {
        $vendor = auth('vendor')->user();
        $qr = QuoteRequest::where('vendor_id', $vendor->id)->findOrFail($id);

        $msg = QuoteMessage::create([
            'quote_request_id' => $qr->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendor->id,
            'message' => $request->message,
        ]);

        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'data' => $msg], 201);
    }

    // POST /api/v1/vendor/quotes/{id}/set-quote
    public function setQuote(VendorSetQuoteRequest $request, $id)
    {
        $vendor = auth('vendor')->user();
        $qr = QuoteRequest::where('vendor_id', $vendor->id)->findOrFail($id);

        if($qr->status === 'cancelled' || $qr->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Cannot set quote on a cancelled/closed request.'], 422);
        }

        $qr->quoted_price = $request->quoted_price;
        $qr->currency = $request->currency ?? $qr->currency;
        $qr->quoted_moq = $request->quoted_moq;
        $from = $qr->status;
        $qr->status = 'quoted';
        $qr->save();

        QuoteStatusHistory::create([
            'quote_request_id' => $qr->id,
            'from_status' => $from,
            'to_status' => 'quoted',
            'changed_by_type' => 'vendor',
            'changed_by_id' => $vendor->id,
            'note' => $request->note ?? 'Quote set by vendor',
        ]);

        // Optional: also send a message automatically
        if ($request->note) {
            QuoteMessage::create([
                'quote_request_id' => $qr->id,
                'sender_type' => 'vendor',
                'sender_id' => $vendor->id,
                'message' => $request->note,
            ]);
            $qr->update(['last_message_at' => now()]);
        }

        $qr->addSystemMessage(
            "Vendor updated quote: {$qr->currency} {$qr->quoted_price}" . ($qr->quoted_moq ? " (MOQ: {$qr->quoted_moq})" : ""),
            'quote',
            [
                'quoted_price' => (float) $qr->quoted_price,
                'currency' => $qr->currency,
                'quoted_moq' => $qr->quoted_moq,
            ]
        );
        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Quote updated.', 'data' => $qr]);
    }

    // POST /api/v1/vendor/quotes/{id}/status
    public function updateStatus(VendorUpdateQuoteStatusRequest $request, $id)
    {
        $vendor = auth('vendor')->user();
        $qr = QuoteRequest::where('vendor_id', $vendor->id)->findOrFail($id);

        $from = $qr->status;
        $to = $request->status;

        if($to != 'paid' && $to != 'shipped' && $to != 'closed') {
            return response()->json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        // basic guard (optional strict rules)
        if ($from === 'closed' || $from === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'Cannot change a closed/cancelled quote.'], 422);
        }

        if($to === 'paid' && $from !== 'accepted') {
            return response()->json(['success' => false, 'message' => 'Only accepted quotes can be marked as paid.'], 422);
        }

        if($to === 'shipped' && $from !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Only paid quotes can be marked as shipped.'], 422);
        }

        $qr->status = $to;
        $qr->save();

        QuoteStatusHistory::create([
            'quote_request_id' => $qr->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by_type' => 'vendor',
            'changed_by_id' => $vendor->id,
            'note' => $request->note,
        ]);

        $qr->addSystemMessage(
            "Status changed: {$from} → {$to}",
            'status',
            [
                'from_status' => $from,
                'to_status' => $to,
                'note' => $request->note,
            ]
        );

        $qr->update(['last_message_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Status updated.', 'data' => $qr]);
    }

    public function messages(Request $request, $id)
{
    $vendor = auth('vendor')->user();

    $request->validate([
        'before_id' => ['nullable','integer','min:1'],
        'limit' => ['nullable','integer','min:1','max:50'],
    ]);

    $qr = QuoteRequest::where('vendor_id', $vendor->id)->findOrFail($id);

    $limit = $request->input('limit', 15);

    $q = QuoteMessage::where('quote_request_id', $qr->id)
        ->orderByDesc('id');

    if ($request->filled('before_id')) {
        $q->where('id', '<', (int)$request->before_id);
    }

    $messages = $q->take($limit)->get();

    $messagesAsc = $messages->sortBy('id')->values();
    $nextBeforeId = $messages->count() ? $messages->last()->id : null;

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
    $vendor = auth('vendor')->user();

    $request->validate([
        'last_seen_message_id' => ['required','integer','min:1'],
    ]);

    $qr = QuoteRequest::where('vendor_id', $vendor->id)->findOrFail($id);

    QuoteMessage::where('quote_request_id', $qr->id)
        ->where('id', '<=', (int)$request->last_seen_message_id)
        ->whereNull('seen_by_vendor_at')
        ->whereIn('sender_type', ['user','system']) // only messages vendor receives
        ->update(['seen_by_vendor_at' => now()]);

    return response()->json(['success' => true, 'message' => 'Seen updated.']);
}
}
