<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-vehicles');
        
        $query = Vehicle::with('owner')->where('is_active', true);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('vrn', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('driver_contact', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by expiring documents
        if ($request->get('filter') === 'expiring') {
            $fifteenDaysFromNow = now()->addDays(15);
            $query->where(function ($q) use ($fifteenDaysFromNow) {
                $q->where(function ($subQ) use ($fifteenDaysFromNow) {
                    $subQ->whereNotNull('token_tax_expiry')
                         ->where('token_tax_expiry', '<=', $fifteenDaysFromNow);
                })
                ->orWhere(function ($subQ) use ($fifteenDaysFromNow) {
                    $subQ->whereNotNull('dip_chart_expiry')
                         ->where('dip_chart_expiry', '<=', $fifteenDaysFromNow);
                })
                ->orWhere(function ($subQ) use ($fifteenDaysFromNow) {
                    $subQ->whereNotNull('tracker_expiry')
                         ->where('tracker_expiry', '<=', $fifteenDaysFromNow);
                });
            });
        }
        
        // Filter by document type
        if ($request->filled('document_type')) {
            $documentType = $request->get('document_type');
            $fifteenDaysFromNow = now()->addDays(15);
            
            switch ($documentType) {
                case 'tracker':
                    $query->whereNotNull('tracker_expiry')
                          ->where('tracker_expiry', '<=', $fifteenDaysFromNow);
                    break;
                case 'dip_chart':
                    $query->whereNotNull('dip_chart_expiry')
                          ->where('dip_chart_expiry', '<=', $fifteenDaysFromNow);
                    break;
                case 'token_tax':
                    $query->whereNotNull('token_tax_expiry')
                          ->where('token_tax_expiry', '<=', $fifteenDaysFromNow);
                    break;
            }
        }
        
        $perPage = $request->get('per_page', 15);
        $vehicles = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $this->authorize('create-vehicles');
        
        $owners = VehicleOwner::where('is_active', true)->get();
        
        return view('vehicles.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-vehicles');
        
        $validator = Validator::make($request->all(), [
            'vrn' => 'required|string|unique:vehicles,vrn',
            'owner_id' => 'required|exists:vehicle_owners,id',
            'driver_name' => 'required|string|max:255',
            'driver_contact' => 'required|string|max:20',
            'capacity' => 'required|numeric|min:0',
            'engine_number' => 'nullable|string|max:255',
            'chassis_number' => 'nullable|string|max:255',
            'token_tax_expiry' => 'nullable|date',
            'dip_chart_expiry' => 'nullable|date',
            'induction_date' => 'nullable|date',
            'tracker_name' => 'nullable|string|max:255',
            'tracker_link' => 'nullable|url',
            'tracker_id' => 'nullable|string|max:255',
            'tracker_password' => 'nullable|string|max:255',
            'tracker_expiry' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle = Vehicle::create([
            'vrn' => $request->vrn,
            'owner_id' => $request->owner_id,
            'driver_name' => $request->driver_name,
            'driver_contact' => $request->driver_contact,
            'capacity' => $request->capacity,
            'engine_number' => $request->engine_number,
            'chassis_number' => $request->chassis_number,
            'token_tax_expiry' => $request->token_tax_expiry,
            'dip_chart_expiry' => $request->dip_chart_expiry,
            'induction_date' => $request->induction_date,
            'tracker_name' => $request->tracker_name,
            'tracker_link' => $request->tracker_link,
            'tracker_id' => $request->tracker_id,
            'tracker_password' => $request->tracker_password,
            'tracker_expiry' => $request->tracker_expiry,
            'is_active' => true,
        ]);

        // Log the action
        AuditLog::log('create', $vehicle);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorize('view-vehicles');
        
        $vehicle->load('owner', 'journeyVouchers', 'vehicleBills');
        
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorize('edit-vehicles');
        
        $owners = VehicleOwner::where('is_active', true)->get();
        
        return view('vehicles.edit', compact('vehicle', 'owners'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('edit-vehicles');
        
        $validator = Validator::make($request->all(), [
            'vrn' => 'required|string|unique:vehicles,vrn,' . $vehicle->id,
            'owner_id' => 'required|exists:vehicle_owners,id',
            'driver_name' => 'required|string|max:255',
            'driver_contact' => 'required|string|max:20',
            'capacity' => 'required|numeric|min:0',
            'engine_number' => 'nullable|string|max:255',
            'chassis_number' => 'nullable|string|max:255',
            'token_tax_expiry' => 'nullable|date',
            'dip_chart_expiry' => 'nullable|date',
            'induction_date' => 'nullable|date',
            'tracker_name' => 'nullable|string|max:255',
            'tracker_link' => 'nullable|url',
            'tracker_id' => 'nullable|string|max:255',
            'tracker_password' => 'nullable|string|max:255',
            'tracker_expiry' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $vehicle->toArray();
        
        $vehicle->update([
            'vrn' => $request->vrn,
            'owner_id' => $request->owner_id,
            'driver_name' => $request->driver_name,
            'driver_contact' => $request->driver_contact,
            'capacity' => $request->capacity,
            'engine_number' => $request->engine_number,
            'chassis_number' => $request->chassis_number,
            'token_tax_expiry' => $request->token_tax_expiry,
            'dip_chart_expiry' => $request->dip_chart_expiry,
            'induction_date' => $request->induction_date,
            'tracker_name' => $request->tracker_name,
            'tracker_link' => $request->tracker_link,
            'tracker_id' => $request->tracker_id,
            'tracker_password' => $request->tracker_password,
            'tracker_expiry' => $request->tracker_expiry,
        ]);

        // Log the action
        AuditLog::log('update', $vehicle, $oldValues, $vehicle->fresh()->toArray());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete-vehicles');
        
        // Soft delete by setting is_active to false
        $vehicle->update(['is_active' => false]);
        
        // Log the action
        AuditLog::log('delete', $vehicle);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $vehicles = Vehicle::with('owner')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('vrn', 'like', "%{$query}%")
                  ->orWhere('driver_name', 'like', "%{$query}%")
                  ->orWhere('driver_contact', 'like', "%{$query}%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($query) {
                      $ownerQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get(['id', 'vrn', 'driver_name', 'driver_contact', 'owner_id']);

        return response()->json($vehicles);
    }

    public function expiringDocuments()
    {
        $vehicles = Vehicle::where('is_active', true)
            ->get()
            ->filter(function ($vehicle) {
                return $vehicle->hasExpiringDocuments();
            })
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'vrn' => $vehicle->vrn,
                    'owner' => $vehicle->owner->name,
                    'expiring_documents' => $vehicle->getExpiringDocuments()
                ];
            });

        return response()->json($vehicles);
    }
}
