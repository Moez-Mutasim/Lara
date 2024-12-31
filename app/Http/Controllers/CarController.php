<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function __construct()
    {
        // Uncomment if authentication is required
        // $this->middleware('auth:sanctum');
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



    public function search(Request $request)
    {
        $query = Car::query();

        // Filter by brand
        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->input('brand') . '%');
        }

        // Filter by price range
        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('rental_price', [
                $request->input('min_price'),
                $request->input('max_price'),
            ]);
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
                'image' => 'nullable|image|max:2048', // Image validation
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('images/cars', 'public');
            }

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
            'image' => 'nullable|image|max:2048', // Image validation
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }

            $validated['image'] = $request->file('image')->store('images/cars', 'public');
        }

        $car->update($validated);

        return response()->json($car, 200);
    }

    public function destroy($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        // Delete image if exists
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
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
