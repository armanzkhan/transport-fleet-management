<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Get notifications for current user
     */
    public function index(Request $request)
    {
        $notifications = NotificationService::getUserNotifications($request->get('limit', 10));
        $count = NotificationService::getNotificationCount();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $success = NotificationService::markAsRead($id);
        
        if ($success) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $count = NotificationService::markAllAsRead();
        
        return response()->json([
            'success' => true,
            'marked_count' => $count
        ]);
    }

    /**
     * Get dashboard alerts
     */
    public function getDashboardAlerts()
    {
        $alerts = NotificationService::getDashboardAlerts();
        
        return response()->json([
            'alerts' => $alerts
        ]);
    }

    /**
     * Check and create expiry notifications
     */
    public function checkExpiryNotifications()
    {
        $notifications = NotificationService::checkExpiryNotifications();
        
        return response()->json([
            'success' => true,
            'notifications_created' => count($notifications),
            'notifications' => $notifications
        ]);
    }

    /**
     * Get notification count for navbar
     */
    public function getCount()
    {
        $count = NotificationService::getNotificationCount();
        
        return response()->json(['count' => $count]);
    }
}
