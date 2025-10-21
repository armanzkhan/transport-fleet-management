<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JourneyVoucher;
use App\Models\Vehicle;
use App\Models\MasterData;
use App\Models\Tariff;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JourneyVoucherController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-journey-vouchers');
        
        $query = JourneyVoucher::with(['vehicle.owner', 'creator']);
        
        // Apply search filters
        if ($request->filled('search_date')) {
            $query->whereDate('journey_date', $request->search_date);
        }
        
        if ($request->filled('search_carriage')) {
            $query->where('carriage_name', 'like', '%' . $request->search_carriage . '%');
        }
        
        if ($request->filled('search_company')) {
            $query->where('company', 'like', '%' . $request->search_company . '%');
        }
        
        if ($request->filled('search_loading_point')) {
            $query->where('loading_point', 'like', '%' . $request->search_loading_point . '%');
        }
        
        if ($request->filled('search_destination')) {
            $query->where('destination', 'like', '%' . $request->search_destination . '%');
        }
        
        if ($request->filled('search_vrn')) {
            $query->whereHas('vehicle', function($q) use ($request) {
                $q->where('vrn', 'like', '%' . $request->search_vrn . '%');
            });
        }
        
        if ($request->filled('search_invoice')) {
            $query->where('invoice_number', 'like', '%' . $request->search_invoice . '%');
        }
        
        // General search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journey_number', 'like', '%' . $search . '%')
                  ->orWhere('carriage_name', 'like', '%' . $search . '%')
                  ->orWhere('company', 'like', '%' . $search . '%')
                  ->orWhere('loading_point', 'like', '%' . $search . '%')
                  ->orWhere('destination', 'like', '%' . $search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $search . '%')
                  ->orWhereHas('vehicle', function($vehicleQuery) use ($search) {
                      $vehicleQuery->where('vrn', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $journeyVouchers = $query->orderBy('journey_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('journey-vouchers.index', compact('journeyVouchers'));
    }

    public function primary()
    {
        $this->authorize('create-journey-vouchers');
        
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();
            
        $loadingPoints = MasterData::where('type', 'loading_point')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $destinations = MasterData::where('type', 'destination')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = MasterData::where('type', 'product')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $companies = MasterData::where('type', 'company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $carriages = MasterData::where('type', 'carriage')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('journey-vouchers.primary', compact('vehicles', 'loadingPoints', 'destinations', 'products', 'companies', 'carriages'));
    }

    public function secondary()
    {
        $this->authorize('create-journey-vouchers');
        
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();
            
        $loadingPoints = MasterData::where('type', 'loading_point')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $destinations = MasterData::where('type', 'destination')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = MasterData::where('type', 'product')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $companies = MasterData::where('type', 'company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $carriages = MasterData::where('type', 'carriage')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('journey-vouchers.secondary', compact('vehicles', 'loadingPoints', 'destinations', 'products', 'companies', 'carriages'));
    }

    public function storePrimary(Request $request)
    {
        $this->authorize('create-journey-vouchers');
        
        $validator = Validator::make($request->all(), [
            'journey_date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'carriage_name' => 'required|string|max:255',
            'loading_point' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'company_freight_rate' => 'required|numeric|min:0',
            'vehicle_freight_rate' => 'nullable|numeric|min:0',
            'shortage_quantity' => 'nullable|numeric|min:0',
            'shortage_rate' => 'nullable|numeric|min:0',
            'company_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'vehicle_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'billing_month' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'decant_capacity' => 'nullable|numeric|min:0',
            'is_direct_bill' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get vehicle capacity if decant capacity not provided
        $vehicle = Vehicle::find($request->vehicle_id);
        $decantCapacity = $request->decant_capacity ?? $vehicle->capacity;

        // Use vehicle rate if not provided
        $vehicleFreightRate = $request->vehicle_freight_rate ?? $request->company_freight_rate;

        // Calculate amounts
        $freightAmount = $decantCapacity * $request->company_freight_rate;
        $shortageAmount = ($request->shortage_quantity ?? 0) * ($request->shortage_rate ?? 0);
        $commissionAmount = $freightAmount * (($request->company_deduction_percentage ?? 0) / 100);
        $totalAmount = $freightAmount - $shortageAmount - $commissionAmount;

        $journeyVoucher = JourneyVoucher::create([
            'journey_number' => $this->generateJourneyNumber(),
            'journey_date' => $request->journey_date,
            'vehicle_id' => $request->vehicle_id,
            'carriage_name' => $request->carriage_name,
            'loading_point' => $request->loading_point,
            'loading_point_urdu' => $request->loading_point_urdu,
            'destination' => $request->destination,
            'destination_urdu' => $request->destination_urdu,
            'product' => $request->product,
            'product_urdu' => $request->product_urdu,
            'company' => $request->company,
            'capacity' => $vehicle->capacity,
            'decant_capacity' => $decantCapacity,
            'company_freight_rate' => $request->company_freight_rate,
            'vehicle_freight_rate' => $vehicleFreightRate,
            'shortage_quantity' => $request->shortage_quantity ?? 0,
            'shortage_rate' => $request->shortage_rate ?? 0,
            'company_deduction_percentage' => $request->company_deduction_percentage ?? 0,
            'vehicle_deduction_percentage' => $request->vehicle_deduction_percentage ?? 0,
            'billing_month' => $request->billing_month,
            'invoice_number' => $request->invoice_number,
            'is_direct_bill' => $request->has('is_direct_bill'),
            'journey_type' => 'primary',
            'freight_amount' => $freightAmount,
            'shortage_amount' => $shortageAmount,
            'commission_amount' => $commissionAmount,
            'total_amount' => $totalAmount,
            'created_by' => auth()->id(),
        ]);

        // Log the action
        AuditLog::log('create', $journeyVoucher);

        return redirect()->route('journey-vouchers.index')
            ->with('success', 'Primary journey voucher created successfully.');
    }

    /**
     * Generate unique journey number for primary vouchers
     */
    private function generateJourneyNumber()
    {
        $prefix = 'PJV';
        $year = date('Y');
        $month = date('m');
        
        $lastVoucher = JourneyVoucher::where('journey_number', 'like', "{$prefix}{$year}{$month}%")
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

    public function storeSecondary(Request $request)
    {
        $this->authorize('create-journey-vouchers');
        
        $validator = Validator::make($request->all(), [
            'journey_date' => 'required|date',
            'transactions' => 'required|array|min:1',
            'transactions.*.vehicle_id' => 'required|exists:vehicles,id',
            'transactions.*.loading_point' => 'required|string|max:255',
            'transactions.*.destination' => 'required|string|max:255',
            'transactions.*.product' => 'required|string|max:255',
            'transactions.*.company' => 'required|string|max:255',
            'transactions.*.rate' => 'required|numeric|min:0',
            'transactions.*.load_quantity' => 'required|numeric|min:0.01',
            'transactions.*.shortage_quantity' => 'nullable|numeric|min:0',
            'transactions.*.shortage_rate' => 'nullable|numeric|min:0',
            'transactions.*.company_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'transactions.*.vehicle_deduction_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $createdVouchers = [];

        foreach ($request->transactions as $transaction) {
            $vehicle = Vehicle::find($transaction['vehicle_id']);
            
            // Calculate amounts
            $freightAmount = $transaction['load_quantity'] * $transaction['rate'];
            $shortageAmount = ($transaction['shortage_quantity'] ?? 0) * ($transaction['shortage_rate'] ?? 0);
            $commissionAmount = $freightAmount * (($transaction['company_deduction_percentage'] ?? 0) / 100);
            $totalAmount = $freightAmount - $shortageAmount - $commissionAmount;

            $journeyVoucher = JourneyVoucher::create([
                'journey_date' => $request->journey_date,
                'vehicle_id' => $transaction['vehicle_id'],
                'carriage_name' => $request->carriage_name ?? 'Secondary Load',
                'loading_point' => $transaction['loading_point'],
                'loading_point_urdu' => $transaction['loading_point_urdu'] ?? null,
                'destination' => $transaction['destination'],
                'destination_urdu' => $transaction['destination_urdu'] ?? null,
                'product' => $transaction['product'],
                'product_urdu' => $transaction['product_urdu'] ?? null,
                'company' => $transaction['company'],
                'capacity' => $vehicle->capacity,
                'decant_capacity' => $transaction['load_quantity'],
                'company_freight_rate' => $transaction['rate'],
                'vehicle_freight_rate' => $transaction['rate'],
                'shortage_quantity' => $transaction['shortage_quantity'] ?? 0,
                'shortage_rate' => $transaction['shortage_rate'] ?? 0,
                'company_deduction_percentage' => $transaction['company_deduction_percentage'] ?? 0,
                'vehicle_deduction_percentage' => $transaction['vehicle_deduction_percentage'] ?? 0,
                'billing_month' => $request->billing_month ?? date('Y-m'),
                'journey_type' => 'secondary',
                'freight_amount' => $freightAmount,
                'shortage_amount' => $shortageAmount,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'created_by' => auth()->id(),
            ]);

            $createdVouchers[] = $journeyVoucher;

            // Log the action
            AuditLog::log('create', $journeyVoucher);
        }

        return redirect()->route('journey-vouchers.index')
            ->with('success', count($createdVouchers) . ' secondary journey vouchers created successfully.');
    }

    public function show(JourneyVoucher $journeyVoucher)
    {
        $this->authorize('view-journey-vouchers');
        
        $journeyVoucher->load(['vehicle.owner', 'creator']);
        
        return view('journey-vouchers.show', compact('journeyVoucher'));
    }

    public function edit(JourneyVoucher $journeyVoucher)
    {
        $this->authorize('edit-journey-vouchers');
        
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();
            
        $loadingPoints = MasterData::where('type', 'loading_point')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $destinations = MasterData::where('type', 'destination')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = MasterData::where('type', 'product')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $companies = MasterData::where('type', 'company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('journey-vouchers.edit', compact('journeyVoucher', 'vehicles', 'loadingPoints', 'destinations', 'products', 'companies'));
    }

    public function update(Request $request, JourneyVoucher $journeyVoucher)
    {
        $this->authorize('edit-journey-vouchers');
        
        $validator = Validator::make($request->all(), [
            'journey_date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'carriage_name' => 'required|string|max:255',
            'loading_point' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'company_freight_rate' => 'required|numeric|min:0',
            'vehicle_freight_rate' => 'nullable|numeric|min:0',
            'shortage_quantity' => 'nullable|numeric|min:0',
            'shortage_rate' => 'nullable|numeric|min:0',
            'company_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'vehicle_deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'billing_month' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'decant_capacity' => 'nullable|numeric|min:0',
            'is_direct_bill' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $journeyVoucher->toArray();

        // Get vehicle capacity if decant capacity not provided
        $vehicle = Vehicle::find($request->vehicle_id);
        $decantCapacity = $request->decant_capacity ?? $vehicle->capacity;

        // Use vehicle rate if not provided
        $vehicleFreightRate = $request->vehicle_freight_rate ?? $request->company_freight_rate;

        // Calculate amounts
        $freightAmount = $decantCapacity * $request->company_freight_rate;
        $shortageAmount = ($request->shortage_quantity ?? 0) * ($request->shortage_rate ?? 0);
        $commissionAmount = $freightAmount * (($request->company_deduction_percentage ?? 0) / 100);
        $totalAmount = $freightAmount - $shortageAmount - $commissionAmount;

        $journeyVoucher->update([
            'journey_date' => $request->journey_date,
            'vehicle_id' => $request->vehicle_id,
            'carriage_name' => $request->carriage_name,
            'loading_point' => $request->loading_point,
            'loading_point_urdu' => $request->loading_point_urdu,
            'destination' => $request->destination,
            'destination_urdu' => $request->destination_urdu,
            'product' => $request->product,
            'product_urdu' => $request->product_urdu,
            'company' => $request->company,
            'capacity' => $vehicle->capacity,
            'decant_capacity' => $decantCapacity,
            'company_freight_rate' => $request->company_freight_rate,
            'vehicle_freight_rate' => $vehicleFreightRate,
            'shortage_quantity' => $request->shortage_quantity ?? 0,
            'shortage_rate' => $request->shortage_rate ?? 0,
            'company_deduction_percentage' => $request->company_deduction_percentage ?? 0,
            'vehicle_deduction_percentage' => $request->vehicle_deduction_percentage ?? 0,
            'billing_month' => $request->billing_month,
            'invoice_number' => $request->invoice_number,
            'is_direct_bill' => $request->has('is_direct_bill'),
            'freight_amount' => $freightAmount,
            'shortage_amount' => $shortageAmount,
            'commission_amount' => $commissionAmount,
            'total_amount' => $totalAmount,
        ]);

        // Log the action
        AuditLog::log('update', $journeyVoucher, $oldValues, $journeyVoucher->fresh()->toArray());

        return redirect()->route('journey-vouchers.index')
            ->with('success', 'Journey voucher updated successfully.');
    }

    public function destroy(JourneyVoucher $journeyVoucher)
    {
        $this->authorize('delete-journey-vouchers');
        
        // Log the action
        AuditLog::log('delete', $journeyVoucher);
        
        $journeyVoucher->delete();

        return redirect()->route('journey-vouchers.index')
            ->with('success', 'Journey voucher deleted successfully.');
    }

    public function getApplicableTariff(Request $request)
    {
        $carriageName = $request->get('carriage_name');
        $company = $request->get('company');
        $loadingPoint = $request->get('loading_point');
        $destination = $request->get('destination');
        $date = $request->get('date');

        $tariff = Tariff::getApplicableTariff($carriageName, $company, $loadingPoint, $destination, $date);
        
        return response()->json(['tariff' => $tariff]);
    }
}
