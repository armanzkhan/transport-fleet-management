<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashBook;
use App\Models\ChartOfAccount;
use App\Models\Vehicle;
use App\Models\AuditLog;
use App\Services\PrintExportService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-cash-book');
        
        $query = CashBook::with(['account', 'vehicle', 'creator']);
        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('cash_book_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }
        
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        // Calculate totals for filtered results before pagination
        $queryForTotals = clone $query;
        $totals = [
            'total_receives' => (clone $queryForTotals)->where('transaction_type', 'receive')->sum('amount'),
            'total_payments' => (clone $queryForTotals)->where('transaction_type', 'payment')->sum('amount'),
            'net_balance' => 0
        ];
        $totals['net_balance'] = $totals['total_receives'] - $totals['total_payments'];
        
        $perPage = $request->get('per_page', 15);
        $cashBooks = $query->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        // Get previous day balance (cash in hand from yesterday)
        $yesterday = now()->subDay()->format('Y-m-d');
        $previousDayBalance = CashBook::getPreviousDayBalance(now()->format('Y-m-d'));
        
        // Get today's cash in hand
        $todaysCashInHand = CashBook::where('entry_date', now()->format('Y-m-d'))
            ->orderBy('id', 'desc')
            ->value('total_cash_in_hand') ?? $previousDayBalance;
        
        // Get vehicles for filter dropdown
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        return view('cash-books.index', compact('cashBooks', 'totals', 'previousDayBalance', 'todaysCashInHand', 'vehicles'));
    }

    public function receive()
    {
        $this->authorize('create-cash-book');
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_name')
            ->get();
            
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        // Get today's previous balance as default
        $todaysPreviousBalance = CashBook::getPreviousDayBalance(date('Y-m-d'));

        return view('cash-books.receive', compact('accounts', 'vehicles', 'todaysPreviousBalance'));
    }

    public function payment()
    {
        $this->authorize('create-cash-book');
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_name')
            ->get();
            
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        // Get today's previous balance as default
        $todaysPreviousBalance = CashBook::getPreviousDayBalance(date('Y-m-d'));

        return view('cash-books.payment', compact('accounts', 'vehicles', 'todaysPreviousBalance'));
    }

    public function daily()
    {
        $this->authorize('create-cash-book');
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_name')
            ->get();
            
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        // Get today's previous balance as default
        $todaysPreviousBalance = CashBook::getPreviousDayBalance(date('Y-m-d'));

        return view('cash-books.daily', compact('accounts', 'vehicles', 'todaysPreviousBalance'));
    }

    public function storeReceive(Request $request)
    {
        $this->authorize('create-cash-book');
        
        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'transactions' => 'required|array|min:1',
            'transactions.*.account_id' => 'required|exists:chart_of_accounts,id',
            'transactions.*.vehicle_id' => 'nullable|exists:vehicles,id',
            'transactions.*.description' => 'required|string|max:500',
            'transactions.*.amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if cash book already exists for this date
        $existingCashBook = CashBook::where('entry_date', $request->entry_date)
            ->where('transaction_type', 'receive')
            ->first();

        if ($existingCashBook) {
            return redirect()->back()
                ->withErrors(['entry_date' => 'Cash book entry already exists for this date.'])
                ->withInput();
        }

        // Get previous day balance
        $previousBalance = CashBook::getPreviousDayBalance($request->entry_date);
        
        // Calculate total received amount
        $totalReceived = collect($request->transactions)->sum('amount');
        $totalCashInHand = CashBook::calculateTotalCashInHand($request->entry_date, $totalReceived, $previousBalance);

        // Create cash book entries
        foreach ($request->transactions as $transaction) {
            $cashBook = CashBook::create([
                'entry_date' => $request->entry_date,
                'transaction_type' => 'receive',
                'account_id' => $transaction['account_id'],
                'vehicle_id' => $transaction['vehicle_id'] ?? null,
                'description' => $transaction['description'],
                'amount' => $transaction['amount'],
                'previous_day_balance' => $previousBalance,
                'total_cash_in_hand' => $totalCashInHand,
                'created_by' => auth()->id(),
            ]);

            // Log the action
            AuditLog::log('create', $cashBook);
        }

        return redirect()->route('cash-books.index')
            ->with('success', 'Cash book receive entries created successfully.');
    }

    public function storePayment(Request $request)
    {
        $this->authorize('create-cash-book');
        
        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'transactions' => 'required|array|min:1',
            'transactions.*.account_id' => 'required|exists:chart_of_accounts,id',
            'transactions.*.vehicle_id' => 'nullable|exists:vehicles,id',
            'transactions.*.payment_type' => 'required|in:Advance,Expense,Shortage',
            'transactions.*.description' => 'required|string|max:500',
            'transactions.*.amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get previous day balance
        $previousBalance = CashBook::getPreviousDayBalance($request->entry_date);
        
        // Calculate total payment amount
        $totalPayment = collect($request->transactions)->sum('amount');
        $totalCashInHand = $previousBalance - $totalPayment;

        // Create cash book entries
        foreach ($request->transactions as $transaction) {
            $cashBook = CashBook::create([
                'entry_date' => $request->entry_date,
                'transaction_type' => 'payment',
                'account_id' => $transaction['account_id'],
                'vehicle_id' => $transaction['vehicle_id'] ?? null,
                'payment_type' => $transaction['payment_type'],
                'description' => $transaction['description'],
                'amount' => $transaction['amount'],
                'previous_day_balance' => $previousBalance,
                'total_cash_in_hand' => $totalCashInHand,
                'created_by' => auth()->id(),
            ]);

            // Log the action
            AuditLog::log('create', $cashBook);
        }

        return redirect()->route('cash-books.index')
            ->with('success', 'Cash book payment entries created successfully.');
    }

    public function storeDaily(Request $request)
    {
        $this->authorize('create-cash-book');
        
        // Check if at least one transaction type has data
        $hasReceives = !empty($request->receives);
        $hasPayments = !empty($request->payments);
        
        if (!$hasReceives && !$hasPayments) {
            return redirect()->back()
                ->withErrors(['error' => 'Please add at least one transaction (receive or payment).'])
                ->withInput();
        }

        $validationRules = [
            'entry_date' => 'required|date',
        ];

        // Add validation rules only if receives exist
        if ($hasReceives) {
            $validationRules['receives'] = 'array';
            $validationRules['receives.*.account_id'] = 'required|exists:chart_of_accounts,id';
            $validationRules['receives.*.vehicle_id'] = 'nullable|exists:vehicles,id';
            $validationRules['receives.*.description'] = 'required|string|max:500';
            $validationRules['receives.*.amount'] = 'required|numeric|min:0.01';
        }

        // Add validation rules only if payments exist
        if ($hasPayments) {
            $validationRules['payments'] = 'array';
            $validationRules['payments.*.account_id'] = 'required|exists:chart_of_accounts,id';
            $validationRules['payments.*.vehicle_id'] = 'nullable|exists:vehicles,id';
            $validationRules['payments.*.payment_type'] = 'required|in:Advance,Expense,Shortage';
            $validationRules['payments.*.description'] = 'required|string|max:500';
            $validationRules['payments.*.amount'] = 'required|numeric|min:0.01';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if entries already exist for this date
        $existingEntries = CashBook::where('entry_date', $request->entry_date)->count();
        if ($existingEntries > 0) {
            return redirect()->back()
                ->withErrors(['entry_date' => "Cash book entries already exist for this date ({$request->entry_date}). Found {$existingEntries} existing entries. Please use a different date or edit existing entries."])
                ->withInput();
        }

        // Get previous day balance
        $previousBalance = CashBook::getPreviousDayBalance($request->entry_date);
        
        // Calculate totals
        $totalReceived = collect($request->receives ?? [])->sum('amount');
        $totalPayment = collect($request->payments ?? [])->sum('amount');
        $totalCashInHand = $previousBalance + $totalReceived - $totalPayment;

        try {
            \DB::beginTransaction();

            // Create receive entries
            if ($request->receives) {
                foreach ($request->receives as $receive) {
                    $cashBook = CashBook::create([
                        'entry_date' => $request->entry_date,
                        'transaction_type' => 'receive',
                        'account_id' => $receive['account_id'],
                        'vehicle_id' => $receive['vehicle_id'] ?? null,
                        'payment_type' => 'Advance', // Default for receives
                        'description' => $receive['description'],
                        'amount' => $receive['amount'],
                        'previous_day_balance' => $previousBalance,
                        'total_cash_in_hand' => $totalCashInHand,
                        'created_by' => auth()->id(),
                    ]);

                    AuditLog::log('create', $cashBook);
                }
            }

            // Create payment entries
            if ($request->payments) {
                foreach ($request->payments as $payment) {
                    $cashBook = CashBook::create([
                        'entry_date' => $request->entry_date,
                        'transaction_type' => 'payment',
                        'account_id' => $payment['account_id'],
                        'vehicle_id' => $payment['vehicle_id'] ?? null,
                        'payment_type' => $payment['payment_type'],
                        'description' => $payment['description'],
                        'amount' => $payment['amount'],
                        'previous_day_balance' => $previousBalance,
                        'total_cash_in_hand' => $totalCashInHand,
                        'created_by' => auth()->id(),
                    ]);

                    AuditLog::log('create', $cashBook);
                }
            }

            \DB::commit();

            return redirect()->route('cash-books.index')
                ->with('success', 'Daily cash book entry created successfully.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create daily entry: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(CashBook $cashBook)
    {
        $this->authorize('view-cash-book');
        
        $cashBook->load(['account', 'vehicle.owner', 'creator']);
        
        return view('cash-books.show', compact('cashBook'));
    }

    public function edit(CashBook $cashBook)
    {
        $this->authorize('edit-cash-book');
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_name')
            ->get();
            
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        return view('cash-books.edit', compact('cashBook', 'accounts', 'vehicles'));
    }

    public function update(Request $request, CashBook $cashBook)
    {
        $this->authorize('edit-cash-book');
        
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:chart_of_accounts,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'payment_type' => 'nullable|in:Advance,Expense,Shortage',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $cashBook->toArray();
        
        $cashBook->update([
            'account_id' => $request->account_id,
            'vehicle_id' => $request->vehicle_id,
            'payment_type' => $request->payment_type,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        // Log the action
        AuditLog::log('update', $cashBook, $oldValues, $cashBook->fresh()->toArray());

        return redirect()->route('cash-books.index')
            ->with('success', 'Cash book entry updated successfully.');
    }

    public function destroy(CashBook $cashBook)
    {
        $this->authorize('delete-cash-book');
        
        // Log the action
        AuditLog::log('delete', $cashBook);
        
        $cashBook->delete();

        return redirect()->route('cash-books.index')
            ->with('success', 'Cash book entry deleted successfully.');
    }

    public function print(CashBook $cashBook)
    {
        $this->authorize('print-cash-vouchers');
        
        $cashBook->load(['account', 'vehicle.owner', 'creator']);
        
        // Log the action
        AuditLog::log('print', $cashBook);
        
        // Get print information with user IDs
        $printService = new PrintExportService();
        $printInfo = $printService->getPrintInfo($cashBook);
        
        return view('cash-books.print', compact('cashBook', 'printInfo'));
    }

    public function simple()
    {
        $this->authorize('create-cash-book');
        
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_name')
            ->get();
            
        $vehicles = Vehicle::where('is_active', true)
            ->with('owner')
            ->orderBy('vrn')
            ->get();

        return view('cash-books.simple', compact('accounts', 'vehicles'));
    }

    public function storeSimple(Request $request)
    {
        $this->authorize('create-cash-book');
        
        $validationRules = [
            'entry_date' => 'required|date',
            'transaction_type' => 'required|in:receive,payment',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
        ];

        // Add payment_type validation only if transaction_type is payment
        if ($request->transaction_type === 'payment') {
            $validationRules['payment_type'] = 'required|in:Advance,Expense,Shortage';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get previous day balance
            $previousBalance = CashBook::getPreviousDayBalance($request->entry_date);
            
            // Calculate total cash in hand
            $totalCashInHand = $previousBalance;
            if ($request->transaction_type === 'receive') {
                $totalCashInHand += $request->amount;
            } else {
                $totalCashInHand -= $request->amount;
            }

            // Create cash book entry
            $cashBook = CashBook::create([
                'entry_date' => $request->entry_date,
                'transaction_type' => $request->transaction_type,
                'account_id' => $request->account_id,
                'vehicle_id' => $request->vehicle_id,
                'payment_type' => $request->transaction_type === 'payment' ? $request->payment_type : 'Advance',
                'description' => $request->description,
                'amount' => $request->amount,
                'previous_day_balance' => $previousBalance,
                'total_cash_in_hand' => $totalCashInHand,
                'created_by' => auth()->id(),
            ]);

            // Log the action
            AuditLog::log('create', $cashBook);

            return redirect()->route('cash-books.index')
                ->with('success', "Cash book entry created successfully! Entry Number: {$cashBook->cash_book_number}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create entry: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function getPreviousDayBalance(Request $request)
    {
        $date = $request->get('date');
        $balance = CashBook::getPreviousDayBalance($date);
        
        return response()->json(['balance' => $balance]);
    }


    /**
     * Export Cash Book to CSV
     */
    public function exportCSV(Request $request)
    {
        $this->authorize('view-cash-book');
        
        $query = CashBook::with(['account', 'vehicle', 'creator']);
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('cash_book_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }
        
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        $cashBooks = $query->orderBy('entry_date', 'desc')->get();
        
        $printService = new PrintExportService();
        $columns = [
            'Entry Date' => 'entry_date',
            'Cash Book No' => 'cash_book_number',
            'TRX Number' => 'transaction_number',
            'Account' => 'account.account_name',
            'Vehicle' => 'vehicle.vrn',
            'Description' => 'description',
            'Amount' => 'amount',
            'Type' => 'transaction_type',
            'Payment Type' => 'payment_type',
            'Created By' => 'creator.name'
        ];
        
        $exportData = $cashBooks->map(function($item) {
            return [
                'entry_date' => $item->entry_date->format('Y-m-d'),
                'cash_book_number' => $item->cash_book_number,
                'transaction_number' => $item->transaction_number,
                'account_name' => $item->account->account_name ?? 'N/A',
                'vehicle_vrn' => $item->vehicle->vrn ?? 'N/A',
                'description' => $item->description,
                'amount' => '₨' . number_format($item->amount, 2),
                'transaction_type' => ucfirst($item->transaction_type),
                'payment_type' => ucfirst($item->payment_type ?? 'N/A'),
                'created_by' => $item->creator->name ?? 'System'
            ];
        });
        
        $result = $printService->generateCSV($exportData, 'Cash Book Export', $columns);
        
        return response()->download($result['file'], $result['filename'], [
            'Content-Type' => $result['mime']
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export Cash Book to HTML
     */
    public function exportHTML(Request $request)
    {
        $this->authorize('view-cash-book');
        
        $query = CashBook::with(['account', 'vehicle', 'creator']);
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('cash_book_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }
        
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        $cashBooks = $query->orderBy('entry_date', 'desc')->get();
        
        $printService = new PrintExportService();
        $columns = [
            'Entry Date' => 'entry_date',
            'Cash Book No' => 'cash_book_number',
            'TRX Number' => 'transaction_number',
            'Account' => 'account.account_name',
            'Vehicle' => 'vehicle.vrn',
            'Description' => 'description',
            'Amount' => 'amount',
            'Type' => 'transaction_type',
            'Payment Type' => 'payment_type',
            'Created By' => 'creator.name'
        ];
        
        $exportData = $cashBooks->map(function($item) {
            return [
                'entry_date' => $item->entry_date->format('Y-m-d'),
                'cash_book_number' => $item->cash_book_number,
                'transaction_number' => $item->transaction_number,
                'account_name' => $item->account->account_name ?? 'N/A',
                'vehicle_vrn' => $item->vehicle->vrn ?? 'N/A',
                'description' => $item->description,
                'amount' => '₨' . number_format($item->amount, 2),
                'transaction_type' => ucfirst($item->transaction_type),
                'payment_type' => ucfirst($item->payment_type ?? 'N/A'),
                'created_by' => $item->creator->name ?? 'System'
            ];
        });
        
        $result = $printService->generateHTML($exportData, 'Cash Book Export', $columns);
        
        return response()->download($result['file'], $result['filename'], [
            'Content-Type' => $result['mime']
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export Cash Book to Word
     */
    public function exportWord(Request $request)
    {
        $this->authorize('view-cash-book');
        
        $query = CashBook::with(['account', 'vehicle', 'creator']);
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('cash_book_number', 'like', "%{$search}%")
                  ->orWhere('transaction_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }
        
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        $cashBooks = $query->orderBy('entry_date', 'desc')->get();
        
        $printService = new PrintExportService();
        $columns = [
            'Entry Date' => 'entry_date',
            'Cash Book No' => 'cash_book_number',
            'TRX Number' => 'transaction_number',
            'Account' => 'account.account_name',
            'Vehicle' => 'vehicle.vrn',
            'Description' => 'description',
            'Amount' => 'amount',
            'Type' => 'transaction_type',
            'Payment Type' => 'payment_type',
            'Created By' => 'creator.name'
        ];
        
        $exportData = $cashBooks->map(function($item) {
            return [
                'entry_date' => $item->entry_date->format('Y-m-d'),
                'cash_book_number' => $item->cash_book_number,
                'transaction_number' => $item->transaction_number,
                'account_name' => $item->account->account_name ?? 'N/A',
                'vehicle_vrn' => $item->vehicle->vrn ?? 'N/A',
                'description' => $item->description,
                'amount' => '₨' . number_format($item->amount, 2),
                'transaction_type' => ucfirst($item->transaction_type),
                'payment_type' => ucfirst($item->payment_type ?? 'N/A'),
                'created_by' => $item->creator->name ?? 'System'
            ];
        });
        
        try {
            $result = $printService->generateWord($exportData, 'Cash Book Export', $columns);
            
            return response()->download($result['file'], $result['filename'], [
                'Content-Type' => $result['mime']
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to export to Word: ' . $e->getMessage()]);
        }
    }
}
