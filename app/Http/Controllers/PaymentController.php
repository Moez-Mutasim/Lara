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
        Log::info('Fetching all payments', ['user_id' => $request->user()->id]);

        $this->authorize('viewAny', Payment::class);

        $query = Payment::query();

        if ($request->has('payment_status')) {
            Log::info('Filtering payments by status', ['status' => $request->input('payment_status')]);
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

        Log::info('Payments retrieved successfully', ['count' => $payments->count()]);

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ], 200);
    }

    public function show($id)
    {
        Log::info('Fetching payment details', ['payment_id' => $id]);

        $payment = Payment::findOrFail($id);
        $this->authorize('view', $payment);

        Log::info('Payment details retrieved successfully', ['payment_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $payment], 200);
    }

    public function store(Request $request)
    {
        Log::info('Attempting to create a new payment', ['user_id' => $request->user()->id]);

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

        Log::info('Payment created successfully', ['payment_id' => $payment->id]);

        return response()->json(['status' => 'success', 'data' => $payment], 201);
    }

    public function update(Request $request, $id)
    {
        Log::info('Attempting to update payment', ['payment_id' => $id]);

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

        Log::info('Payment updated successfully', ['payment_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $payment], 200);
    }

    public function destroy($id)
{
    try {
        $user = auth()->user();
        
        Log::info('Attempting to delete payment', [
            'user_id' => $user->user_id ?? 'guest',
            'payment_id' => $id
        ]);

        $payment = Payment::findOrFail($id);
        $this->authorize('delete', $payment);

        $payment->delete();

        Log::info('Payment deleted successfully', [
            'user_id' => $user->user_id ?? 'guest',
            'payment_id' => $id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment deleted successfully'
        ], 200);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error('Payment not found', [
            'user_id' => $user->user_id ?? 'guest',
            'payment_id' => $id
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Payment not found'
        ], 404);

    } catch (\Exception $e) {
        Log::error('Error deleting payment', [
            'user_id' => $user->user_id ?? 'guest',
            'payment_id' => $id,
            'error_message' => $e->getMessage()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete payment'
        ], 500);
    }
}

    public function search(Request $request)
    {
        Log::info('Searching for payments', ['query' => $request->all()]);

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

        Log::info('Payment search completed', ['results_found' => $payments->count()]);

        return response()->json(['status' => 'success', 'data' => $payments], 200);
    }

    public function markAsCompleted($id)
    {
        Log::info('Marking payment as completed', ['payment_id' => $id]);

        $payment = Payment::findOrFail($id);
        $this->authorize('update', $payment);

        $payment->update(['payment_status' => 'completed', 'paid_at' => now()]);

        Log::info('Payment marked as completed successfully', ['payment_id' => $id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment marked as completed',
            'data' => $payment,
        ], 200);
    }
}
