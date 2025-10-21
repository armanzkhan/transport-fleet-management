<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Check for expiring documents and create notifications
     */
    public static function checkExpiryNotifications()
    {
        $notifications = [];
        
        // Check Token Tax expiry (15 days before)
        $tokenTaxExpiring = Vehicle::whereNotNull('token_tax_expiry')
            ->where('token_tax_expiry', '<=', Carbon::now()->addDays(15))
            ->where('token_tax_expiry', '>', Carbon::now())
            ->get();
            
        foreach ($tokenTaxExpiring as $vehicle) {
            $daysLeft = Carbon::now()->diffInDays($vehicle->token_tax_expiry, false);
            $notifications[] = [
                'type' => 'token_tax_expiry',
                'title' => 'Token Tax Expiring Soon',
                'message' => "Vehicle {$vehicle->vrn} token tax expires in {$daysLeft} days ({$vehicle->token_tax_expiry->format('Y-m-d')})",
                'vehicle_id' => $vehicle->id,
                'expiry_date' => $vehicle->token_tax_expiry,
                'days_left' => $daysLeft,
                'priority' => $daysLeft <= 7 ? 'high' : ($daysLeft <= 10 ? 'medium' : 'low')
            ];
        }
        
        // Check Dip Chart expiry (15 days before)
        $dipChartExpiring = Vehicle::whereNotNull('dip_chart_expiry')
            ->where('dip_chart_expiry', '<=', Carbon::now()->addDays(15))
            ->where('dip_chart_expiry', '>', Carbon::now())
            ->get();
            
        foreach ($dipChartExpiring as $vehicle) {
            $daysLeft = Carbon::now()->diffInDays($vehicle->dip_chart_expiry, false);
            $notifications[] = [
                'type' => 'dip_chart_expiry',
                'title' => 'Dip Chart Expiring Soon',
                'message' => "Vehicle {$vehicle->vrn} dip chart expires in {$daysLeft} days ({$vehicle->dip_chart_expiry->format('Y-m-d')})",
                'vehicle_id' => $vehicle->id,
                'expiry_date' => $vehicle->dip_chart_expiry,
                'days_left' => $daysLeft,
                'priority' => $daysLeft <= 7 ? 'high' : ($daysLeft <= 10 ? 'medium' : 'low')
            ];
        }
        
        // Check Tracker expiry (15 days before)
        $trackerExpiring = Vehicle::whereNotNull('tracker_expiry')
            ->where('tracker_expiry', '<=', Carbon::now()->addDays(15))
            ->where('tracker_expiry', '>', Carbon::now())
            ->get();
            
        foreach ($trackerExpiring as $vehicle) {
            $daysLeft = Carbon::now()->diffInDays($vehicle->tracker_expiry, false);
            $notifications[] = [
                'type' => 'tracker_expiry',
                'title' => 'Tracker Expiring Soon',
                'message' => "Vehicle {$vehicle->vrn} tracker expires in {$daysLeft} days ({$vehicle->tracker_expiry->format('Y-m-d')})",
                'vehicle_id' => $vehicle->id,
                'expiry_date' => $vehicle->tracker_expiry,
                'days_left' => $daysLeft,
                'priority' => $daysLeft <= 7 ? 'high' : ($daysLeft <= 10 ? 'medium' : 'low')
            ];
        }
        
        // Save notifications to database
        foreach ($notifications as $notificationData) {
            Notification::updateOrCreate(
                [
                    'type' => $notificationData['type'],
                    'vehicle_id' => $notificationData['vehicle_id'],
                    'is_read' => false
                ],
                [
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'priority' => $notificationData['priority'],
                    'expiry_date' => $notificationData['expiry_date'],
                    'days_left' => $notificationData['days_left'],
                    'created_by' => 1, // System
                    'created_at' => now()
                ]
            );
        }
        
        return $notifications;
    }
    
    /**
     * Get notifications for current user
     */
    public static function getUserNotifications($limit = 10)
    {
        return Notification::where('is_read', false)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get notification count
     */
    public static function getNotificationCount()
    {
        return Notification::where('is_read', false)->count();
    }
    
    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
            return true;
        }
        return false;
    }
    
    /**
     * Mark all notifications as read
     */
    public static function markAllAsRead()
    {
        return Notification::where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
    
    /**
     * Get dashboard alerts
     */
    public static function getDashboardAlerts()
    {
        $alerts = [];
        
        // High priority alerts
        $highPriority = Notification::where('is_read', false)
            ->where('priority', 'high')
            ->count();
            
        if ($highPriority > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Urgent Expiry Alerts',
                'message' => "{$highPriority} documents expiring within 7 days",
                'count' => $highPriority
            ];
        }
        
        // Medium priority alerts
        $mediumPriority = Notification::where('is_read', false)
            ->where('priority', 'medium')
            ->count();
            
        if ($mediumPriority > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Expiry Warnings',
                'message' => "{$mediumPriority} documents expiring within 10 days",
                'count' => $mediumPriority
            ];
        }
        
        // Low priority alerts
        $lowPriority = Notification::where('is_read', false)
            ->where('priority', 'low')
            ->count();
            
        if ($lowPriority > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Upcoming Expiries',
                'message' => "{$lowPriority} documents expiring within 15 days",
                'count' => $lowPriority
            ];
        }
        
        return $alerts;
    }
}
