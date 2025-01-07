<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Car::class);

        $query = Car::query();

        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->input('brand') . '%');
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('rental_price', [
                $request->input('min_price'),
                $request->input('max_price'),
            ]);
        }

        if ($request->has('availability')) {
            $query->where('availability', $request->input('availability'));
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        if ($request->has('features')) {
            foreach ($request->input('features') as $feature) {
                $query->whereJsonContains('features', $feature);
            }
        }

        $cars = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $cars,
            'meta' => [
                'total' => $cars->total(),
                'per_page' => $cars->perPage(),
                'current_page' => $cars->currentPage(),
            ],
        ], 200);
    }

    public function show($id)
    {
        $car = Car::findOrFail($id);
        $this->authorize('view', $car);

        return response()->json(['status' => 'success', 'data' => $car], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Car::class);

        $validated = $request->validate([
            'model' => 'required|integer',
            'brand' => 'required|string|max:255',
            'rental_price' => 'required|numeric|min:0',
            'availability' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images/cars', 'public');
        }

        $car = Car::create($validated);

        return response()->json(['status' => 'success', 'data' => $car], 201);
    }

    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        $this->authorize('update', $car);

        $validated = $request->validate([
            'model' => 'nullable|integer',
            'brand' => 'nullable|string|max:255',
            'rental_price' => 'nullable|numeric|min:0',
            'availability' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }

            $validated['image'] = $request->file('image')->store('images/cars', 'public');
        }

        $car->update($validated);

        return response()->json(['status' => 'success', 'data' => $car], 200);
    }

    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $this->authorize('delete', $car);

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return response()->json(['status' => 'success', 'message' => 'Car deleted'], 200);
    }

    public function toggleAvailability($id)
    {
        $car = Car::findOrFail($id);
        $this->authorize('update', $car);

        $car->availability = !$car->availability;
        $car->save();

        return response()->json(['status' => 'success', 'message' => 'Car availability toggled', 'data' => $car], 200);
    }

    public function available()
    {
        $this->authorize('viewAny', Car::class);

        $cars = Car::where('availability', true)->paginate(10);

        return response()->json(['status' => 'success', 'data' => $cars], 200);
    }
}
