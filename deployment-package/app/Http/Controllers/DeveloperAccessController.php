<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeveloperAccess;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DeveloperAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-developer-access')->only('create', 'store', 'edit', 'update', 'destroy', 'approve', 'revoke');
    }

    /**
     * Display a listing of developer access requests
     */
    public function index(Request $request)
    {
        $query = DeveloperAccess::with(['creator', 'approver', 'revoker']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('access_type')) {
            $query->where('access_type', $request->access_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('developer_name', 'like', "%{$search}%")
                  ->orWhere('developer_email', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }
        
        $accesses = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('developer-access.index', compact('accesses'));
    }

    /**
     * Show the form for creating a new developer access request
     */
    public function create()
    {
        return view('developer-access.create');
    }

    /**
     * Store a newly created developer access request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'developer_name' => 'required|string|max:255',
            'developer_email' => 'required|email|max:255',
            'access_type' => 'required|in:read_only,limited_write,full_access,emergency',
            'permissions' => 'nullable|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DeveloperAccess::create([
                'developer_name' => $request->developer_name,
                'developer_email' => $request->developer_email,
                'access_type' => $request->access_type,
                'permissions' => $request->permissions,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('developer-access.index')
                ->with('success', 'Developer access request created successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create access request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Approve developer access
     */
    public function approve(Request $request, DeveloperAccess $developerAccess)
    {
        if ($developerAccess->status !== DeveloperAccess::STATUS_PENDING) {
            return redirect()->back()
                ->withErrors(['error' => 'Only pending requests can be approved.']);
        }

        try {
            $developerAccess->update([
                'status' => DeveloperAccess::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            return redirect()->route('developer-access.index')
                ->with('success', 'Developer access approved successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to approve access: ' . $e->getMessage()]);
        }
    }

    /**
     * Activate developer access
     */
    public function activate(DeveloperAccess $developerAccess)
    {
        if ($developerAccess->status !== DeveloperAccess::STATUS_APPROVED) {
            return redirect()->back()
                ->withErrors(['error' => 'Only approved requests can be activated.']);
        }

        try {
            $developerAccess->update([
                'status' => DeveloperAccess::STATUS_ACTIVE
            ]);

            return redirect()->route('developer-access.index')
                ->with('success', 'Developer access activated successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to activate access: ' . $e->getMessage()]);
        }
    }

    /**
     * Revoke developer access
     */
    public function revoke(Request $request, DeveloperAccess $developerAccess)
    {
        if (!in_array($developerAccess->status, [DeveloperAccess::STATUS_ACTIVE, DeveloperAccess::STATUS_APPROVED])) {
            return redirect()->back()
                ->withErrors(['error' => 'Only active or approved access can be revoked.']);
        }

        $validator = Validator::make($request->all(), [
            'revoke_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $developerAccess->update([
                'status' => DeveloperAccess::STATUS_REVOKED,
                'revoked_by' => Auth::id(),
                'revoked_at' => now(),
                'reason' => $developerAccess->reason . "\n\nRevoke Reason: " . $request->revoke_reason
            ]);

            return redirect()->route('developer-access.index')
                ->with('success', 'Developer access revoked successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to revoke access: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified developer access
     */
    public function destroy(DeveloperAccess $developerAccess)
    {
        if ($developerAccess->status === DeveloperAccess::STATUS_ACTIVE) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete active access. Please revoke it first.']);
        }

        try {
            $developerAccess->delete();
            
            return redirect()->route('developer-access.index')
                ->with('success', 'Developer access deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete access: ' . $e->getMessage()]);
        }
    }

    /**
     * Get active developer accesses
     */
    public function getActiveAccesses()
    {
        $accesses = DeveloperAccess::active()
            ->with(['creator', 'approver'])
            ->get();
        
        return response()->json([
            'accesses' => $accesses
        ]);
    }

    /**
     * Get access statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => DeveloperAccess::count(),
            'pending' => DeveloperAccess::pending()->count(),
            'active' => DeveloperAccess::active()->count(),
            'expired' => DeveloperAccess::expired()->count(),
            'revoked' => DeveloperAccess::where('status', DeveloperAccess::STATUS_REVOKED)->count()
        ];
        
        return response()->json($stats);
    }

    /**
     * Auto-expire accesses
     */
    public function autoExpireAccesses()
    {
        $expiredCount = 0;
        
        DeveloperAccess::active()->get()->each(function($access) use (&$expiredCount) {
            if ($access->isExpired()) {
                $access->autoExpire();
                $expiredCount++;
            }
        });
        
        return response()->json([
            'expired_count' => $expiredCount,
            'message' => "Auto-expired {$expiredCount} accesses"
        ]);
    }
}
