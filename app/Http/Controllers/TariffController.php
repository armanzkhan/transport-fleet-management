<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tariff;
use App\Models\MasterData;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TariffController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-tariffs');
        
        $query = Tariff::with(['creator']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tariff_number', 'like', "%{$search}%")
                  ->orWhere('carriage_name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('loading_point', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }
        
        // Specific search filters
        if ($request->filled('search_tariff')) {
            $query->where('tariff_number', 'like', "%{$request->search_tariff}%");
        }
        
        if ($request->filled('search_carriage')) {
            $query->where('carriage_name', 'like', "%{$request->search_carriage}%");
        }
        
        if ($request->filled('search_company')) {
            $query->where('company', 'like', "%{$request->search_company}%");
        }
        
        if ($request->filled('search_loading_point')) {
            $query->where('loading_point', 'like', "%{$request->search_loading_point}%");
        }
        
        if ($request->filled('search_destination')) {
            $query->where('destination', 'like', "%{$request->search_destination}%");
        }
        
        // Dropdown filters (from index form)
        if ($request->filled('carriage_name')) {
            $query->where('carriage_name', $request->carriage_name);
        }
        
        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }
        
        if ($request->filled('search_date')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('from_date', '<=', $request->search_date)
                  ->whereDate('to_date', '>=', $request->search_date);
            });
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('from_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('to_date', '<=', $request->date_to);
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('from_date', '<=', now())
                      ->where('to_date', '>=', now());
            } elseif ($request->status === 'expired') {
                $query->where('to_date', '<', now());
            } elseif ($request->status === 'future') {
                $query->where('from_date', '>', now());
            }
        }
        
        $tariffs = $query->orderBy('from_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return view('tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        $this->authorize('create-tariffs');
        
        $carriages = MasterData::where('type', 'carriage')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $companies = MasterData::where('type', 'company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $loadingPoints = MasterData::where('type', 'loading_point')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $destinations = MasterData::where('type', 'destination')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tariffs.create', compact('carriages', 'companies', 'loadingPoints', 'destinations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-tariffs');
        
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'carriage_name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'loading_point' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'company_freight_rate' => 'required|numeric|min:0',
            'vehicle_freight_rate' => 'nullable|numeric|min:0',
            'company_shortage_rate' => 'required|numeric|min:0',
            'vehicle_shortage_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate entries
        $existingTariff = Tariff::where('carriage_name', $request->carriage_name)
            ->where('company', $request->company)
            ->where('loading_point', $request->loading_point)
            ->where('destination', $request->destination)
            ->where(function($query) use ($request) {
                $query->whereBetween('from_date', [$request->from_date, $request->to_date])
                      ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('from_date', '<=', $request->from_date)
                            ->where('to_date', '>=', $request->to_date);
                      });
            })
            ->first();

        if ($existingTariff) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'A tariff already exists for this route and date range.'])
                ->withInput();
        }

        try {
            $tariff = Tariff::create([
                'tariff_number' => $this->generateTariffNumber(),
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'carriage_name' => $request->carriage_name,
                'company' => $request->company,
                'loading_point' => $request->loading_point,
                'destination' => $request->destination,
                'company_freight_rate' => $request->company_freight_rate,
                'vehicle_freight_rate' => $request->vehicle_freight_rate,
                'company_shortage_rate' => $request->company_shortage_rate,
                'vehicle_shortage_rate' => $request->vehicle_shortage_rate,
                'created_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Tariff creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tariff: ' . $e->getMessage()])
                ->withInput();
        }

        // Log the action
        AuditLog::log('create', $tariff);

        return redirect()->route('tariffs.index')
            ->with('success', 'Tariff created successfully.');
    }

    public function show(Tariff $tariff)
    {
        $this->authorize('view-tariffs');
        
        $tariff->load(['creator']);
        
        return view('tariffs.show', compact('tariff'));
    }

    public function edit(Tariff $tariff)
    {
        $this->authorize('edit-tariffs');
        
        $carriages = MasterData::where('type', 'carriage')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $companies = MasterData::where('type', 'company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $loadingPoints = MasterData::where('type', 'loading_point')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $destinations = MasterData::where('type', 'destination')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tariffs.edit', compact('tariff', 'carriages', 'companies', 'loadingPoints', 'destinations'));
    }

    public function update(Request $request, Tariff $tariff)
    {
        $this->authorize('edit-tariffs');
        
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'carriage_name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'loading_point' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'company_freight_rate' => 'required|numeric|min:0',
            'vehicle_freight_rate' => 'nullable|numeric|min:0',
            'company_shortage_rate' => 'required|numeric|min:0',
            'vehicle_shortage_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $tariff->toArray();

        $tariff->update([
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'carriage_name' => $request->carriage_name,
            'company' => $request->company,
            'loading_point' => $request->loading_point,
            'destination' => $request->destination,
            'company_freight_rate' => $request->company_freight_rate,
            'vehicle_freight_rate' => $request->vehicle_freight_rate,
            'company_shortage_rate' => $request->company_shortage_rate,
            'vehicle_shortage_rate' => $request->vehicle_shortage_rate,
        ]);

        // Log the action
        AuditLog::log('update', $tariff, $oldValues, $tariff->fresh()->toArray());

        return redirect()->route('tariffs.index')
            ->with('success', 'Tariff updated successfully.');
    }

    public function destroy(Tariff $tariff)
    {
        $this->authorize('delete-tariffs');
        
        // Log the action
        AuditLog::log('delete', $tariff);
        
        $tariff->delete();

        return redirect()->route('tariffs.index')
            ->with('success', 'Tariff deleted successfully.');
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

    /**
     * Generate unique tariff number
     */
    private function generateTariffNumber()
    {
        $prefix = 'TRF';
        $year = date('Y');
        $month = date('m');
        
        do {
            $lastTariff = Tariff::where('tariff_number', 'like', "{$prefix}{$year}{$month}%")
                ->orderBy('tariff_number', 'desc')
                ->first();
            
            if ($lastTariff) {
                $lastNumber = (int) substr($lastTariff->tariff_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $tariffNumber = $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            // Check if this number already exists (race condition protection)
            $exists = Tariff::where('tariff_number', $tariffNumber)->exists();
            
        } while ($exists);
        
        return $tariffNumber;
    }
}
