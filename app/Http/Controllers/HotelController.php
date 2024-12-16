<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct()
    { 
        //$this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
        }

        $hotels = $query->paginate(10);

        return response()->json($hotels, 200);
    }

    public function show($id)
    {
        $hotel = Hotel::find($id);

        return $hotel
            ? response()->json($hotel, 200)
            : response()->json(['message' => 'Hotel not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'price_per_night' => 'required|numeric|min:0',
                'availability' => 'required|boolean',
            ]);

            $hotel = Hotel::create($validated);

            return response()->json($hotel, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the hotel.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'price_per_night' => 'nullable|numeric|min:0',
            'availability' => 'nullable|boolean',
        ]);

        $hotel->update($validated);

        return response()->json($hotel, 200);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        $hotel->delete();

        return response()->json(['message' => 'Hotel deleted'], 200);
    }

    public function toggleAvailability($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        $hotel->availability = !$hotel->availability;
        $hotel->save();

        return response()->json(['message' => 'Hotel availability toggled', 'hotel' => $hotel], 200);
    }

    public function search(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price_per_night', [$request->min_price, $request->max_price]);
        }

        $hotels = $query->paginate(10);

        return response()->json($hotels, 200);
    }
}
