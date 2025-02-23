<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user
     * 
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            auth()->user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'action' => 'mark_all_as_read'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }
}