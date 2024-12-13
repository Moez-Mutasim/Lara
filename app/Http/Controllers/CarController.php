<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Car::query();

        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->input('brand') . '%');
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $cars = $query->paginate(10);

        return response()->json($cars, 200);
    }

    public function show($id)
    {
        $car = Car::find($id);

        return $car
            ? response()->json($car, 200)
            : response()->json(['message' => 'Car not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'model' => 'required|integer',
                'brand' => 'required|string|max:255',
                'rental_price' => 'required|numeric|min:0',
                'availability' => 'required|boolean',
            ]);

            $car = Car::create($validated);

            return response()->json($car, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the car.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $validated = $request->validate([
            'model' => 'nullable|integer',
            'brand' => 'nullable|string|max:255',
            'rental_price' => 'nullable|numeric|min:0',
            'availability' => 'nullable|boolean',
        ]);

        $car->update($validated);

        return response()->json($car, 200);
    }

    public function destroy($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted'], 200);
    }

    public function toggleAvailability($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $car->availability = !$car->availability;
        $car->save();

        return response()->json(['message' => 'Car availability toggled', 'car' => $car], 200);
    }
}
