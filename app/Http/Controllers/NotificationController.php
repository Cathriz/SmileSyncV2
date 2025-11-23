<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch all unread notifications
        $unreadNotifications = $user->unreadNotifications;

        // Fetch all read notifications
        $readNotifications = $user->readNotifications;

        // Mark all unread notifications as read immediately upon viewing the page
        $user->unreadNotifications->markAsRead();

        return view('notification', compact('unreadNotifications', 'readNotifications'));
    }

    // Optional: Delete a single notification
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}