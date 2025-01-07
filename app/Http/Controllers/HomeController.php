<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $isGuest = is_null($user);

            $data = [
                'title' => 'Welcome to Bookins',
                'user' => $user,
                'is_guest' => $isGuest,
                'features' => $this->getAvailableFeatures($isGuest),
            ];

            if ($request->ajax()) {
                return $this->jsonResponse($data, 'Welcome to Bookins');
            }

            return view('home', $data);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Failed to load the home page');
        }
    }

    private function getAvailableFeatures($isGuest)
    {
        $features = [
            'flights' => route('flights.index'),
            'hotels' => route('hotels.index'),
            'cars' => route('cars.index'),
        ];

        if (!$isGuest) {
            $features['profile'] = route('profile.index');
            $features['bookings'] = route('bookings.index');
        }

        return $features;
    }
}
