<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

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

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'Notification marked as read');

        } catch (ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            return redirect()->back()->with('error', 'Notification not found');

        } catch (Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notification as read'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to mark notification as read');
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

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All notifications marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'All notifications marked as read');

        } catch (Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'action' => 'mark_all_as_aead'
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to mark all notifications as read');
        }
    }

 
}
