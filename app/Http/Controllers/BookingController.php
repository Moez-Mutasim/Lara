<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $bookings = $query->with(['user', 'flight', 'hotel', 'car'])->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'flight', 'hotel', 'car'])->findOrFail($id);
        $this->authorize('view', $booking);

        return response()->json(['status' => 'success', 'data' => $booking], 200);
    }

    public function store(Request $request)
    {
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

        $booking = Booking::create($validated);

        return response()->json(['status' => 'success', 'data' => $booking], 201);
    }

    public function update(Request $request, $id)
    {
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

        return response()->json(['status' => 'success', 'data' => $booking], 200);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('delete', $booking);

        $booking->delete();

        return response()->json(['status' => 'success', 'message' => 'Booking deleted'], 200);
    }

    public function getUserBookings(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewUserBookings', [Booking::class, $user]);

        $bookings = Booking::where('user_id', $user->user_id)
            ->with(['flight', 'hotel', 'car'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }
}
