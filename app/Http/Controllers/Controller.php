<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // Add any global middleware here if needed.
    }

    /**
     * Return a JSON response for success.
     */
    protected function jsonResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Log an event.
     */
    protected function logEvent($message, $context = [])
    {
        \Log::info($message, $context);
    }

    /**
     * Handle an exception and return a JSON response.
     */
    protected function handleException(\Throwable $e)
    {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }

    /**
     * Return a success API response.
     */
    protected function apiSuccess($data, $message = 'Operation successful', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error API response.
     */
    protected function apiError($message = 'Operation failed', $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
