<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\NotificationRequest;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $notifications = $query->paginate(10);

        return response()->json($notifications, 200);
    }

    public function show($notification_id)
    {
        $notification = Notification::find($notification_id);

        return $notification
            ? response()->json($notification, 200)
            : response()->json(['message' => 'Notification not found'], 404);
    }

    public function store(NotificationRequest $request)
    {
        try {
            $validated = $request->validated();

            \Log::info('Creating notification with data: ', $validated);

            $notification = Notification::create($validated);

            return response()->json(['data' => $notification], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the notification.'], 500);
        }
    }

    public function update(Request $request, $notification_id)
    {
        $notification = Notification::find($notification_id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,user_id',
            'message' => 'nullable|text',
            'is_read' => 'nullable|boolean',
           // 'status' => 'nullable|boolean',
        ]);

        $notification->update($validated);

        return response()->json($notification, 200);
    }

    public function destroy($notification_id)
    {
        $notification = Notification::find($notification_id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted'], 200);
    }

    public function markAsRead($notification_id)
    {
        $notification = Notification::find($notification_id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read', 'notification' => $notification], 200);
    }

    public function search(Request $request)
    {
        $query = Notification::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('content', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $notifications = $query->paginate(10);

        return response()->json($notifications, 200);
    }
}
