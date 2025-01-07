<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlightController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Flight::class);

        $query = Flight::query();

        if ($request->has('departure_id')) {
            $query->where('departure_id', $request->departure_id);
        }
        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->has('class')) {
            $query->where('class', $request->class);
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
        }

        $flights = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $flights,
            'meta' => [
                'total' => $flights->total(),
                'per_page' => $flights->perPage(),
                'current_page' => $flights->currentPage(),
            ],
        ], 200);
    }

    public function show($id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('view', $flight);

        return response()->json(['status' => 'success', 'data' => $flight], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Flight::class);

        $validated = $request->validate([
            'airline_name' => 'required|string|max:255',
            'departure_id' => 'required|exists:locations,location_id',
            'destination_id' => 'required|exists:locations,location_id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'class' => 'required|in:Economy,Business,First',
            'seats_available' => 'required|integer|min:0',
            'is_available' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images/flights', 'public');
        }

        $flight = Flight::create($validated);

        return response()->json(['status' => 'success', 'data' => $flight], 201);
    }

    public function update(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('update', $flight);

        $validated = $request->validate([
            'airline_name' => 'nullable|string|max:255',
            'departure_id' => 'nullable|exists:locations,location_id',
            'destination_id' => 'nullable|exists:locations,location_id',
            'departure_time' => 'nullable|date',
            'arrival_time' => 'nullable|date|after:departure_time',
            'price' => 'nullable|numeric|min:0',
            'class' => 'nullable|in:Economy,Business,First',
            'seats_available' => 'nullable|integer|min:0',
            'is_available' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($flight->image) {
                Storage::disk('public')->delete($flight->image);
            }
            $validated['image'] = $request->file('image')->store('images/flights', 'public');
        }

        $flight->update($validated);

        return response()->json(['status' => 'success', 'data' => $flight], 200);
    }

    public function destroy($id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('delete', $flight);

        if ($flight->image) {
            Storage::disk('public')->delete($flight->image);
        }

        $flight->delete();

        return response()->json(['status' => 'success', 'message' => 'Flight deleted'], 200);
    }

    public function toggleAvailability($id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('update', $flight);

        $flight->is_available = !$flight->is_available;
        $flight->save();

        return response()->json(['status' => 'success', 'message' => 'Flight availability toggled', 'flight' => $flight], 200);
    }
}
