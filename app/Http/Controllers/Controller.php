<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function jsonResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function logEvent($message, $context = [])
    {
        \Log::info($message, $context);
    }

    protected function handleException(\Throwable $e)
    {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }

    protected function apiSuccess($data, $message = 'Operation successful', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function apiError($message = 'Operation failed', $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
