<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataExportService;
use App\Services\DataImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataExportImportController extends Controller
{
    protected $exportService;
    protected $importService;

    public function __construct(DataExportService $exportService, DataImportService $importService)
    {
        $this->exportService = $exportService;
        $this->importService = $importService;
    }

    /**
     * Show export/import dashboard
     */
    public function index()
    {
        return view('data-export-import.index');
    }

    /**
     * Export data
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_type' => 'required|in:vehicles,vehicle_owners,journey_vouchers,vehicle_bills,cash_book,audit_logs,dashboard_summary',
            'format' => 'required|in:csv,excel,json,pdf'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $dataType = $request->data_type;
            $format = $request->format;

            switch ($dataType) {
                case 'vehicles':
                    return $this->exportService->exportVehicles($format);
                case 'vehicle_owners':
                    return $this->exportService->exportVehicleOwners($format);
                case 'journey_vouchers':
                    return $this->exportService->exportJourneyVouchers($format);
                case 'vehicle_bills':
                    return $this->exportService->exportVehicleBills($format);
                case 'cash_book':
                    return $this->exportService->exportCashBook($format);
                case 'audit_logs':
                    return $this->exportService->exportAuditLogs($format);
                case 'dashboard_summary':
                    return $this->exportService->exportDashboardSummary($format);
                default:
                    return response()->json(['error' => 'Invalid data type'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import data
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_type' => 'required|in:vehicles,vehicle_owners,journey_vouchers,cash_book',
            'file' => 'required|file|mimes:csv,txt|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $dataType = $request->data_type;
            $file = $request->file('file');
            
            // Read CSV content
            $csvContent = file_get_contents($file->getPathname());
            $csvData = $this->importService->parseCSV($csvContent);

            if (empty($csvData)) {
                return response()->json(['error' => 'No data found in CSV file'], 400);
            }

            // Import based on data type
            switch ($dataType) {
                case 'vehicles':
                    $results = $this->importService->importVehicles($csvData);
                    break;
                case 'vehicle_owners':
                    $results = $this->importService->importVehicleOwners($csvData);
                    break;
                case 'journey_vouchers':
                    $results = $this->importService->importJourneyVouchers($csvData);
                    break;
                case 'cash_book':
                    $results = $this->importService->importCashBook($csvData);
                    break;
                default:
                    return response()->json(['error' => 'Invalid data type'], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed successfully!",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_type' => 'required|in:vehicles,vehicle_owners,journey_vouchers,cash_book'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $dataType = $request->data_type;
            $filename = $dataType . '_import_template.csv';

            switch ($dataType) {
                case 'vehicles':
                    $csvContent = $this->importService->getVehicleImportTemplate();
                    break;
                case 'vehicle_owners':
                    $csvContent = $this->importService->getVehicleOwnerImportTemplate();
                    break;
                case 'journey_vouchers':
                    $csvContent = $this->importService->getJourneyVoucherImportTemplate();
                    break;
                case 'cash_book':
                    $csvContent = $this->importService->getCashBookImportTemplate();
                    break;
                default:
                    return response()->json(['error' => 'Invalid data type'], 400);
            }

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Template download failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get data statistics for export/import dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'vehicles' => \App\Models\Vehicle::count(),
                'vehicle_owners' => \App\Models\VehicleOwner::count(),
                'journey_vouchers' => \App\Models\JourneyVoucher::count(),
                'vehicle_bills' => \App\Models\VehicleBill::count(),
                'cash_book_entries' => \App\Models\CashBook::count(),
                'audit_logs' => \App\Models\AuditLog::count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get statistics: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk export all data
     */
    public function bulkExport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|in:csv,excel,json,pdf'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $format = $request->format;
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = 'complete_data_export_' . $timestamp;

            // Create a comprehensive export with all data
            $allData = [
                'export_info' => [
                    'exported_at' => now()->format('Y-m-d H:i:s'),
                    'exported_by' => auth()->user()->name,
                    'export_type' => 'Complete Data Export'
                ],
                'vehicles' => \App\Models\Vehicle::with(['owner'])->get()->toArray(),
                'vehicle_owners' => \App\Models\VehicleOwner::all()->toArray(),
                'journey_vouchers' => \App\Models\JourneyVoucher::with(['vehicle.owner'])->get()->toArray(),
                'vehicle_bills' => \App\Models\VehicleBill::with(['vehicle.owner'])->get()->toArray(),
                'cash_book_entries' => \App\Models\CashBook::with(['account', 'vehicle.owner'])->get()->toArray(),
                'audit_logs' => \App\Models\AuditLog::with(['user'])->get()->toArray(),
            ];

            switch ($format) {
                case 'json':
                    return response(json_encode($allData, JSON_PRETTY_PRINT))
                        ->header('Content-Type', 'application/json')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
                case 'csv':
                case 'excel':
                case 'pdf':
                default:
                    // For CSV/Excel/PDF, create a summary
                    $summaryData = collect([
                        [
                            'Data Type' => 'Vehicles',
                            'Count' => count($allData['vehicles']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'Data Type' => 'Vehicle Owners',
                            'Count' => count($allData['vehicle_owners']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'Data Type' => 'Journey Vouchers',
                            'Count' => count($allData['journey_vouchers']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'Data Type' => 'Vehicle Bills',
                            'Count' => count($allData['vehicle_bills']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'Data Type' => 'Cash Book Entries',
                            'Count' => count($allData['cash_book_entries']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'Data Type' => 'Audit Logs',
                            'Count' => count($allData['audit_logs']),
                            'Last Updated' => now()->format('Y-m-d H:i:s')
                        ]
                    ]);

                    return $this->exportService->formatExport($summaryData, $filename, $format);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Bulk export failed: ' . $e->getMessage()], 500);
        }
    }
}
