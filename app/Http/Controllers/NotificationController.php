<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark all unread notifications as read.
     */
    public function readAll(): RedirectResponse
    {
        Auth::user()->notifications()->unread()->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Show/mark single notification as read and redirect or show details.
     */
    public function show(Notification $notification): RedirectResponse
    {
        // Ensure user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        // Redirect if notification data has action/route
        if ($notification->data && isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai dibaca.');
    }
}
