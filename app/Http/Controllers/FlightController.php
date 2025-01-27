<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FlightController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'available']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Flight::class);

        $query = Flight::query();

        if ($request->has('airline_name')) {
            $query->where('airline_name', 'like', '%' . $request->input('airline_name') . '%');
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price', [
                $request->input('min_price'),
                $request->input('max_price'),
            ]);
        }

        if ($request->has('class')) {
            $query->where('class', $request->input('class'));
        }

        if ($request->has('departure_id')) {
            $query->where('departure_id', $request->input('departure_id'));
        }

        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->input('destination_id'));
        }

        if ($request->has('is_available')) {
            $query->where('is_available', $request->input('is_available'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
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
            'departure_id'   => 'required|exists:locations,location_id|different:destination_id',
            'destination_id' => 'required|exists:locations,location_id',
            'airline_name'   => 'required|string|max:255',
            'departure_time' => 'required|date',
            'arrival_time'   => 'required|date|after:departure_time',
            'price'          => 'required|numeric|min:0',
            'class'          => 'required|in:Economy,Business,First',
            'seats_available'=> 'required|integer|min:0',
            'is_available'   => 'nullable|boolean',
            'flight_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('flight_image')) {
            $validated['flight_image'] = $request->file('flight_image')->store('images/flights', 'public');
        }

        $flight = Flight::create($validated);

        Log::info("Flight {$flight->flight_id} created by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Flight Created Successfully', 'data' => $flight], 201);
    }

    public function update(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('update', $flight);

        $validated = $request->validate([
            'departure_id'   => 'sometimes|required|exists:locations,location_id|different:destination_id',
            'destination_id' => 'sometimes|required|exists:locations,location_id',
            'airline_name'   => 'sometimes|required|string|max:255',
            'departure_time' => 'sometimes|required|date',
            'arrival_time'   => 'sometimes|required|date|after:departure_time',
            'price'          => 'sometimes|required|numeric|min:0',
            'class'          => 'sometimes|required|in:Economy,Business,First',
            'seats_available'=> 'sometimes|required|integer|min:0',
            'is_available'   => 'sometimes|nullable|boolean',
            'flight_image'   => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('flight_image')) {
            if ($flight->flight_image) {
                Storage::disk('public')->delete($flight->flight_image);
            }
            $validated['flight_image'] = $request->file('flight_image')->store('images/flights', 'public');
        }

        $flight->update($validated);

        Log::info("Flight {$flight->flight_id} updated by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Flight Updated Successfully', 'data' => $flight], 200);
    }

    public function destroy($id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('delete', $flight);

        if ($flight->flight_image) {
            Storage::disk('public')->delete($flight->flight_image);
        }

        $flight->delete();

        Log::info("Flight {$flight->flight_id} deleted by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Flight Deleted Successfully'], 200);
    }

    public function toggleAvailability($id)
    {
        $flight = Flight::findOrFail($id);
        $this->authorize('update', $flight);

        $flight->is_available = !$flight->is_available;
        $flight->save();

        Log::info("Flight {$flight->flight_id} availability toggled by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Flight Availability Toggled Successfully', 'data' => $flight], 200);
    }

    public function available()
    {
        $this->authorize('viewAny', Flight::class);

        $flights = Flight::where('is_available', true)->paginate(10);

        return response()->json(['status' => 'success', 'data' => $flights], 200);
    }
}
