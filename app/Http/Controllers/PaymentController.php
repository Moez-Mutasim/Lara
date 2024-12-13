<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $payments = $query->paginate(10);

        return response()->json($payments, 200);
    }

    public function show($id)
    {
        $payment = Payment::find($id);

        return $payment
            ? response()->json($payment, 200)
            : response()->json(['message' => 'Payment not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,booking_id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string|max:50',
                'payment_status' => 'required|in:pending,completed,failed',
            ]);

            $payment = Payment::create($validated);

            return response()->json($payment, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing the payment.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_status' => 'nullable|in:pending,completed,failed',
        ]);

        $payment->update($validated);

        return response()->json($payment, 200);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted'], 200);
    }

    public function search(Request $request)
    {
        $query = Payment::query();

        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->input('booking_id'));
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(10);

        return response()->json($payments, 200);
    }

    public function markAsCompleted($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->payment_status = 'completed';
        $payment->save();

        return response()->json(['message' => 'Payment marked as completed', 'payment' => $payment], 200);
    }
}
