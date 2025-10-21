<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleOwner;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;

class VehicleOwnerController extends Controller
{
    public function index()
    {
        $this->authorize('view-vehicle-owners');
        
        $owners = VehicleOwner::with('vehicles')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vehicle-owners.index', compact('owners'));
    }

    public function create()
    {
        $this->authorize('create-vehicle-owners');
        
        return view('vehicle-owners.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create-vehicle-owners');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|unique:vehicle_owners,cnic',
            'contact_number' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $owner = VehicleOwner::create([
            'name' => $request->name,
            'cnic' => $request->cnic,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Log the action
        AuditLog::log('create', $owner);

        return redirect()->route('vehicle-owners.index')
            ->with('success', 'Vehicle owner created successfully.');
    }

    public function show(VehicleOwner $vehicleOwner)
    {
        $this->authorize('view-vehicle-owners');
        
        $vehicleOwner->load('vehicles');
        
        return view('vehicle-owners.show', compact('vehicleOwner'));
    }

    public function edit(VehicleOwner $vehicleOwner)
    {
        $this->authorize('edit-vehicle-owners');
        
        return view('vehicle-owners.edit', compact('vehicleOwner'));
    }

    public function update(Request $request, VehicleOwner $vehicleOwner)
    {
        $this->authorize('edit-vehicle-owners');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|unique:vehicle_owners,cnic,' . $vehicleOwner->id,
            'contact_number' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $vehicleOwner->toArray();
        
        $vehicleOwner->update([
            'name' => $request->name,
            'cnic' => $request->cnic,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        // Log the action
        AuditLog::log('update', $vehicleOwner, $oldValues, $vehicleOwner->fresh()->toArray());

        return redirect()->route('vehicle-owners.index')
            ->with('success', 'Vehicle owner updated successfully.');
    }

    public function destroy(VehicleOwner $vehicleOwner)
    {
        $this->authorize('delete-vehicle-owners');
        
        // Soft delete by setting is_active to false
        $vehicleOwner->update(['is_active' => false]);
        
        // Log the action
        AuditLog::log('delete', $vehicleOwner);

        return redirect()->route('vehicle-owners.index')
            ->with('success', 'Vehicle owner deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $owners = VehicleOwner::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('cnic', 'like', "%{$query}%")
                  ->orWhere('contact_number', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'cnic', 'contact_number']);

        return response()->json($owners);
    }
}
