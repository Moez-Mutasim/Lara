<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Hotel::class);

        $query = Hotel::query();

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price_per_night', [$request->min_price, $request->max_price]);
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->has('amenities')) {
            foreach ($request->input('amenities') as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
        }

        $hotels = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $hotels,
            'meta' => [
                'total' => $hotels->total(),
                'per_page' => $hotels->perPage(),
                'current_page' => $hotels->currentPage(),
            ],
        ], 200);
    }

    public function show($id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
            $this->authorize('view', $hotel);

            return response()->json(['status' => 'success', 'data' => $hotel], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Hotel with ID {$id} not found.");
            return response()->json(['status' => 'error', 'message' => 'Hotel not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Hotel::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:locations,location_id',
            'price_per_night' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|between:1,5',
            'amenities' => 'nullable|array',
            'availability' => 'required|boolean',
            'rooms_available' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleImageUpload($request);
        }

        $hotel = Hotel::create($validated);

        Log::info("Hotel {$hotel->id} created by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Hotel Created Successfully', 'data' => $hotel], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
            $this->authorize('update', $hotel);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'city_id' => 'nullable|exists:locations,location_id',
                'price_per_night' => 'nullable|numeric|min:0',
                'rating' => 'nullable|numeric|between:1,5',
                'amenities' => 'nullable|array',
                'availability' => 'nullable|boolean',
                'rooms_available' => 'nullable|integer|min:0',
                'image' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('image')) {
                if ($hotel->image) {
                    Storage::disk('public')->delete($hotel->image);
                }
                $validated['image'] = $this->handleImageUpload($request);
            }

            $hotel->update($validated);

            Log::info("Hotel {$hotel->id} updated by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Hotel Updated Successfully', 'data' => $hotel], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Hotel not found'], 404);
        } catch (\Exception $e) {
            Log::error("Hotel update failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update hotel'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
            $this->authorize('delete', $hotel);

            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }

            $hotel->delete();

            Log::info("Hotel {$hotel->id} deleted by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Hotel Deleted Successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Hotel not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete hotel'], 500);
        }
    }

    public function toggleAvailability($id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
            $this->authorize('update', $hotel);

            $hotel->availability = !$hotel->availability;
            $hotel->save();

            Log::info("Hotel {$hotel->id} availability toggled by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Hotel Availability Toggled Successfully', 'data' => $hotel], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Hotel not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to toggle availability'], 500);
        }
    }

    private function handleImageUpload(Request $request, Hotel $hotel = null)
    {
        if ($request->hasFile('image')) {
            if ($hotel && $hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            return $request->file('image')->store('images/hotels', 'public');
        }
        return $hotel ? $hotel->image : null;
    }
}
