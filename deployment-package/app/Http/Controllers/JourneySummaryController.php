<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JourneyVoucher;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JourneySummaryController extends Controller
{
    public function index()
    {
        $this->authorize('view-journey-summary');
        
        // Get journey vouchers that have invoice numbers (ready for summary)
        $journeyVouchers = JourneyVoucher::with(['vehicle.owner', 'creator'])
            ->whereNotNull('invoice_number')
            ->where('is_processed', false)
            ->orderBy('journey_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('journey-summary.index', compact('journeyVouchers'));
    }

    public function process(Request $request)
    {
        $this->authorize('process-journey-summary');
        
        $validator = Validator::make($request->all(), [
            'journey_ids' => 'required|array|min:1',
            'journey_ids.*' => 'exists:journey_vouchers,id',
            'action' => 'required|in:add_to_billing,add_to_pr04',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $journeyVouchers = JourneyVoucher::whereIn('id', $request->journey_ids)->get();
        $processedCount = 0;

        foreach ($journeyVouchers as $voucher) {
            if ($request->action === 'add_to_billing') {
                $voucher->update(['is_processed' => true, 'is_billed' => true]);
            } else {
                $voucher->update(['is_processed' => true, 'is_billed' => false]);
            }

            // Log the action
            AuditLog::log('process', $voucher, null, [
                'action' => $request->action,
                'processed_at' => now(),
            ]);

            $processedCount++;
        }

        $actionText = $request->action === 'add_to_billing' ? 'added to billing' : 'added to PR04';
        
        return redirect()->route('journey-summary.index')
            ->with('success', "{$processedCount} journey vouchers {$actionText} successfully.");
    }

    public function unprocess(Request $request)
    {
        $this->authorize('process-journey-summary');
        
        $validator = Validator::make($request->all(), [
            'journey_ids' => 'required|array|min:1',
            'journey_ids.*' => 'exists:journey_vouchers,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $journeyVouchers = JourneyVoucher::whereIn('id', $request->journey_ids)->get();
        $unprocessedCount = 0;

        foreach ($journeyVouchers as $voucher) {
            $voucher->update(['is_processed' => false, 'is_billed' => false]);

            // Log the action
            AuditLog::log('unprocess', $voucher, null, [
                'unprocessed_at' => now(),
            ]);

            $unprocessedCount++;
        }

        return redirect()->route('journey-summary.index')
            ->with('success', "{$unprocessedCount} journey vouchers unprocessed successfully.");
    }

    public function getSummaryData(Request $request)
    {
        $journeyIds = $request->get('journey_ids', []);
        
        if (empty($journeyIds)) {
            return response()->json(['summary' => null]);
        }

        $journeys = JourneyVoucher::whereIn('id', $journeyIds)->get();
        
        $summary = [
            'total_freight' => $journeys->sum('freight_amount'),
            'total_shortage' => $journeys->sum('shortage_amount'),
            'total_commission' => $journeys->sum('commission_amount'),
            'total_amount' => $journeys->sum('total_amount'),
            'journey_count' => $journeys->count(),
        ];

        return response()->json(['summary' => $summary]);
    }
}
