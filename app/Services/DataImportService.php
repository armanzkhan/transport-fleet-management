<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\JourneyVoucher;
use App\Models\VehicleBill;
use App\Models\CashBook;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DataImportService
{
    /**
     * Import vehicles from CSV data
     */
    public function importVehicles($csvData)
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        DB::beginTransaction();
        try {
            foreach ($csvData as $index => $row) {
                $validator = Validator::make($row, [
                    'serial_number' => 'required|string|max:255',
                    'vrn' => 'required|string|max:255|unique:vehicles,vrn',
                    'owner_id' => 'required|exists:vehicle_owners,id',
                    'driver_name' => 'required|string|max:255',
                    'capacity' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $results['errors'][] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    $results['skipped']++;
                    continue;
                }

                Vehicle::create([
                    'serial_number' => $row['serial_number'],
                    'vrn' => $row['vrn'],
                    'owner_id' => $row['owner_id'],
                    'driver_name' => $row['driver_name'],
                    'capacity' => $row['capacity'],
                    'token_tax_expiry' => isset($row['token_tax_expiry']) ? Carbon::parse($row['token_tax_expiry']) : null,
                    'dip_chart_expiry' => isset($row['dip_chart_expiry']) ? Carbon::parse($row['dip_chart_expiry']) : null,
                    'tracker_expiry' => isset($row['tracker_expiry']) ? Carbon::parse($row['tracker_expiry']) : null,
                    'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
                    'created_by' => Auth::id(),
                ]);

                $results['success']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $results['errors'][] = "Database error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import vehicle owners from CSV data
     */
    public function importVehicleOwners($csvData)
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        DB::beginTransaction();
        try {
            foreach ($csvData as $index => $row) {
                $validator = Validator::make($row, [
                    'name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'email' => 'nullable|email|max:255',
                    'address' => 'nullable|string|max:500',
                    'cnic' => 'nullable|string|max:20',
                ]);

                if ($validator->fails()) {
                    $results['errors'][] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    $results['skipped']++;
                    continue;
                }

                VehicleOwner::create([
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'] ?? null,
                    'address' => $row['address'] ?? null,
                    'cnic' => $row['cnic'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                $results['success']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $results['errors'][] = "Database error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import journey vouchers from CSV data
     */
    public function importJourneyVouchers($csvData)
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        DB::beginTransaction();
        try {
            foreach ($csvData as $index => $row) {
                $validator = Validator::make($row, [
                    'journey_number' => 'required|string|max:255|unique:journey_vouchers,journey_number',
                    'journey_date' => 'required|date',
                    'vehicle_id' => 'required|exists:vehicles,id',
                    'loading_point' => 'required|string|max:255',
                    'destination' => 'required|string|max:255',
                    'product' => 'required|string|max:255',
                    'company' => 'required|string|max:255',
                    'company_freight_rate' => 'required|numeric|min:0',
                    'vehicle_freight_rate' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $results['errors'][] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    $results['skipped']++;
                    continue;
                }

                $journey = JourneyVoucher::create([
                    'journey_number' => $row['journey_number'],
                    'journey_date' => Carbon::parse($row['journey_date']),
                    'vehicle_id' => $row['vehicle_id'],
                    'loading_point' => $row['loading_point'],
                    'destination' => $row['destination'],
                    'product' => $row['product'],
                    'company' => $row['company'],
                    'company_freight_rate' => $row['company_freight_rate'],
                    'vehicle_freight_rate' => $row['vehicle_freight_rate'],
                    'shortage_quantity' => $row['shortage_quantity'] ?? 0,
                    'commission_amount' => $row['commission_amount'] ?? 0,
                    'is_processed' => isset($row['is_processed']) ? (bool)$row['is_processed'] : false,
                    'is_billed' => isset($row['is_billed']) ? (bool)$row['is_billed'] : false,
                    'created_by' => Auth::id(),
                ]);

                // Calculate amounts
                $journey->calculateAmounts();
                $journey->save();

                $results['success']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $results['errors'][] = "Database error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import cash book entries from CSV data
     */
    public function importCashBook($csvData)
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        DB::beginTransaction();
        try {
            foreach ($csvData as $index => $row) {
                $validator = Validator::make($row, [
                    'cash_book_number' => 'required|string|max:255|unique:cash_books,cash_book_number',
                    'entry_date' => 'required|date',
                    'transaction_type' => 'required|in:credit,debit',
                    'amount' => 'required|numeric|min:0',
                    'account_id' => 'required|exists:chart_of_accounts,id',
                ]);

                if ($validator->fails()) {
                    $results['errors'][] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    $results['skipped']++;
                    continue;
                }

                $cashBook = CashBook::create([
                    'cash_book_number' => $row['cash_book_number'],
                    'entry_date' => Carbon::parse($row['entry_date']),
                    'transaction_type' => $row['transaction_type'],
                    'amount' => $row['amount'],
                    'account_id' => $row['account_id'],
                    'vehicle_id' => $row['vehicle_id'] ?? null,
                    'description' => $row['description'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                // Calculate balances
                $cashBook->calculateBalances();
                $cashBook->save();

                $results['success']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $results['errors'][] = "Database error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Parse CSV content
     */
    public function parseCSV($csvContent)
    {
        $lines = explode("\n", $csvContent);
        $data = [];
        $headers = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $row = str_getcsv($line);
            
            if ($index === 0) {
                $headers = $row;
                continue;
            }

            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        return $data;
    }

    /**
     * Get import template for vehicles
     */
    public function getVehicleImportTemplate()
    {
        $template = [
            'serial_number' => 'VH001',
            'vrn' => 'ABC-123',
            'owner_id' => '1',
            'driver_name' => 'John Doe',
            'capacity' => '1000',
            'token_tax_expiry' => '2024-12-31',
            'dip_chart_expiry' => '2024-12-31',
            'tracker_expiry' => '2024-12-31',
            'is_active' => '1'
        ];

        return $this->arrayToCSV([$template]);
    }

    /**
     * Get import template for vehicle owners
     */
    public function getVehicleOwnerImportTemplate()
    {
        $template = [
            'name' => 'John Smith',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'address' => '123 Main St, City',
            'cnic' => '12345-1234567-1'
        ];

        return $this->arrayToCSV([$template]);
    }

    /**
     * Get import template for journey vouchers
     */
    public function getJourneyVoucherImportTemplate()
    {
        $template = [
            'journey_number' => 'JV001',
            'journey_date' => '2024-01-01',
            'vehicle_id' => '1',
            'loading_point' => 'City A',
            'destination' => 'City B',
            'product' => 'Wheat',
            'company' => 'ABC Company',
            'company_freight_rate' => '1000',
            'vehicle_freight_rate' => '800',
            'shortage_quantity' => '0',
            'commission_amount' => '50',
            'is_processed' => '0',
            'is_billed' => '0'
        ];

        return $this->arrayToCSV([$template]);
    }

    /**
     * Get import template for cash book
     */
    public function getCashBookImportTemplate()
    {
        $template = [
            'cash_book_number' => 'CB001',
            'entry_date' => '2024-01-01',
            'transaction_type' => 'credit',
            'amount' => '1000',
            'account_id' => '1',
            'vehicle_id' => '1',
            'description' => 'Cash received'
        ];

        return $this->arrayToCSV([$template]);
    }

    /**
     * Convert array to CSV string
     */
    private function arrayToCSV($data)
    {
        $csv = '';
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $csv .= implode(',', $headers) . "\n";
            
            foreach ($data as $row) {
                $csv .= implode(',', array_map(function($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
        }
        
        return $csv;
    }
}
