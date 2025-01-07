<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query();

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->has('currency')) {
            $query->where('currency', $request->input('currency'));
        }

        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->input('booking_id'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $payments = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ], 200);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);

        $this->authorize('view', $payment);

        return response()->json(['status' => 'success', 'data' => $payment], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Payment::class);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'transaction_fee' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $validated['payment_reference'] = uniqid('PAY_');

        $payment = Payment::create($validated);

        return response()->json(['status' => 'success', 'data' => $payment], 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $this->authorize('update', $payment);

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_status' => 'nullable|in:pending,completed,failed,refunded',
            'transaction_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
        ]);

        $payment->update($validated);

        return response()->json(['status' => 'success', 'data' => $payment], 200);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        $this->authorize('delete', $payment);

        $payment->delete();

        return response()->json(['status' => 'success', 'message' => 'Payment deleted'], 200);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,booking_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'currency' => 'nullable|string|size:3',
        ]);

        $query = Payment::query();

        if ($request->has('booking_id')) {
            $query->where('booking_id', $validated['booking_id']);
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
        }

        if ($request->has('currency')) {
            $query->where('currency', $validated['currency']);
        }

        $payments = $query->paginate($request->input('per_page', 10));

        return response()->json(['status' => 'success', 'data' => $payments], 200);
    }

    public function markAsCompleted($id)
    {
        $payment = Payment::findOrFail($id);

        $this->authorize('update', $payment);

        $payment->update(['payment_status' => 'completed', 'paid_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'Payment marked as completed', 'data' => $payment], 200);
    }
}
