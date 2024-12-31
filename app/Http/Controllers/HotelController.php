<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function __construct()
    {
        // Uncomment the middleware for authentication when required
        // $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->has('min_price')) {
        $query->where('price_per_night', '>=', $request->input('min_price'));
    }

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
        }

        $hotels = $query->paginate(10);

        $hotels->getCollection()->transform(function ($hotel) {
            $hotel->image = url($hotel->image);
            return $hotel;
        });

        return response()->json($hotels, 200);
    }

    public function show($id)
    {
        $hotel = Hotel::find($id);

    if ($hotel) {
        $hotel->image = url($hotel->image);
        return response()->json($hotel, 200);
    } else {
        return response()->json(['message' => 'Hotel not found'], 404);
    }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'availability' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images/hotels', 'public');
        }

        $hotel = Hotel::create($validated);

        return response()->json($hotel, 201);
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
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }

            $validated['image'] = $request->file('image')->store('images/hotels', 'public');
        }

        $hotel->update($validated);

        return response()->json($hotel, 200);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        if ($hotel->image) {
            Storage::disk('public')->delete($hotel->image);
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
