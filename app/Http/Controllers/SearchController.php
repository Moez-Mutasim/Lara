<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Car;

class SearchController extends Controller
{
    public function searchFlights(Request $request)
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

        return $this->paginatedResponse($query, 10, 'Flights retrieved successfully.');
    }

    public function searchHotels(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price_per_night', [$request->min_price, $request->max_price]);
        }

        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        return $this->paginatedResponse($query, 10, 'Hotels retrieved successfully.');
    }

    public function searchCars(Request $request)
    {
        $query = Car::query();

        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }

        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('rental_price', [$request->min_price, $request->max_price]);
        }

        return $this->paginatedResponse($query, 10, 'Cars retrieved successfully.');
    }
}
