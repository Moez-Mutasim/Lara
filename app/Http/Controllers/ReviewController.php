<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Review::class);

        $query = Review::query();

        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->input('rating'));
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            if (in_array($type, ['flight', 'hotel', 'car'])) {
                $query->whereNotNull("{$type}_id");
            }
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->input('is_verified'));
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ], 200);
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('view', $review);

        return response()->json(['status' => 'success', 'data' => $review], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Review::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'flight_id' => 'nullable|exists:flights,flight_id',
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'car_id' => 'nullable|exists:cars,car_id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create($validated);

        return response()->json(['status' => 'success', 'data' => $review], 201);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_verified' => 'nullable|boolean',
        ]);

        $review->update($validated);

        return response()->json(['status' => 'success', 'data' => $review], 200);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(['status' => 'success', 'message' => 'Review deleted'], 200);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Review::class);

        $query = Review::query();

        if ($request->has('comment')) {
            $query->where('comment', 'like', '%' . $request->input('comment') . '%');
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $reviews = $query->paginate(10);

        return response()->json(['status' => 'success', 'data' => $reviews], 200);
    }
}
