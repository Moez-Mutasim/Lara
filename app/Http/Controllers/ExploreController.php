<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Car;

class ExploreController extends Controller
{
    public function index()
    {
        Gate::authorize('viewExplore');

        $flights = Flight::orderBy('rating', 'desc')->take(5)->get();
        $hotels = Hotel::orderBy('rating', 'desc')->take(5)->get();
        $cars = Car::orderBy('rental_price', 'asc')->take(5)->get();

        return $this->jsonResponse([
            'featured_flights' => $flights,
            'top_rated_hotels' => $hotels,
            'affordable_cars' => $cars,
        ], 'Explore data retrieved successfully.');
    }
}
