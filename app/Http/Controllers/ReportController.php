<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\JourneyVoucher;
use App\Models\CashBook;
use App\Models\VehicleBill;
use App\Models\ChartOfAccount;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('view-reports');
        
        return view('reports.index');
    }

    public function generalLedger(Request $request)
    {
        $this->authorize('view-reports');
        
        $accountId = $request->get('account_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('account_name')->get();
        
        $query = CashBook::with(['account', 'vehicle.owner', 'creator']);
        
        if ($accountId) {
            $query->where('account_id', $accountId);
        }
        
        if ($dateFrom) {
            $query->where('entry_date', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('entry_date', '<=', $dateTo);
        }
        
        $transactions = $query->orderBy('entry_date', 'desc')->paginate(50);
        
        return view('reports.general-ledger', compact('transactions', 'accounts'));
    }

    public function companySummary(Request $request)
    {
        $this->authorize('view-reports');
        
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());
        
        $companies = JourneyVoucher::whereBetween('journey_date', [$dateFrom, $dateTo])
            ->select('company')
            ->distinct()
            ->pluck('company');
        
        $summary = [];
        
        foreach ($companies as $company) {
            $vouchers = JourneyVoucher::where('company', $company)
                ->whereBetween('journey_date', [$dateFrom, $dateTo])
                ->get();
            
            $summary[] = [
                'company' => $company,
                'total_billed' => $vouchers->where('is_billed', true)->sum('total_amount'),
                'total_received' => $vouchers->where('is_billed', true)->sum('freight_amount'),
                'total_shortage' => $vouchers->sum('shortage_amount'),
                'total_deduction' => $vouchers->sum('commission_amount'),
                'trip_count' => $vouchers->count(),
            ];
        }
        
        // Convert array to collection for proper method calls
        $summary = collect($summary);
        
        return view('reports.company-summary', compact('summary', 'dateFrom', 'dateTo'));
    }

    public function carriageSummary(Request $request)
    {
        $this->authorize('view-reports');
        
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());
        
        $carriages = JourneyVoucher::whereBetween('journey_date', [$dateFrom, $dateTo])
            ->select('carriage_name')
            ->distinct()
            ->pluck('carriage_name');
        
        $summary = [];
        
        foreach ($carriages as $carriage) {
            $vouchers = JourneyVoucher::where('carriage_name', $carriage)
                ->whereBetween('journey_date', [$dateFrom, $dateTo])
                ->get();
            
            $summary[] = [
                'carriage_name' => $carriage,
                'trip_count' => $vouchers->count(),
                'total_freight' => $vouchers->sum('freight_amount'),
                'total_shortage' => $vouchers->sum('shortage_amount'),
                'total_commission' => $vouchers->sum('commission_amount'),
                'net_amount' => $vouchers->sum('total_amount'),
            ];
        }
        
        // Convert array to collection for proper method calls
        $summary = collect($summary);
        
        return view('reports.carriage-summary', compact('summary', 'dateFrom', 'dateTo'));
    }

    public function monthlyVehicleBills(Request $request)
    {
        $this->authorize('view-reports');
        
        $query = VehicleBill::with(['vehicle.owner', 'creator']);
        
        if ($request->filled('vrn')) {
            $query->whereHas('vehicle', function($q) use ($request) {
                $q->where('vrn', 'like', '%' . $request->vrn . '%');
            });
        }
        
        if ($request->filled('owner_name')) {
            $query->whereHas('vehicle.owner', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->owner_name . '%');
            });
        }
        
        if ($request->filled('billing_month')) {
            $query->where('billing_month', $request->billing_month);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $bills = $query->orderBy('billing_month', 'desc')->paginate(25);
        
        return view('reports.monthly-vehicle-bills', compact('bills'));
    }

    public function debitAccountNotifications()
    {
        $this->authorize('view-reports');
        
        // Get all accounts with debit balances
        $debitAccounts = DB::table('cash_books')
            ->join('chart_of_accounts', 'cash_books.account_id', '=', 'chart_of_accounts.id')
            ->select('chart_of_accounts.account_name', 'chart_of_accounts.account_code')
            ->selectRaw('SUM(CASE WHEN transaction_type = "payment" THEN amount ELSE -amount END) as balance')
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.account_name', 'chart_of_accounts.account_code')
            ->having('balance', '>', 0)
            ->get();
        
        return view('reports.debit-account-notifications', compact('debitAccounts'));
    }

    public function companyTrialBalance(Request $request)
    {
        $this->authorize('view-reports');
        
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());
        
        $trialBalance = DB::table('cash_books')
            ->join('chart_of_accounts', 'cash_books.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('cash_books.entry_date', [$dateFrom, $dateTo])
            ->select('chart_of_accounts.account_name', 'chart_of_accounts.account_code')
            ->selectRaw('SUM(CASE WHEN transaction_type = "payment" THEN amount ELSE 0 END) as total_debits')
            ->selectRaw('SUM(CASE WHEN transaction_type = "receive" THEN amount ELSE 0 END) as total_credits')
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.account_name', 'chart_of_accounts.account_code')
            ->orderBy('chart_of_accounts.account_name')
            ->get();
        
        $totalDebits = $trialBalance->sum('total_debits');
        $totalCredits = $trialBalance->sum('total_credits');
        
        return view('reports.company-trial-balance', compact('trialBalance', 'totalDebits', 'totalCredits', 'dateFrom', 'dateTo'));
    }

    public function unattachedShortages(Request $request)
    {
        $this->authorize('view-reports');
        
        $shortages = JourneyVoucher::with(['vehicle.owner'])
            ->where('shortage_amount', '>', 0)
            ->where('is_billed', false)
            ->orderBy('journey_date', 'desc')
            ->paginate(25);
        
        return view('reports.unattached-shortages', compact('shortages'));
    }

    public function incomeReports(Request $request)
    {
        $this->authorize('view-reports');
        
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());
        
        // Freight Difference Income
        $freightDifference = JourneyVoucher::whereBetween('journey_date', [$dateFrom, $dateTo])
            ->whereRaw('company_freight_rate > vehicle_freight_rate')
            ->selectRaw('SUM((company_freight_rate - vehicle_freight_rate) * decant_capacity) as total_income')
            ->first();
        
        // Shortage Difference Income (using shortage_rate as base rate)
        $shortageDifference = JourneyVoucher::whereBetween('journey_date', [$dateFrom, $dateTo])
            ->where('shortage_rate', '>', 0)
            ->selectRaw('SUM(shortage_rate * shortage_quantity) as total_income')
            ->first();
        
        // Company-wise Income
        $companyIncome = JourneyVoucher::whereBetween('journey_date', [$dateFrom, $dateTo])
            ->select('company')
            ->selectRaw('SUM(commission_amount) as commission_income')
            ->selectRaw('SUM((company_freight_rate - COALESCE(vehicle_freight_rate, company_freight_rate)) * decant_capacity) as freight_difference')
            ->selectRaw('SUM(shortage_rate * shortage_quantity) as shortage_difference')
            ->groupBy('company')
            ->get();
        
        return view('reports.income-reports', compact('freightDifference', 'shortageDifference', 'companyIncome', 'dateFrom', 'dateTo'));
    }

    public function pendingTrips(Request $request)
    {
        $this->authorize('view-reports');
        
        $unprocessedTrips = JourneyVoucher::with(['vehicle.owner'])
            ->where('is_processed', false)
            ->where('is_direct_bill', false)
            ->orderBy('journey_date', 'desc')
            ->paginate(25);
        
        $readyForBilling = JourneyVoucher::with(['vehicle.owner'])
            ->where('is_processed', true)
            ->where('is_billed', false)
            ->orderBy('journey_date', 'desc')
            ->paginate(25);
        
        return view('reports.pending-trips', compact('unprocessedTrips', 'readyForBilling'));
    }

    public function outstandingAdvances(Request $request)
    {
        $this->authorize('view-reports');
        
        $advances = CashBook::with(['vehicle.owner', 'account'])
            ->where('payment_type', 'Advance')
            ->where('is_billed', false)
            ->orderBy('entry_date', 'desc')
            ->paginate(25);
        
        $expenses = CashBook::with(['vehicle.owner', 'account'])
            ->where('payment_type', 'Expense')
            ->where('is_billed', false)
            ->orderBy('entry_date', 'desc')
            ->paginate(25);
        
        return view('reports.outstanding-advances', compact('advances', 'expenses'));
    }

    public function vehicleDatabase(Request $request)
    {
        $this->authorize('view-reports');
        
        $query = Vehicle::with(['owner']);
        
        if ($request->filled('vrn')) {
            $query->where('vrn', 'like', '%' . $request->vrn . '%');
        }
        
        if ($request->filled('owner_name')) {
            $query->whereHas('owner', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->owner_name . '%');
            });
        }
        
        $vehicles = $query->orderBy('vrn')->paginate(25);
        
        return view('reports.vehicle-database', compact('vehicles'));
    }

    public function vehicleOwnerLedger(Request $request)
    {
        $this->authorize('view-reports');
        
        $ownerId = $request->get('owner_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $owners = VehicleOwner::where('is_active', true)->orderBy('name')->get();
        
        $owner = null;
        $transactions = collect();
        
        if ($ownerId) {
            $owner = VehicleOwner::with(['vehicles'])->find($ownerId);
            
            if ($owner) {
                $vehicleIds = $owner->vehicles->pluck('id');
                
                // Get journey voucher transactions
                $journeyTransactions = JourneyVoucher::whereIn('vehicle_id', $vehicleIds)
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('journey_date', '>=', $dateFrom);
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('journey_date', '<=', $dateTo);
                    })
                    ->get()
                    ->map(function($voucher) {
                        return [
                            'date' => $voucher->journey_date,
                            'vrn' => $voucher->vehicle->vrn,
                            'description' => "Journey: {$voucher->loading_point} to {$voucher->destination}",
                            'debit' => $voucher->freight_amount,
                            'credit' => 0,
                            'type' => 'journey'
                        ];
                    });
                
                // Get cash book transactions
                $cashTransactions = CashBook::whereIn('vehicle_id', $vehicleIds)
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('entry_date', '>=', $dateFrom);
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('entry_date', '<=', $dateTo);
                    })
                    ->get()
                    ->map(function($cash) {
                        return [
                            'date' => $cash->entry_date,
                            'vrn' => $cash->vehicle ? $cash->vehicle->vrn : 'N/A',
                            'description' => $cash->description,
                            'debit' => $cash->transaction_type === 'payment' ? $cash->amount : 0,
                            'credit' => $cash->transaction_type === 'receive' ? $cash->amount : 0,
                            'type' => 'cash'
                        ];
                    });
                
                $transactions = $journeyTransactions->concat($cashTransactions)
                    ->sortBy('date')
                    ->values();
            }
        }
        
        return view('reports.vehicle-owner-ledger', compact('owners', 'owner', 'transactions', 'dateFrom', 'dateTo'));
    }
}
