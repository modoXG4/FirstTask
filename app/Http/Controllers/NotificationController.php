<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $unreadCount = $request->user()->unreadNotifications()->count();

        if ($request->expectsJson()) {
            return response()->json($notifications);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Marked as read']);
        }

        return back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'All marked as read']);
        }

        return back()->with('success', 'تم تحديد كل الإشعارات كمقروءة.');
    }
}
