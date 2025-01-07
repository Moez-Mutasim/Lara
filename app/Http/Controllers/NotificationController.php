<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\NotificationRequest;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Notification::class);

        $query = Notification::query();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id)
                  ->orWhereNull('user_id'); // Include system-wide notifications
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->input('is_read'));
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ], 200);
    }

    public function show($notification_id)
    {
        $notification = Notification::findOrFail($notification_id);

        $this->authorize('view', $notification);

        return response()->json(['status' => 'success', 'data' => $notification], 200);
    }

    public function store(NotificationRequest $request)
    {
        $this->authorize('create', Notification::class);

        $validated = $request->validated();

        $notification = Notification::create($validated);

        return response()->json(['status' => 'success', 'data' => $notification], 201);
    }

    public function update(Request $request, $notification_id)
    {
        $notification = Notification::findOrFail($notification_id);

        $this->authorize('update', $notification);

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
            'type' => 'nullable|string|in:info,warning,error',
            'is_read' => 'nullable|boolean',
        ]);

        if ($request->has('is_read') && $request->is_read) {
            $validated['read_at'] = now();
        }

        $notification->update($validated);

        return response()->json(['status' => 'success', 'data' => $notification], 200);
    }

    public function destroy($notification_id)
    {
        $notification = Notification::findOrFail($notification_id);

        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json(['status' => 'success', 'message' => 'Notification deleted'], 200);
    }

    public function markAsRead($notification_id)
    {
        $notification = Notification::findOrFail($notification_id);

        $this->authorize('update', $notification);

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification,
        ], 200);
    }

    public function markAllAsRead(Request $request)
    {
        $this->authorize('markAllAsRead', Notification::class);

        $notifications = Notification::where('user_id', $request->user()->id)->where('is_read', false);
        $notifications->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read'], 200);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Notification::class);

        $query = Notification::query();

        if ($request->has('search')) {
            $query->where('message', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('type', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $notifications = $query->paginate(10);

        return response()->json(['status' => 'success', 'data' => $notifications], 200);
    }
}
