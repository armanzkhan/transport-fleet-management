<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\CashBook;
use App\Models\JourneyVoucher;
use App\Models\VehicleBill;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PrintExportService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Route to role-specific dashboards
        if ($user->hasAdminAccess()) {
            // Admin and Super Admin have access to all dashboards - default to admin dashboard
            return $this->adminDashboard();
        } elseif ($user->isFleetManager()) {
            return $this->fleetDashboard();
        } elseif ($user->isAccountant()) {
            return $this->financeDashboard();
        }
        
        // Default dashboard for users without specific roles
        return $this->defaultDashboard();
    }

    public function adminDashboard()
    {
        $user = auth()->user();
        
        // Check for expiry notifications (async to avoid blocking)
        try {
            NotificationService::checkExpiryNotifications();
        } catch (\Exception $e) {
            // Log error but don't block dashboard
            \Log::error('Notification check failed: ' . $e->getMessage());
        }
        
        // Get expiring documents using optimized query
        $fifteenDaysFromNow = now()->addDays(15);
        $expiringVehicles = Vehicle::where('is_active', true)
            ->where(function($query) use ($fifteenDaysFromNow) {
                $query->where('token_tax_expiry', '<=', $fifteenDaysFromNow)
                      ->orWhere('dip_chart_expiry', '<=', $fifteenDaysFromNow)
                      ->orWhere('tracker_expiry', '<=', $fifteenDaysFromNow);
            })
            ->limit(20) // Limit to prevent performance issues
            ->get();

        // Get recent activities with limit
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent users with limit
        $recentUsers = User::with('roles')
            ->orderBy('last_login_at', 'desc')
            ->limit(5)
            ->get();

        // Get system alerts (simplified)
        $systemAlerts = $this->getSystemAlerts();

        // Get statistics (optimized)
        $stats = $this->getAdminStatsOptimized();

        return view('dashboards.admin', compact('expiringVehicles', 'recentActivities', 'recentUsers', 'systemAlerts', 'stats'));
    }

    public function fleetDashboard()
    {
        $user = auth()->user();
        
        // Get expiring documents using optimized query
        $fifteenDaysFromNow = now()->addDays(15);
        $expiringVehicles = Vehicle::where('is_active', true)
            ->where(function($query) use ($fifteenDaysFromNow) {
                $query->where('token_tax_expiry', '<=', $fifteenDaysFromNow)
                      ->orWhere('dip_chart_expiry', '<=', $fifteenDaysFromNow)
                      ->orWhere('tracker_expiry', '<=', $fifteenDaysFromNow);
            })
            ->limit(20)
            ->get();

        // Get recent activities with limit
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get vehicle status
        $vehicleStatus = Vehicle::with('owner')
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent journeys
        $recentJourneys = JourneyVoucher::with('vehicle')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get fleet alerts
        $fleetAlerts = $this->getFleetAlerts();

        // Get statistics
        $stats = $this->getFleetStats($user);

        return view('dashboards.fleet', compact('expiringVehicles', 'recentActivities', 'vehicleStatus', 'recentJourneys', 'fleetAlerts', 'stats'));
    }

    public function financeDashboard()
    {
        $user = auth()->user();
        
        // Get recent cash entries
        $recentCashEntries = CashBook::orderBy('entry_date', 'desc')
            ->limit(10)
            ->get();

        // Get recent bills
        $recentBills = VehicleBill::with('vehicle')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent financial activities
        $recentFinancialActivities = $this->getRecentFinancialActivities();

        // Get financial alerts
        $financialAlerts = $this->getFinancialAlerts();

        // Get statistics
        $stats = $this->getFinanceStats($user);

        return view('dashboards.finance', compact('recentCashEntries', 'recentBills', 'recentFinancialActivities', 'financialAlerts', 'stats'));
    }

    public function defaultDashboard()
    {
        $user = auth()->user();
        
        // Get expiring documents
        $expiringVehicles = Vehicle::where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            });

        // Get recent activities
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get statistics based on user role
        $stats = $this->getDashboardStats($user);

        return view('dashboard', compact('expiringVehicles', 'recentActivities', 'stats'));
    }

    private function getDashboardStats($user)
    {
        $stats = [
            'total_vehicles' => Vehicle::where('is_active', true)->count(),
            'total_owners' => VehicleOwner::where('is_active', true)->count(),
            'total_journeys' => JourneyVoucher::count(),
            'total_bills' => VehicleBill::count(),
        ];

        // Add role-specific stats based on user permissions
        if ($user->isAccountant() || $user->isSuperAdmin()) {
            $stats['total_cash_entries'] = CashBook::count();
            $stats['pending_bills'] = VehicleBill::where('status', 'draft')->count();
            $stats['finalized_bills'] = VehicleBill::where('is_finalized', true)->count();
        }

        if ($user->isFleetManager() || $user->isSuperAdmin()) {
            $stats['active_journeys'] = JourneyVoucher::where('is_processed', false)->count();
            $stats['processed_journeys'] = JourneyVoucher::where('is_processed', true)->count();
        }

        return $stats;
    }

    private function getAdminStats($user)
    {
        return [
            'total_users' => User::count(),
            'active_vehicles' => Vehicle::where('is_active', true)->count(),
            'expiring_documents' => Vehicle::where('is_active', true)
                ->get()
                ->filter(function ($vehicle) {
                    return $vehicle->hasExpiringDocuments();
                })->count(),
            'total_owners' => VehicleOwner::where('is_active', true)->count(),
            'total_journeys' => JourneyVoucher::count(),
            'total_bills' => VehicleBill::count(),
        ];
    }

    private function getAdminStatsOptimized()
    {
        $fifteenDaysFromNow = now()->addDays(15);
        
        return [
            'total_users' => User::count(),
            'active_vehicles' => Vehicle::where('is_active', true)->count(),
            'expiring_documents' => Vehicle::where('is_active', true)
                ->where(function($query) use ($fifteenDaysFromNow) {
                    $query->where('token_tax_expiry', '<=', $fifteenDaysFromNow)
                          ->orWhere('dip_chart_expiry', '<=', $fifteenDaysFromNow)
                          ->orWhere('tracker_expiry', '<=', $fifteenDaysFromNow);
                })
                ->count(),
            'total_owners' => VehicleOwner::where('is_active', true)->count(),
            'total_journeys' => JourneyVoucher::count(),
            'total_bills' => VehicleBill::count(),
        ];
    }

    private function getFleetStats($user)
    {
        return [
            'total_vehicles' => Vehicle::where('is_active', true)->count(),
            'active_journeys' => JourneyVoucher::where('is_processed', false)->count(),
            'processed_journeys' => JourneyVoucher::where('is_processed', true)->count(),
            'maintenance_due' => Vehicle::where('is_active', true)
                ->where('next_maintenance_date', '<=', Carbon::now()->addDays(30))
                ->count(),
            'expiring_documents' => Vehicle::where('is_active', true)
                ->get()
                ->filter(function ($vehicle) {
                    return $vehicle->hasExpiringDocuments();
                })->count(),
        ];
    }

    private function getFinanceStats($user)
    {
        $totalRevenue = CashBook::where('transaction_type', 'receive')->orWhere('transaction_type', '')->sum('amount');
        $totalExpenses = CashBook::where('transaction_type', 'payment')->sum('amount');
        
        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
            'pending_bills' => VehicleBill::where('is_finalized', false)->count(),
            'total_cash_entries' => CashBook::count(),
            'finalized_bills' => VehicleBill::where('is_finalized', true)->count(),
        ];
    }

    private function getSystemAlerts()
    {
        $alerts = [];
        
        // Check for expiring documents
        $expiringCount = Vehicle::where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            })->count();
            
        if ($expiringCount > 0) {
            $alerts[] = [
                'icon' => 'exclamation-triangle',
                'type' => 'warning',
                'title' => 'Document Expiry Alert',
                'message' => "{$expiringCount} vehicles have expiring documents",
                'time' => 'Just now'
            ];
        }
        
        // Check for inactive users
        $inactiveUsers = User::where('last_login_at', '<', Carbon::now()->subDays(30))->count();
        if ($inactiveUsers > 0) {
            $alerts[] = [
                'icon' => 'user-times',
                'type' => 'info',
                'title' => 'Inactive Users',
                'message' => "{$inactiveUsers} users haven't logged in for 30+ days",
                'time' => '1 hour ago'
            ];
        }
        
        return $alerts;
    }

    private function getFleetAlerts()
    {
        $alerts = [];
        
        // Check for vehicles needing maintenance
        $maintenanceDue = Vehicle::where('is_active', true)
            ->where('next_maintenance_date', '<=', Carbon::now()->addDays(7))
            ->count();
            
        if ($maintenanceDue > 0) {
            $alerts[] = [
                'icon' => 'wrench',
                'type' => 'warning',
                'title' => 'Maintenance Due',
                'message' => "{$maintenanceDue} vehicles need maintenance within 7 days",
                'time' => 'Just now'
            ];
        }
        
        // Check for long-running journeys
        $longJourneys = JourneyVoucher::where('is_processed', false)
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();
            
        if ($longJourneys > 0) {
            $alerts[] = [
                'icon' => 'clock',
                'type' => 'info',
                'title' => 'Long-running Journeys',
                'message' => "{$longJourneys} journeys have been running for 3+ days",
                'time' => '2 hours ago'
            ];
        }
        
        return $alerts;
    }

    private function getFinancialAlerts()
    {
        $alerts = [];
        
        // Check for overdue bills
        $overdueBills = VehicleBill::where('is_finalized', true)
            ->where('due_date', '<', Carbon::now())
            ->count();
            
        if ($overdueBills > 0) {
            $alerts[] = [
                'icon' => 'exclamation-circle',
                'type' => 'danger',
                'title' => 'Overdue Bills',
                'message' => "{$overdueBills} bills are overdue for payment",
                'time' => 'Just now'
            ];
        }
        
        // Check for low cash balance
        $cashBalance = CashBook::latest('date')->first();
        if ($cashBalance && $cashBalance->balance < 1000) {
            $alerts[] = [
                'icon' => 'money-bill-wave',
                'type' => 'warning',
                'title' => 'Low Cash Balance',
                'message' => "Cash balance is low: $" . number_format($cashBalance->balance, 2),
                'time' => '30 minutes ago'
            ];
        }
        
        return $alerts;
    }

    private function getRecentFinancialActivities()
    {
        $activities = [];
        
        // Get recent cash entries
        $recentCashEntries = CashBook::orderBy('created_at', 'desc')->limit(5)->get();
        foreach ($recentCashEntries as $entry) {
            $activities[] = [
                'icon' => ($entry->transaction_type === 'receive' || $entry->transaction_type === '') ? 'arrow-down' : 'arrow-up',
                'color' => ($entry->transaction_type === 'receive' || $entry->transaction_type === '') ? 'success' : 'danger',
                'title' => ($entry->transaction_type ? ucfirst($entry->transaction_type) : 'Receive') . ' Entry',
                'description' => $entry->description . ' - $' . number_format($entry->amount, 2),
                'time' => $entry->created_at->diffForHumans()
            ];
        }
        
        // Get recent bills
        $recentBills = VehicleBill::orderBy('created_at', 'desc')->limit(3)->get();
        foreach ($recentBills as $bill) {
            $activities[] = [
                'icon' => 'file-invoice',
                'color' => $bill->is_finalized ? 'success' : 'warning',
                'title' => 'Bill ' . ($bill->is_finalized ? 'Finalized' : 'Created'),
                'description' => $bill->bill_number . ' - $' . number_format($bill->total_amount, 2),
                'time' => $bill->created_at->diffForHumans()
            ];
        }
        
        return $activities;
    }

    public function notifications()
    {
        // Get all vehicles with expiring documents
        $expiringVehicles = Vehicle::with('owner')
            ->where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            });

        // Categorize notifications by urgency
        $criticalNotifications = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] <= 3;
            });
        });

        $warningNotifications = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] > 3 && $doc['days_remaining'] <= 7;
            });
        });

        $infoNotifications = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['days_remaining'] > 7;
            });
        });

        // Get specific document type counts
        $trackerExpiring = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['type'] === 'Tracker';
            });
        });

        $dipChartExpiring = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['type'] === 'Dip Chart';
            });
        });

        $tokenTaxExpiring = $expiringVehicles->filter(function ($vehicle) {
            return collect($vehicle->getExpiringDocuments())->some(function ($doc) {
                return $doc['type'] === 'Token Tax';
            });
        });

        return view('notifications', compact(
            'expiringVehicles',
            'criticalNotifications',
            'warningNotifications', 
            'infoNotifications',
            'trackerExpiring',
            'dipChartExpiring',
            'tokenTaxExpiring'
        ));
    }

    public function notificationsApi()
    {
        $expiringVehicles = Vehicle::where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            })
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'vrn' => $vehicle->vrn,
                    'owner' => $vehicle->owner?->name,
                    'driver_name' => $vehicle->driver_name,
                    'expiring_documents' => $vehicle->getExpiringDocuments()
                ];
            });

        return response()->json($expiringVehicles);
    }

    public function updateNotificationSettings(Request $request)
    {
        $user = auth()->user();
        
        $settings = $request->validate([
            'popup_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'snooze_hours' => 'integer|min:1|max:168', // Max 1 week
        ]);

        // Store in session for now (can be moved to database later)
        session(['notification_settings' => $settings]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully',
            'settings' => $settings
        ]);
    }

    /**
     * Export dashboard report
     */
    public function exportReport(Request $request)
    {
        try {
            // Basic authorization - allow authenticated users
            if (!auth()->check()) {
                abort(403, 'Unauthorized');
            }
            
            $user = auth()->user();
            $exportType = $request->get('type', 'csv'); // csv, html, excel
            
            // Create simple CSV content for testing
            $csvContent = "Export Date,Exported By,Data Type,Value\n";
            $csvContent .= now()->format('Y-m-d H:i:s') . "," . $user->name . ",Total Vehicles," . Vehicle::count() . "\n";
            $csvContent .= now()->format('Y-m-d H:i:s') . "," . $user->name . ",Total Owners," . VehicleOwner::count() . "\n";
            $csvContent .= now()->format('Y-m-d H:i:s') . "," . $user->name . ",Total Journeys," . JourneyVoucher::count() . "\n";
            $csvContent .= now()->format('Y-m-d H:i:s') . "," . $user->name . ",Total Bills," . VehicleBill::count() . "\n";
            
            $filename = 'dashboard-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Export failed: ' . $e->getMessage());
            
            // Return a simple error response
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin export data
     */
    private function getAdminExportData()
    {
        $stats = $this->getAdminStats();
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        $expiringVehicles = Vehicle::where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            });
        
        return [
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'expiring_vehicles' => $expiringVehicles,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()->name
        ];
    }

    /**
     * Get fleet export data
     */
    private function getFleetExportData()
    {
        $stats = $this->getFleetStats();
        $recentTrips = JourneyVoucher::with(['vehicle.owner', 'creator'])
            ->orderBy('journey_date', 'desc')
            ->limit(50)
            ->get();
        
        return [
            'stats' => $stats,
            'recent_trips' => $recentTrips,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()->name
        ];
    }

    /**
     * Get finance export data
     */
    private function getFinanceExportData()
    {
        $stats = $this->getFinanceStats();
        $recentCashEntries = CashBook::with(['account', 'vehicle', 'creator'])
            ->orderBy('entry_date', 'desc')
            ->limit(50)
            ->get();
        
        $recentBills = VehicleBill::with(['vehicle.owner', 'creator'])
            ->orderBy('billing_month', 'desc')
            ->limit(50)
            ->get();
        
        return [
            'stats' => $stats,
            'recent_cash_entries' => $recentCashEntries,
            'recent_bills' => $recentBills,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()->name
        ];
    }

    /**
     * Get default export data
     */
    private function getDefaultExportData()
    {
        $stats = [
            'total_vehicles' => Vehicle::where('is_active', true)->count(),
            'total_owners' => VehicleOwner::where('is_active', true)->count(),
            'total_trips' => JourneyVoucher::count(),
            'total_revenue' => JourneyVoucher::sum('total_amount')
        ];
        
        return [
            'stats' => $stats,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()->name
        ];
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($data, $title, $printService)
    {
        try {
            $columns = [
                'Export Date' => 'export_date',
                'Exported By' => 'exported_by',
                'Data Type' => 'data_type',
                'Value' => 'value'
            ];
            
            $exportData = [];
            
            // Add stats
            if (isset($data['stats'])) {
                foreach ($data['stats'] as $key => $value) {
                    $exportData[] = [
                        'export_date' => $data['export_date'],
                        'exported_by' => $data['exported_by'],
                        'data_type' => ucwords(str_replace('_', ' ', $key)),
                        'value' => is_numeric($value) ? number_format($value, 2) : $value
                    ];
                }
            }
            
            $result = $printService->generateCSV($exportData, $title, $columns);
            
            return response()->download($result['file'], $result['filename'], [
                'Content-Type' => $result['mime']
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('CSV Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'CSV Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export to HTML
     */
    private function exportToHTML($data, $title, $printService)
    {
        $columns = [
            'Export Date' => 'export_date',
            'Exported By' => 'exported_by',
            'Data Type' => 'data_type',
            'Value' => 'value'
        ];
        
        $exportData = [];
        
        // Add stats
        if (isset($data['stats'])) {
            foreach ($data['stats'] as $key => $value) {
                $exportData[] = [
                    'export_date' => $data['export_date'],
                    'exported_by' => $data['exported_by'],
                    'data_type' => ucwords(str_replace('_', ' ', $key)),
                    'value' => is_numeric($value) ? number_format($value, 2) : $value
                ];
            }
        }
        
        $result = $printService->generateHTML($exportData, $title, $columns);
        
        return response()->download($result['file'], $result['filename'], [
            'Content-Type' => $result['mime']
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export to Excel (simplified as CSV for now)
     */
    private function exportToExcel($data, $title, $printService)
    {
        // For now, export as CSV since we don't have PhpSpreadsheet installed
        return $this->exportToCSV($data, $title, $printService);
    }
}
