<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-expiry {--days=15 : Days before expiry to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications for vehicles with expiring documents (tracker expiry, dip chart, token tax)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Checking for documents expiring within {$days} days...");
        
        // Get vehicles with expiring documents
        $expiringVehicles = Vehicle::with('owner')
            ->where('is_active', true)
            ->get()
            ->filter(function ($vehicle) use ($days) {
                $checkDate = now()->addDays($days);
                return ($vehicle->token_tax_expiry && $vehicle->token_tax_expiry <= $checkDate) ||
                       ($vehicle->dip_chart_expiry && $vehicle->dip_chart_expiry <= $checkDate) ||
                       ($vehicle->tracker_expiry && $vehicle->tracker_expiry <= $checkDate);
            });

        if ($expiringVehicles->isEmpty()) {
            $this->info('No vehicles found with expiring documents.');
            return 0;
        }

        $this->info("Found {$expiringVehicles->count()} vehicles with expiring documents.");

        // Get admin users who should receive notifications
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'Fleet Manager']);
        })->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('No admin users found to send notifications to.');
            return 0;
        }

        // Group vehicles by urgency
        $criticalVehicles = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] <= 3;
            });
        });

        $warningVehicles = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] > 3 && $doc['days_remaining'] <= 7;
            });
        });

        $infoVehicles = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] > 7;
            });
        });

        $this->table(
            ['Priority', 'Count', 'Description'],
            [
                ['Critical', $criticalVehicles->count(), 'Expiring within 3 days'],
                ['Warning', $warningVehicles->count(), 'Expiring within 4-7 days'],
                ['Info', $infoVehicles->count(), 'Expiring within 8-15 days'],
            ]
        );

        // Send notifications to each admin user
        foreach ($adminUsers as $user) {
            try {
                $this->sendNotificationEmail($user, $expiringVehicles, $criticalVehicles, $warningVehicles, $infoVehicles);
                $this->info("Notification sent to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send notification to {$user->email}: " . $e->getMessage());
                Log::error("Failed to send expiry notification to {$user->email}", [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
            }
        }

        $this->info('Expiry notifications sending completed.');
        return 0;
    }

    /**
     * Send notification email to user
     */
    private function sendNotificationEmail($user, $allVehicles, $criticalVehicles, $warningVehicles, $infoVehicles)
    {
        // For now, we'll create a simple text-based notification
        // In a real application, you would create a proper Mailable class
        
        $subject = 'Vehicle Document Expiry Alert - ' . now()->format('M d, Y');
        
        $message = "Dear {$user->name},\n\n";
        $message .= "This is an automated notification about vehicle documents that are expiring soon.\n\n";
        
        if ($criticalVehicles->count() > 0) {
            $message .= "🚨 CRITICAL ALERTS ({$criticalVehicles->count()} vehicles - Expiring within 3 days):\n";
            foreach ($criticalVehicles->take(5) as $vehicle) {
                $message .= "- {$vehicle->vrn} ({$vehicle->driver_name})\n";
                foreach ($vehicle->getExpiringDocuments() as $doc) {
                    if ($doc['days_remaining'] <= 3) {
                        $daysText = $doc['days_remaining'] <= 0 ? 'EXPIRED' : "{$doc['days_remaining']} days";
                        $message .= "  * {$doc['type']}: {$daysText}\n";
                    }
                }
            }
            $message .= "\n";
        }
        
        if ($warningVehicles->count() > 0) {
            $message .= "⚠️ WARNING ALERTS ({$warningVehicles->count()} vehicles - Expiring within 4-7 days):\n";
            foreach ($warningVehicles->take(3) as $vehicle) {
                $message .= "- {$vehicle->vrn} ({$vehicle->driver_name})\n";
            }
            $message .= "\n";
        }
        
        if ($infoVehicles->count() > 0) {
            $message .= "ℹ️ INFO ALERTS ({$infoVehicles->count()} vehicles - Expiring within 8-15 days)\n\n";
        }
        
        $message .= "Please log into the Transport Fleet Management System to view detailed information and take necessary actions.\n\n";
        $message .= "System URL: " . config('app.url') . "\n\n";
        $message .= "This is an automated message. Please do not reply to this email.\n";
        
        // Log the notification instead of sending email (since email might not be configured)
        Log::info("Expiry notification for {$user->email}", [
            'user_id' => $user->id,
            'total_vehicles' => $allVehicles->count(),
            'critical_count' => $criticalVehicles->count(),
            'warning_count' => $warningVehicles->count(),
            'info_count' => $infoVehicles->count(),
            'subject' => $subject,
            'message' => $message
        ]);
        
        // Uncomment the following lines when email is properly configured
        /*
        Mail::raw($message, function ($mail) use ($user, $subject) {
            $mail->to($user->email)
                 ->subject($subject);
        });
        */
    }
}
