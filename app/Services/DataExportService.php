<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\JourneyVoucher;
use App\Models\VehicleBill;
use App\Models\CashBook;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DataExportService
{
    /**
     * Export all vehicles data
     */
    public function exportVehicles($format = 'csv')
    {
        $vehicles = Vehicle::with(['owner', 'journeyVouchers', 'vehicleBills'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $vehicles->map(function ($vehicle) {
            return [
                'Serial Number' => $vehicle->serial_number,
                'VRN' => $vehicle->vrn,
                'Owner Name' => $vehicle->owner->name ?? 'N/A',
                'Driver Name' => $vehicle->driver_name,
                'Capacity' => $vehicle->capacity,
                'Token Tax Expiry' => $vehicle->token_tax_expiry?->format('Y-m-d'),
                'Dip Chart Expiry' => $vehicle->dip_chart_expiry?->format('Y-m-d'),
                'Tracker Expiry' => $vehicle->tracker_expiry?->format('Y-m-d'),
                'Is Active' => $vehicle->is_active ? 'Yes' : 'No',
                'Created At' => $vehicle->created_at->format('Y-m-d H:i:s'),
                'Total Journeys' => $vehicle->journeyVouchers->count(),
                'Total Bills' => $vehicle->vehicleBills->count(),
            ];
        });

        return $this->formatExport($data, 'vehicles', $format);
    }

    /**
     * Export all vehicle owners data
     */
    public function exportVehicleOwners($format = 'csv')
    {
        $owners = VehicleOwner::with(['vehicles'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $owners->map(function ($owner) {
            return [
                'Name' => $owner->name,
                'Phone' => $owner->phone,
                'Email' => $owner->email,
                'Address' => $owner->address,
                'CNIC' => $owner->cnic,
                'Total Vehicles' => $owner->vehicles->count(),
                'Active Vehicles' => $owner->vehicles->where('is_active', true)->count(),
                'Created At' => $owner->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->formatExport($data, 'vehicle_owners', $format);
    }

    /**
     * Export all journey vouchers data
     */
    public function exportJourneyVouchers($format = 'csv')
    {
        $journeys = JourneyVoucher::with(['vehicle.owner', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $journeys->map(function ($journey) {
            return [
                'Journey Number' => $journey->journey_number,
                'Journey Date' => $journey->journey_date->format('Y-m-d'),
                'Vehicle VRN' => $journey->vehicle->vrn,
                'Owner Name' => $journey->vehicle->owner->name ?? 'N/A',
                'Loading Point' => $journey->loading_point,
                'Destination' => $journey->destination,
                'Product' => $journey->product,
                'Company' => $journey->company,
                'Company Freight Rate' => number_format($journey->company_freight_rate, 2),
                'Vehicle Freight Rate' => number_format($journey->vehicle_freight_rate, 2),
                'Shortage Quantity' => $journey->shortage_quantity,
                'Commission Amount' => number_format($journey->commission_amount, 2),
                'Total Amount' => number_format($journey->total_amount, 2),
                'Is Processed' => $journey->is_processed ? 'Yes' : 'No',
                'Is Billed' => $journey->is_billed ? 'Yes' : 'No',
                'Created By' => $journey->creator->name ?? 'N/A',
                'Created At' => $journey->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->formatExport($data, 'journey_vouchers', $format);
    }

    /**
     * Export all vehicle bills data
     */
    public function exportVehicleBills($format = 'csv')
    {
        $bills = VehicleBill::with(['vehicle.owner', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $bills->map(function ($bill) {
            return [
                'Bill Number' => $bill->bill_number,
                'Vehicle VRN' => $bill->vehicle->vrn,
                'Owner Name' => $bill->vehicle->owner->name ?? 'N/A',
                'Billing Month' => $bill->billing_month,
                'Total Freight' => number_format($bill->total_freight, 2),
                'Total Advance' => number_format($bill->total_advance, 2),
                'Total Expense' => number_format($bill->total_expense, 2),
                'Gross Profit' => number_format($bill->gross_profit, 2),
                'Net Profit' => number_format($bill->net_profit, 2),
                'Total Vehicle Balance' => number_format($bill->total_vehicle_balance, 2),
                'Is Finalized' => $bill->is_finalized ? 'Yes' : 'No',
                'Status' => $bill->status,
                'Created By' => $bill->creator->name ?? 'N/A',
                'Created At' => $bill->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->formatExport($data, 'vehicle_bills', $format);
    }

    /**
     * Export all cash book entries
     */
    public function exportCashBook($format = 'csv')
    {
        $entries = CashBook::with(['account', 'vehicle.owner', 'creator'])
            ->orderBy('entry_date', 'desc')
            ->get();

        $data = $entries->map(function ($entry) {
            return [
                'Cash Book Number' => $entry->cash_book_number,
                'Entry Date' => $entry->entry_date->format('Y-m-d'),
                'Transaction Type' => ucfirst($entry->transaction_type),
                'Amount' => number_format($entry->amount, 2),
                'Account Name' => $entry->account->account_name ?? 'N/A',
                'Vehicle VRN' => $entry->vehicle->vrn ?? 'N/A',
                'Owner Name' => $entry->vehicle->owner->name ?? 'N/A',
                'Description' => $entry->description,
                'Previous Day Balance' => number_format($entry->previous_day_balance, 2),
                'Total Cash in Hand' => number_format($entry->total_cash_in_hand, 2),
                'Created By' => $entry->creator->name ?? 'N/A',
                'Created At' => $entry->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->formatExport($data, 'cash_book', $format);
    }

    /**
     * Export audit logs
     */
    public function exportAuditLogs($format = 'csv')
    {
        $logs = AuditLog::with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $logs->map(function ($log) {
            return [
                'User' => $log->user->name ?? 'N/A',
                'Action' => ucfirst($log->action),
                'Model Type' => class_basename($log->model_type),
                'Model ID' => $log->model_id,
                'Old Values' => $log->old_values ? json_encode($log->old_values) : 'N/A',
                'New Values' => $log->new_values ? json_encode($log->new_values) : 'N/A',
                'IP Address' => $log->ip_address,
                'User Agent' => $log->user_agent,
                'Created At' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->formatExport($data, 'audit_logs', $format);
    }

    /**
     * Export dashboard summary
     */
    public function exportDashboardSummary($format = 'csv')
    {
        $summary = [
            'Export Date' => now()->format('Y-m-d H:i:s'),
            'Exported By' => Auth::user()->name,
            'Total Vehicles' => Vehicle::count(),
            'Active Vehicles' => Vehicle::where('is_active', true)->count(),
            'Total Owners' => VehicleOwner::count(),
            'Total Journeys' => JourneyVoucher::count(),
            'Processed Journeys' => JourneyVoucher::where('is_processed', true)->count(),
            'Total Bills' => VehicleBill::count(),
            'Finalized Bills' => VehicleBill::where('is_finalized', true)->count(),
            'Total Cash Entries' => CashBook::count(),
            'Total Audit Logs' => AuditLog::count(),
        ];

        $data = collect([$summary]);
        return $this->formatExport($data, 'dashboard_summary', $format);
    }

    /**
     * Format export data based on format
     */
    private function formatExport($data, $filename, $format)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = $filename . '_' . $timestamp;

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($data, $filename);
            case 'excel':
                return $this->exportToExcel($data, $filename);
            case 'json':
                return $this->exportToJSON($data, $filename);
            case 'pdf':
                return $this->exportToPDF($data, $filename);
            default:
                return $this->exportToCSV($data, $filename);
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($data, $filename)
    {
        $csvContent = '';
        
        if ($data->isNotEmpty()) {
            // Add headers
            $headers = array_keys($data->first());
            $csvContent .= implode(',', $headers) . "\n";
            
            // Add data rows
            foreach ($data as $row) {
                $csvContent .= implode(',', array_map(function($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export to Excel (CSV format for now)
     */
    private function exportToExcel($data, $filename)
    {
        return $this->exportToCSV($data, $filename);
    }

    /**
     * Export to JSON
     */
    private function exportToJSON($data, $filename)
    {
        $jsonData = [
            'export_info' => [
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'exported_by' => Auth::user()->name,
                'total_records' => $data->count(),
            ],
            'data' => $data->toArray()
        ];

        return response(json_encode($jsonData, JSON_PRETTY_PRINT))
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export to PDF (HTML format for now)
     */
    private function exportToPDF($data, $filename)
    {
        $html = $this->generateHTMLTable($data, $filename);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.html"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Generate HTML table for export
     */
    private function generateHTMLTable($data, $filename)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $filename . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { background: #f9f9f9; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . ucwords(str_replace('_', ' ', $filename)) . '</h1>
    </div>
    
    <div class="info">
        <p><strong>Exported by:</strong> ' . Auth::user()->name . '</p>
        <p><strong>Export Date:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
        <p><strong>Total Records:</strong> ' . $data->count() . '</p>
    </div>
    
    <table>
        <thead>
            <tr>';

        if ($data->isNotEmpty()) {
            $headers = array_keys($data->first());
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>
</body>
</html>';

        return $html;
    }
}
