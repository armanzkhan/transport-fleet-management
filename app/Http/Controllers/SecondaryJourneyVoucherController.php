<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecondaryJourneyVoucher;
use App\Models\MasterData;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\ChartOfAccount;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SecondaryJourneyVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-journey-vouchers')->only('create', 'store');
        $this->middleware('can:view-journey-vouchers')->only('index', 'show');
        $this->middleware('can:edit-journey-vouchers')->only('edit', 'update');
        $this->middleware('can:delete-journey-vouchers')->only('destroy');
    }

    /**
     * Display a listing of secondary journey vouchers
     */
    public function index(Request $request)
    {
        $this->authorize('view-journey-vouchers');
        
        $query = SecondaryJourneyVoucher::with(['vehicle', 'vehicle.owner']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journey_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('contractor_name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('journey_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('journey_date', '<=', $request->date_to);
        }
        
        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }
        
        if ($request->filled('contractor_name')) {
            $query->where('contractor_name', $request->contractor_name);
        }
        
        $secondaryVouchers = $query->orderBy('journey_date', 'desc')->paginate(15);
        
        // Get filter options
        $companies = SecondaryJourneyVoucher::distinct()->pluck('company')->filter();
        $contractors = SecondaryJourneyVoucher::distinct()->pluck('contractor_name')->filter();
        
        return view('secondary-journey-vouchers.index', compact('secondaryVouchers', 'companies', 'contractors'));
    }

    /**
     * Show the form for creating a new secondary journey voucher
     */
    public function create()
    {
        $this->authorize('create-journey-vouchers');
        
        // Get master data
        $loadingPoints = MasterData::where('type', 'loading_point')->where('is_active', true)->get();
        $destinations = MasterData::where('type', 'destination')->where('is_active', true)->get();
        $products = MasterData::where('type', 'product')->where('is_active', true)->get();
        $companies = MasterData::where('type', 'company')->where('is_active', true)->get();
        
        // Get vehicles with owners
        $vehicles = Vehicle::with('owner')->where('is_active', true)->get();
        
        return view('secondary-journey-vouchers.create', compact(
            'loadingPoints', 'destinations', 'products', 'companies', 'vehicles'
        ));
    }

    /**
     * Store a newly created secondary journey voucher
     */
    public function store(Request $request)
    {
        $this->authorize('create-journey-vouchers');
        
        $validator = Validator::make($request->all(), [
            'contractor_name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'journey_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.vrn' => 'required|string',
            'entries.*.invoice_number' => 'required|string',
            'entries.*.loading_point' => 'required|string',
            'entries.*.destination' => 'required|string',
            'entries.*.product' => 'required|string',
            'entries.*.rate' => 'required|numeric|min:0',
            'entries.*.load_quantity' => 'required|numeric|min:0',
            'entries.*.shortage_quantity' => 'nullable|numeric|min:0',
            'entries.*.shortage_amount' => 'nullable|numeric|min:0',
            'entries.*.company_deduction' => 'nullable|numeric|min:0',
            'entries.*.vehicle_commission' => 'nullable|numeric|min:0',
            'entries.*.pr04' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $totalFreight = 0;
            $totalShortage = 0;
            $totalCompanyDeduction = 0;
            $totalVehicleCommission = 0;
            
            // Calculate totals
            foreach ($request->entries as $entry) {
                $freight = $entry['rate'] * $entry['load_quantity'];
                $totalFreight += $freight;
                $totalShortage += $entry['shortage_amount'] ?? 0;
                $totalCompanyDeduction += $entry['company_deduction'] ?? 0;
                $totalVehicleCommission += $entry['vehicle_commission'] ?? 0;
            }
            
            // Create secondary journey voucher
            $secondaryVoucher = SecondaryJourneyVoucher::create([
                'journey_number' => $this->generateJourneyNumber(),
                'contractor_name' => $request->contractor_name,
                'company' => $request->company,
                'journey_date' => $request->journey_date,
                'total_freight' => $totalFreight,
                'total_shortage' => $totalShortage,
                'total_company_deduction' => $totalCompanyDeduction,
                'total_vehicle_commission' => $totalVehicleCommission,
                'net_amount' => $totalFreight - $totalShortage - $totalCompanyDeduction,
                'created_by' => auth()->id()
            ]);
            
            // Create individual entries
            foreach ($request->entries as $entry) {
                $freight = $entry['rate'] * $entry['load_quantity'];
                $netAmount = $freight - ($entry['shortage_amount'] ?? 0) - ($entry['company_deduction'] ?? 0);
                
                $secondaryVoucher->entries()->create([
                    'vrn' => $entry['vrn'],
                    'invoice_number' => $entry['invoice_number'],
                    'loading_point' => $entry['loading_point'],
                    'destination' => $entry['destination'],
                    'product' => $entry['product'],
                    'rate' => $entry['rate'],
                    'load_quantity' => $entry['load_quantity'],
                    'freight' => $freight,
                    'shortage_quantity' => $entry['shortage_quantity'] ?? 0,
                    'shortage_amount' => $entry['shortage_amount'] ?? 0,
                    'company_deduction' => $entry['company_deduction'] ?? 0,
                    'vehicle_commission' => $entry['vehicle_commission'] ?? 0,
                    'net_amount' => $netAmount,
                    'pr04' => $entry['pr04'] ?? false
                ]);
            }
            
            // Log the activity
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'model' => 'SecondaryJourneyVoucher',
                'model_id' => $secondaryVoucher->id,
                'description' => "Created secondary journey voucher {$secondaryVoucher->journey_number}"
            ]);
            
            return redirect()->route('secondary-journey-vouchers.show', $secondaryVoucher)
                ->with('success', 'Secondary journey voucher created successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create secondary journey voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified secondary journey voucher
     */
    public function show(SecondaryJourneyVoucher $secondaryJourneyVoucher)
    {
        $this->authorize('view-journey-vouchers');
        
        $secondaryJourneyVoucher->load(['entries', 'creator']);
        
        return view('secondary-journey-vouchers.show', compact('secondaryJourneyVoucher'));
    }

    /**
     * Show the form for editing the specified secondary journey voucher
     */
    public function edit(SecondaryJourneyVoucher $secondaryJourneyVoucher)
    {
        $this->authorize('edit-journey-vouchers');
        
        $secondaryJourneyVoucher->load('entries');
        
        // Get master data
        $loadingPoints = MasterData::where('type', 'loading_point')->where('is_active', true)->get();
        $destinations = MasterData::where('type', 'destination')->where('is_active', true)->get();
        $products = MasterData::where('type', 'product')->where('is_active', true)->get();
        $companies = MasterData::where('type', 'company')->where('is_active', true)->get();
        
        // Get vehicles with owners
        $vehicles = Vehicle::with('owner')->where('is_active', true)->get();
        
        return view('secondary-journey-vouchers.edit', compact(
            'secondaryJourneyVoucher', 'loadingPoints', 'destinations', 'products', 'companies', 'vehicles'
        ));
    }

    /**
     * Update the specified secondary journey voucher
     */
    public function update(Request $request, SecondaryJourneyVoucher $secondaryJourneyVoucher)
    {
        $this->authorize('edit-journey-vouchers');
        
        $validator = Validator::make($request->all(), [
            'contractor_name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'journey_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.vrn' => 'required|string',
            'entries.*.invoice_number' => 'required|string',
            'entries.*.loading_point' => 'required|string',
            'entries.*.destination' => 'required|string',
            'entries.*.product' => 'required|string',
            'entries.*.rate' => 'required|numeric|min:0',
            'entries.*.load_quantity' => 'required|numeric|min:0',
            'entries.*.shortage_quantity' => 'nullable|numeric|min:0',
            'entries.*.shortage_amount' => 'nullable|numeric|min:0',
            'entries.*.company_deduction' => 'nullable|numeric|min:0',
            'entries.*.vehicle_commission' => 'nullable|numeric|min:0',
            'entries.*.pr04' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $totalFreight = 0;
            $totalShortage = 0;
            $totalCompanyDeduction = 0;
            $totalVehicleCommission = 0;
            
            // Calculate totals
            foreach ($request->entries as $entry) {
                $freight = $entry['rate'] * $entry['load_quantity'];
                $totalFreight += $freight;
                $totalShortage += $entry['shortage_amount'] ?? 0;
                $totalCompanyDeduction += $entry['company_deduction'] ?? 0;
                $totalVehicleCommission += $entry['vehicle_commission'] ?? 0;
            }
            
            // Update secondary journey voucher
            $secondaryJourneyVoucher->update([
                'contractor_name' => $request->contractor_name,
                'company' => $request->company,
                'journey_date' => $request->journey_date,
                'total_freight' => $totalFreight,
                'total_shortage' => $totalShortage,
                'total_company_deduction' => $totalCompanyDeduction,
                'total_vehicle_commission' => $totalVehicleCommission,
                'net_amount' => $totalFreight - $totalShortage - $totalCompanyDeduction
            ]);
            
            // Delete existing entries and create new ones
            $secondaryJourneyVoucher->entries()->delete();
            
            foreach ($request->entries as $entry) {
                $freight = $entry['rate'] * $entry['load_quantity'];
                $netAmount = $freight - ($entry['shortage_amount'] ?? 0) - ($entry['company_deduction'] ?? 0);
                
                $secondaryJourneyVoucher->entries()->create([
                    'vrn' => $entry['vrn'],
                    'invoice_number' => $entry['invoice_number'],
                    'loading_point' => $entry['loading_point'],
                    'destination' => $entry['destination'],
                    'product' => $entry['product'],
                    'rate' => $entry['rate'],
                    'load_quantity' => $entry['load_quantity'],
                    'freight' => $freight,
                    'shortage_quantity' => $entry['shortage_quantity'] ?? 0,
                    'shortage_amount' => $entry['shortage_amount'] ?? 0,
                    'company_deduction' => $entry['company_deduction'] ?? 0,
                    'vehicle_commission' => $entry['vehicle_commission'] ?? 0,
                    'net_amount' => $netAmount,
                    'pr04' => $entry['pr04'] ?? false
                ]);
            }
            
            // Log the activity
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'model' => 'SecondaryJourneyVoucher',
                'model_id' => $secondaryJourneyVoucher->id,
                'description' => "Updated secondary journey voucher {$secondaryJourneyVoucher->journey_number}"
            ]);
            
            return redirect()->route('secondary-journey-vouchers.show', $secondaryJourneyVoucher)
                ->with('success', 'Secondary journey voucher updated successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update secondary journey voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified secondary journey voucher
     */
    public function destroy(SecondaryJourneyVoucher $secondaryJourneyVoucher)
    {
        $this->authorize('delete-journey-vouchers');
        
        try {
            $journeyNumber = $secondaryJourneyVoucher->journey_number;
            
            // Log the activity
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'model' => 'SecondaryJourneyVoucher',
                'model_id' => $secondaryJourneyVoucher->id,
                'description' => "Deleted secondary journey voucher {$journeyNumber}"
            ]);
            
            $secondaryJourneyVoucher->delete();
            
            return redirect()->route('secondary-journey-vouchers.index')
                ->with('success', 'Secondary journey voucher deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete secondary journey voucher: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate unique journey number
     */
    private function generateJourneyNumber()
    {
        $prefix = 'SJV';
        $year = date('Y');
        $month = date('m');
        
        $lastVoucher = SecondaryJourneyVoucher::where('journey_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('journey_number', 'desc')
            ->first();
        
        if ($lastVoucher) {
            $lastNumber = (int) substr($lastVoucher->journey_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get auto rate apply data
     */
    public function getAutoRateData(Request $request)
    {
        $loadingPoint = $request->get('loading_point');
        $destination = $request->get('destination');
        
        // Get recent rates for this route
        $recentRates = SecondaryJourneyVoucher::whereHas('entries', function($query) use ($loadingPoint, $destination) {
            $query->where('loading_point', $loadingPoint)
                  ->where('destination', $destination);
        })
        ->with(['entries' => function($query) use ($loadingPoint, $destination) {
            $query->where('loading_point', $loadingPoint)
                  ->where('destination', $destination);
        }])
        ->orderBy('journey_date', 'desc')
        ->limit(5)
        ->get();
        
        $rates = $recentRates->flatMap(function($voucher) {
            return $voucher->entries->pluck('rate');
        })->unique()->values();
        
        return response()->json([
            'rates' => $rates,
            'suggested_rate' => $rates->avg()
        ]);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('view-journey-vouchers');
        
        // Implementation for Excel export
        // This would use the PrintExportService
        return response()->json(['message' => 'Excel export functionality will be implemented']);
    }
}
