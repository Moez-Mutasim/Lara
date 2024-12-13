<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function __construct()
    {
        if (!app()->runningUnitTests()) {
            $this->middleware('auth:api');
        }
    }

    public function index(Request $request)
    {
        $query = Flight::query();

        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
        }

        $flights = $query->paginate(10);

        return response()->json($flights, 200);
    }


    public function show($id)
    {
        $flight = Flight::find($id);

        return $flight
            ? response()->json($flight, 200)
            : response()->json(['message' => 'Flight not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'airline_name' => 'required|string|max:255',
            'departure_id' => 'required|exists:locations,location_id',
            'destination_id' => 'required|exists:locations,location_id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
        ]);

        $flight = Flight::create($validated);

        return response()->json($flight, 201);
    }


    public function update(Request $request, $id)
    {
        $flight = Flight::find($id);

        if (!$flight) {
            return response()->json(['message' => 'Flight not found'], 404);
        }

        $validated = $request->validate([
            'airline_name' => 'nullable|string|max:255',
            'departure_id' => 'nullable|exists:locations,location_id',
            'destination_id' => 'nullable|exists:locations,location_id',
            'departure_time' => 'nullable|date',
            'arrival_time' => 'nullable|date|after:departure_time',
            'price' => 'nullable|numeric|min:0',
        ]);

        $flight->update($validated);

        return response()->json($flight, 200);
    }




    public function destroy($id)
    {
        $flight = Flight::find($id);

        if (!$flight) {
            return response()->json(['message' => 'Flight not found'], 404);
        }

        $flight->delete();

        return response()->json(['message' => 'Flight deleted'], 200);
    }



    public function search(Request $request)
    {
        $query = Flight::query();

        if ($request->has('departure_id')) {
            $query->where('departure_id', $request->departure_id);
        }

        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        $flights = $query->paginate(10);

        return response()->json($flights, 200);
    }
    

    public function toggleAvailability($id)
    {
        $flight = Flight::find($id);

        if (!$flight) {
            return response()->json(['message' => 'Flight not found'], 404);
        }

        $flight->is_available = !$flight->is_available;
        $flight->save();

        return response()->json(['message' => 'Flight availability toggled', 'flight' => $flight], 200);
    }
}
