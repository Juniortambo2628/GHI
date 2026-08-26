<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AdminNotification::forUser($request->user()->id)
            ->latest()
            ->paginate(15);

        $unreadCount = AdminNotification::forUser($request->user()->id)
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(int $id): JsonResponse
    {
        $notification = AdminNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        AdminNotification::forUser($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = AdminNotification::forUser($request->user()->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}
