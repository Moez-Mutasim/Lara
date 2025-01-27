<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        Log::info('Fetching all bookings', ['user_id' => $request->user()->user_id]);

        $this->authorize('viewAny', Booking::class);

        $query = Booking::query();

        if ($request->has('status')) {
            Log::info('Filtering bookings by status', ['status' => $request->input('status')]);
            $query->where('status', $request->input('status'));
        }

        if ($request->has('sort_by')) {
            Log::info('Sorting bookings', [
                'sort_by' => $request->input('sort_by'),
                'sort_order' => $request->input('sort_order', 'asc')
            ]);
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $perPage = $request->input('per_page', 10);
        $bookings = $query->with(['user', 'flight', 'hotel', 'car'])->paginate($perPage);

        Log::info('Bookings retrieved successfully', ['count' => $bookings->count()]);

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }

    public function show($id)
    {
        Log::info("Fetching booking details", ['booking_id' => $id]);

        $booking = Booking::with(['user', 'flight', 'hotel', 'car'])->findOrFail($id);
        $this->authorize('view', $booking);

        Log::info("Booking details retrieved successfully", ['booking_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $booking], 200);
    }

    public function store(Request $request)
    {
        Log::info("Attempting to create booking", [
            'user_id' => $request->user()->user_id,
            'request_data' => $request->all()
        ]);

        $this->authorize('create', Booking::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'flight_id' => 'nullable|exists:flights,flight_id',
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'car_id' => 'nullable|exists:cars,car_id',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,canceled',
            'booking_date' => 'required|date',
        ]);

        \DB::beginTransaction();
        try {
            $booking = Booking::create($validated);
            \DB::commit();
            Log::info("Booking created successfully", ['booking_id' => $booking->booking_id]);
            return response()->json(['status' => 'success', 'data' => $booking], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("Booking creation failed", ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Booking creation failed'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("Attempting to update booking", ['booking_id' => $id]);

        $booking = Booking::findOrFail($id);
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'flight_id' => 'nullable|exists:flights,flight_id',
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'car_id' => 'nullable|exists:cars,car_id',
            'total_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,canceled',
            'booking_date' => 'nullable|date',
        ]);

        $booking->update($validated);

        Log::info("Booking updated successfully", ['booking_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $booking], 200);
    }

    public function cancel($id)
    {
        Log::info("Attempting to cancel booking", ['booking_id' => $id]);

        $booking = Booking::findOrFail($id);
        $this->authorize('update', $booking);

        if ($booking->status === 'canceled') {
            Log::warning("Booking already canceled", ['booking_id' => $id]);
            return response()->json(['status' => 'error', 'message' => 'Booking is already canceled'], 400);
        }

        $booking->update(['status' => 'canceled']);

        Log::info("Booking canceled successfully", ['booking_id' => $id]);

        return response()->json(['status' => 'success', 'message' => 'Booking canceled successfully'], 200);
    }

    public function destroy($id)
    {
        Log::info("Attempting to delete booking", ['booking_id' => $id]);

        $booking = Booking::findOrFail($id);
        $this->authorize('delete', $booking);

        $booking->delete();

        Log::info("Booking deleted successfully", ['booking_id' => $id]);

        return response()->json(['status' => 'success', 'message' => 'Booking deleted'], 200);
    }
}
