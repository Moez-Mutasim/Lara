<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
//use App\Http\Requests\NotificationRequest;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        Log::info("Fetching notifications", ['user_id' => $request->user()->id ?? 'Guest']);

        $this->authorize('viewAny', Notification::class);

        $query = Notification::query();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id)
                  ->orWhereNull('user_id');
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->input('is_read'));
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        Log::info("Notifications fetched successfully", ['total' => $notifications->total()]);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ], 200);
    }

    public function show($notification_id)
    {
        Log::info("Fetching notification details", ['notification_id' => $notification_id]);

        $notification = Notification::findOrFail($notification_id);
        $this->authorize('view', $notification);

        Log::info("Notification details retrieved successfully", ['notification_id' => $notification_id]);

        return response()->json(['status' => 'success', 'data' => $notification], 200);
    }

    public function store(Request $request)
    {
        Log::info("Creating new notification", ['request_data' => $request->all()]);

        $this->authorize('create', Notification::class);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,user_id',
            'message' => 'required|string|max:500',
            'type' => 'required|string|in:info,warning,error',
            'is_read' => 'nullable|boolean',
        ]);

        $notification = Notification::create($validated);

        Log::info("Notification created successfully", ['notification_id' => $notification->id]);

        return response()->json(['status' => 'success', 'data' => $notification], 201);
    }

    public function update(Request $request, $notification_id)
    {
        Log::info("Updating notification", ['notification_id' => $notification_id, 'request_data' => $request->all()]);

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

        Log::info("Notification updated successfully", ['notification_id' => $notification_id]);

        return response()->json(['status' => 'success', 'data' => $notification], 200);
    }

    public function destroy($notification_id)
    {
        Log::info("Deleting notification", ['notification_id' => $notification_id]);

        $notification = Notification::findOrFail($notification_id);
        $this->authorize('delete', $notification);

        $notification->delete();

        Log::info("Notification deleted successfully", ['notification_id' => $notification_id]);

        return response()->json(['status' => 'success', 'message' => 'Notification deleted'], 200);
    }

    public function markAsRead($notification_id)
    {
        Log::info("Marking notification as read", ['notification_id' => $notification_id]);

        $notification = Notification::findOrFail($notification_id);
        $this->authorize('update', $notification);

        $notification->update(['is_read' => true, 'read_at' => now()]);

        Log::info("Notification marked as read successfully", ['notification_id' => $notification_id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification,
        ], 200);
    }

    public function markAllAsRead(Request $request)
    {
        Log::info("Marking all notifications as read for user", ['user_id' => $request->user()->user_id]);

        $this->authorize('markAllAsRead', Notification::class);

        $notifications = Notification::where('user_id', $request->user()->user_id)->where('is_read', false);
        $notifications->update(['is_read' => true, 'read_at' => now()]);

        Log::info("All notifications marked as read for user", ['user_id' => $request->user()->user_id]);

        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read'], 200);
    }

    public function search(Request $request)
    {
        Log::info("Searching notifications", ['query' => $request->all()]);

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

        Log::info("Search completed successfully", ['results_found' => $notifications->total()]);

        return response()->json(['status' => 'success', 'data' => $notifications], 200);
    }
}
