<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the home page.
     */
    public function index(Request $request)
    {
        try {
            // Log user's access
            \Log::info('Home page accessed by user', ['user_id' => auth()->id()]);

            $data = [
                'title' => 'Welcome to the Home Page',
                'user' => auth()->user(),
            ];

            // If the request is AJAX, return JSON response
            if ($request->ajax()) {
                return response()->json(['message' => 'Welcome to the Home Page', 'data' => $data]);
            }

            // Otherwise, return the home view
            return view('home', $data);
        } catch (\Exception $e) {
            \Log::error('Error loading home view', ['error' => $e->getMessage()]);

            // Return an error view or response
            return response()->view('errors.500', [], 500);
        }
    }
}
