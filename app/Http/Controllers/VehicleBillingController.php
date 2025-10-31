<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleBill;
use App\Models\Vehicle;
use App\Models\JourneyVoucher;
use App\Models\CashBook;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VehicleBillingController extends Controller
{
    public function index()
    {
        $this->authorize('view-vehicle-billing');
        
        $vehicleBills = VehicleBill::with(['vehicle.owner', 'creator'])
            ->orderBy('billing_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vehicle-billing.index', compact('vehicleBills'));
    }

    public function create()
    {
        $this->authorize('create-vehicle-billing');
        
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        return view('vehicle-billing.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-vehicle-billing');
        
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'billing_month' => 'required|string|max:255',
            'freight_entries' => 'required|array|min:1',
            'freight_entries.*' => 'exists:journey_vouchers,id',
            'advance_entries' => 'nullable|array',
            'advance_entries.*' => 'exists:cash_books,id',
            'expense_entries' => 'nullable|array',
            'expense_entries.*' => 'exists:cash_books,id',
            'bill_munshiyana' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get freight entries
        $freightEntries = JourneyVoucher::whereIn('id', $request->freight_entries)->get();
        $totalFreight = $freightEntries->sum('total_amount');

        // Get advance entries
        $advanceEntries = CashBook::whereIn('id', $request->advance_entries ?? [])->get();
        $totalAdvance = $advanceEntries->sum('amount');

        // Get expense entries
        $expenseEntries = CashBook::whereIn('id', $request->expense_entries ?? [])->get();
        $totalExpense = $expenseEntries->sum('amount');

        // Get previous bill balance
        $previousBill = VehicleBill::where('vehicle_id', $request->vehicle_id)
            ->where('is_finalized', true)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $previousBillBalance = $previousBill ? $previousBill->total_vehicle_balance : 0;

        // Calculate totals
        $grossProfit = $totalFreight - $totalAdvance - $totalExpense;
        $netProfit = $grossProfit - ($request->bill_munshiyana ?? 0);
        $totalVehicleBalance = $previousBillBalance + $netProfit;

        $vehicleBill = VehicleBill::create([
            'vehicle_id' => $request->vehicle_id,
            'billing_month' => $request->billing_month,
            'previous_bill_balance' => $previousBillBalance,
            'total_freight' => $totalFreight,
            'total_advance' => $totalAdvance,
            'total_expense' => $totalExpense,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'total_vehicle_balance' => $totalVehicleBalance,
            'created_by' => auth()->id(),
        ]);

        // Mark freight entries as billed
        JourneyVoucher::whereIn('id', $request->freight_entries)->update(['is_billed' => true]);

        // Log the action
        AuditLog::log('create', $vehicleBill);

        return redirect()->route('vehicle-billing.show', $vehicleBill)
            ->with('success', 'Vehicle bill created successfully.');
    }

    public function show(VehicleBill $vehicleBill)
    {
        $this->authorize('view-vehicle-billing');
        
        $vehicleBill->load(['vehicle.owner', 'creator']);
        
        // Get related entries
        $freightEntries = JourneyVoucher::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('is_billed', true)
            ->where('billing_month', $vehicleBill->billing_month)
            ->get();
            
        $advanceEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Advance')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();
            
        $expenseEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Expense')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();

        return view('vehicle-billing.show', compact('vehicleBill', 'freightEntries', 'advanceEntries', 'expenseEntries'));
    }

    public function finalize(VehicleBill $vehicleBill)
    {
        $this->authorize('finalize-vehicle-billing');
        
        if ($vehicleBill->is_finalized) {
            return redirect()->back()
                ->with('error', 'This bill is already finalized.');
        }

        $vehicleBill->update([
            'is_finalized' => true,
            'status' => 'final',
        ]);

        // Log the action
        AuditLog::log('finalize', $vehicleBill);

        return redirect()->back()
            ->with('success', 'Vehicle bill finalized successfully.');
    }

    public function print(VehicleBill $vehicleBill)
    {
        $this->authorize('print-vehicle-bills');
        
        $vehicleBill->load(['vehicle.owner', 'creator']);
        
        // Get related entries
        $freightEntries = JourneyVoucher::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('is_billed', true)
            ->where('billing_month', $vehicleBill->billing_month)
            ->get();
            
        $advanceEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Advance')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();
            
        $expenseEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Expense')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();

        // Log the action
        AuditLog::log('print', $vehicleBill);

        return view('vehicle-billing.print', compact('vehicleBill', 'freightEntries', 'advanceEntries', 'expenseEntries'));
    }

    public function getBillingData(Request $request)
    {
        $vehicleId = $request->get('vehicle_id');
        $billingMonth = $request->get('billing_month');
        
        if (!$vehicleId || !$billingMonth) {
            return response()->json(['data' => null]);
        }

        // Get unbilled freight entries
        $freightEntries = JourneyVoucher::where('vehicle_id', $vehicleId)
            ->where('is_processed', true)
            ->where('is_billed', false)
            ->where('billing_month', $billingMonth)
            ->get();

        // Get unbilled advance entries
        $advanceEntries = CashBook::where('vehicle_id', $vehicleId)
            ->where('payment_type', 'Advance')
            ->where('entry_date', '>=', $billingMonth . '-01')
            ->where('entry_date', '<=', Carbon::parse($billingMonth . '-01')->endOfMonth())
            ->get();

        // Get unbilled expense entries
        $expenseEntries = CashBook::where('vehicle_id', $vehicleId)
            ->where('payment_type', 'Expense')
            ->where('entry_date', '>=', $billingMonth . '-01')
            ->where('entry_date', '<=', Carbon::parse($billingMonth . '-01')->endOfMonth())
            ->get();

        return response()->json([
            'freight_entries' => $freightEntries,
            'advance_entries' => $advanceEntries,
            'expense_entries' => $expenseEntries,
        ]);
    }

    public function exportWord(VehicleBill $vehicleBill)
    {
        $this->authorize('view-vehicle-billing');
        
        $vehicleBill->load(['vehicle.owner', 'creator']);
        
        // Get related entries
        $freightEntries = JourneyVoucher::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('is_billed', true)
            ->where('billing_month', $vehicleBill->billing_month)
            ->get();
            
        $advanceEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Advance')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();
            
        $expenseEntries = CashBook::where('vehicle_id', $vehicleBill->vehicle_id)
            ->where('payment_type', 'Expense')
            ->where('entry_date', '>=', $vehicleBill->billing_month . '-01')
            ->where('entry_date', '<=', Carbon::parse($vehicleBill->billing_month . '-01')->endOfMonth())
            ->get();

        $printService = new \App\Services\PrintExportService();
        $printInfo = $printService->getPrintInfo($vehicleBill);
        
        // Prepare data for export
        $data = [
            [
                'field' => 'Bill Number',
                'value' => $vehicleBill->bill_number
            ],
            [
                'field' => 'Vehicle (VRN)',
                'value' => $vehicleBill->vehicle->vrn ?? 'N/A'
            ],
            [
                'field' => 'Owner',
                'value' => $vehicleBill->vehicle->owner->name ?? 'N/A'
            ],
            [
                'field' => 'Billing Month',
                'value' => $vehicleBill->billing_month
            ],
            [
                'field' => 'Total Freight',
                'value' => '₨' . number_format($vehicleBill->total_freight, 2)
            ],
            [
                'field' => 'Total Advance',
                'value' => '₨' . number_format($vehicleBill->total_advance, 2)
            ],
            [
                'field' => 'Total Expense',
                'value' => '₨' . number_format($vehicleBill->total_expense, 2)
            ],
            [
                'field' => 'Total Shortage',
                'value' => '₨' . number_format($vehicleBill->total_shortage, 2)
            ],
            [
                'field' => 'Gross Profit',
                'value' => '₨' . number_format($vehicleBill->gross_profit, 2)
            ],
            [
                'field' => 'Net Profit',
                'value' => '₨' . number_format($vehicleBill->net_profit, 2)
            ],
            [
                'field' => 'Previous Balance',
                'value' => '₨' . number_format($vehicleBill->previous_bill_balance, 2)
            ],
            [
                'field' => 'Total Vehicle Balance',
                'value' => '₨' . number_format($vehicleBill->total_vehicle_balance, 2)
            ],
            [
                'field' => 'Status',
                'value' => $vehicleBill->is_finalized ? 'Finalized' : 'Draft'
            ],
            [
                'field' => 'Printed By',
                'value' => $printInfo['printed_by']
            ],
            [
                'field' => 'Created By',
                'value' => $printInfo['created_by']
            ],
            [
                'field' => 'Print Date',
                'value' => $printInfo['print_date']
            ]
        ];

        $columns = [
            'Field' => 'field',
            'Value' => 'value'
        ];

        try {
            $result = $printService->generateWord($data, 'Vehicle Bill - ' . $vehicleBill->bill_number, $columns);
            
            return response()->download($result['file'], $result['filename'], [
                'Content-Type' => $result['mime']
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to export to Word: ' . $e->getMessage()]);
        }
    }
}
