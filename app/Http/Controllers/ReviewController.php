<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->has('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            if (in_array($type, ['flight', 'hotel', 'car'])) {
                $query->whereNotNull("{$type}_id");
            }
        }

        $reviews = $query->paginate(10);

        return response()->json($reviews, 200);
    }

    public function show($id)
    {
        $review = Review::find($id);

        return $review
            ? response()->json($review, 200)
            : response()->json(['message' => 'Review not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'flight_id' => 'nullable|exists:flights,flight_id',
                'hotel_id' => 'nullable|exists:hotels,hotel_id',
                'car_id' => 'nullable|exists:cars,car_id',
                'rating' => 'required|numeric|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $review = Review::create($validated);

            return response()->json($review, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the review.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json($review, 200);
    }

    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted'], 200);
    }

    public function search(Request $request)
    {
        $query = Review::query();

        if ($request->has('comment')) {
            $query->where('comment', 'like', '%' . $request->input('comment') . '%');
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $reviews = $query->paginate(10);

        return response()->json($reviews, 200);
    }
}
