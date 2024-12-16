<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $bookings = $query->paginate(10);

        return response()->json([
            'data' => $bookings,
        ]);
    }

    public function show($id)
    {
        $bookings = Booking::with(['user', 'flight', 'hotel', 'car'])->find($id);

        return $bookings
            ? response()->json($bookings, 200)
            : response()->json(['message' => 'Booking not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'flight_id' => 'nullable|exists:flights,flight_id',
                'hotel_id' => 'nullable|exists:hotels,hotel_id',
                'car_id' => 'nullable|exists:cars,car_id',
                'total_price' => 'required|numeric|min:0',
                'status' => 'required|in:pending,confirmed,canceled',
            ]);

            $bookings = Booking::create($validated);

            return response()->json($bookings, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the booking.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $bookings = Booking::find($id);
        if (!$bookings) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $request->validate([
            'flight_id' => 'nullable|exists:flights,flight_id',
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'car_id' => 'nullable|exists:cars,car_id',
            'total_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,canceled',
        ]);

        $bookings->update($request->all());

        return response()->json($bookings, 200);
    }

    public function destroy($id)
    {
        $bookings = Booking::find($id);
        if (!$bookings) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $bookings->delete();

        return response()->json(['message' => 'Booking deleted'], 200);
    }
}
