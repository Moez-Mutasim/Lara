<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        Log::info('Fetching reviews', ['user_id' => $request->user()->user_id ?? 'Guest']);

        $this->authorize('viewAny', Review::class);

        $query = Review::query();

        if ($request->filled('rating')) {
            Log::info('Filtering reviews by rating', ['rating' => $request->input('rating')]);
            $query->where('rating', '>=', $request->input('rating'));
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            if (in_array($type, ['flight', 'hotel', 'car'])) {
                Log::info('Filtering reviews by type', ['type' => $type]);
                $query->whereNotNull("{$type}_id");
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->input('is_verified'));
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        Log::info('Reviews fetched successfully', ['total' => $reviews->total()]);

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ], 200);
    }

    public function show($id)
    {
        Log::info("Fetching review details", ['review_id' => $id]);

        $review = Review::findOrFail($id);
        $this->authorize('view', $review);

        Log::info("Review details retrieved successfully", ['review_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $review], 200);
    }

    public function store(Request $request)
    {
        Log::info('Attempting to create a review', ['user_id' => $request->user()->user_id]);

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

        Log::info("Review created successfully", ['review_id' => $review->review_id, 'user_id' => $review->user_id]);

        return response()->json(['status' => 'success', 'data' => $review], 201);
    }

    public function update(Request $request, $id)
    {
        Log::info("Attempting to update review", ['review_id' => $id]);

        $review = Review::findOrFail($id);
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_verified' => 'nullable|boolean',
        ]);

        $review->update($validated);

        Log::info("Review updated successfully", ['review_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $review], 200);
    }

    public function destroy($id)
    {
        Log::info("Attempting to delete review", ['review_id' => $id]);

        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);

        $review->delete();

        Log::info("Review deleted successfully", ['review_id' => $id]);

        return response()->json(['status' => 'success', 'message' => 'Review deleted'], 200);
    }

    public function search(Request $request)
    {
        Log::info("Searching reviews", ['query' => $request->all()]);

        $this->authorize('viewAny', Review::class);

        $query = Review::query();

        if ($request->filled('comment')) {
            $query->where('comment', 'like', '%' . $request->input('comment') . '%');
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->input('rating'));
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->input('is_verified'));
        }

        $reviews = $query->paginate(10);

        Log::info("Search completed successfully", ['results_found' => $reviews->total()]);

        return response()->json(['status' => 'success', 'data' => $reviews], 200);
    }
}
