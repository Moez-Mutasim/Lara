<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

    }

    protected function jsonResponse($data = [], $message = '', $status = 200, $code = null)
    {
        return response()->json([
            'status' => $status < 400 ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
            'code' => $code ?? ($status < 400 ? 0 : 1),
        ], $status);
    }

    protected function jsonError($message = 'An error occurred', $status = 400, $errors = [], $code = 1)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
        ], $status);
    }

    protected function handleException(\Throwable $e, $message = 'An unexpected error occurred', $status = 500)
    {
        Log::error($e->getMessage(), ['exception' => $e]);

        return $this->jsonError($message, $status, [
            'exception' => config('app.debug') ? $e->getMessage() : null,
        ]);
    }

    protected function validateRequest($request, array $rules, array $messages = [])
    {
        try {
            return $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->jsonError('Validation failed', 422, $e->errors());
        }
    }

    protected function unauthorizedResponse($message = 'Unauthorized', $code = 403)
    {
        return $this->jsonError($message, 403, [], $code);
    }

    protected function notFoundResponse($message = 'Resource not found', $code = 404)
    {
        return $this->jsonError($message, 404, [], $code);
    }

    protected function paginatedResponse($query, $perPage = 10, $message = 'Data retrieved successfully')
    {
        $paginatedData = $query->paginate($perPage);

        return $this->jsonResponse([
            'items' => $paginatedData->items(),
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
            ],
        ], $message);
    }
}
