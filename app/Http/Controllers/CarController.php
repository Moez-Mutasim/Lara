<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'available']);
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
        try {
            $car = Car::findOrFail($id);
            $this->authorize('view', $car);

            return response()->json(['status' => 'success', 'data' => $car], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Car not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Car::class);

        $validated = $request->validate([
            'model' => 'required|integer',
            'brand' => 'required|string|max:255',
            'rental_price' => 'required|numeric|min:0',
            'availability' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->handleImageUpload($request);
        }

        $car = Car::create($validated);
        Log::info("Car {$car->car_id} created by User: " . auth()->user()->name);

        return response()->json(['status' => 'success', 'message' => 'Car Created Successfully', 'data' => $car], 201);
    }

    public function update(Request $request, $id)
    {
        try {
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
                $validated['image'] = $this->handleImageUpload($request);
            }

            $car->update($validated);
            Log::info("Car {$car->car_id} updated by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Car Updated Successfully', 'data' => $car], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Car not found'], 404);
        } catch (\Exception $e) {
            Log::error("Car update failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update car'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $car = Car::findOrFail($id);
            $this->authorize('delete', $car);

            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }

            $car->delete();
            Log::info("Car {$car->car_id} deleted by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Car Deleted Successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Car not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete car'], 500);
        }
    }

    public function toggleAvailability($id)
    {
        try {
            $car = Car::findOrFail($id);
            $this->authorize('update', $car);

            $car->availability = !$car->availability;
            $car->save();
            Log::info("Car {$car->car_id} availability toggled by User: " . auth()->user()->name);

            return response()->json(['status' => 'success', 'message' => 'Car Availability Toggled Successfully', 'data' => $car], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Car not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to toggle availability'], 500);
        }
    }

    public function available()
    {
        $this->authorize('viewAny', Car::class);

        $cars = Car::where('availability', true)->paginate(10);

        return response()->json(['status' => 'success', 'data' => $cars], 200);
    }

    private function handleImageUpload(Request $request, Car $car = null)
    {
        if ($request->hasFile('image')) {
            if ($car && $car->image) {
                Storage::disk('public')->delete($car->image);
            }
            return $request->file('image')->store('images/cars', 'public');
        }
        return $car ? $car->image : null;
    }
}
