<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use App\Traits\ExpectsJsonTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait, ExpectsJsonTrait;

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $unreadCount = $request->user()->unreadNotifications()->count();

        return $this->respondBasedOnRequest($request, [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ], 'notifications.index', __('notifications.list_success'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->respondBasedOnRequest($request, null, 'notifications.index', __('notifications.marked_read'));
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->respondBasedOnRequest($request, null, 'notifications.index', __('notifications.marked_all_read'));
    }
}
