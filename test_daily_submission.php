<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Simulate daily form submission
    $data = [
        'entry_date' => date('Y-m-d'),
        'receives' => [
            0 => [
                'account_id' => 1, // Assets
                'vehicle_id' => 1, // TEST-001
                'description' => 'Test receive entry',
                'amount' => 200.00
            ]
        ],
        'payments' => [
            0 => [
                'payment_type' => 'Expense',
                'account_id' => 1, // Assets
                'vehicle_id' => 1, // TEST-001
                'description' => 'Test payment entry',
                'amount' => 50.00
            ]
        ]
    ];
    
    echo "Test data prepared:\n";
    echo "Entry Date: " . $data['entry_date'] . "\n";
    echo "Receives: " . count($data['receives']) . "\n";
    echo "Payments: " . count($data['payments']) . "\n";
    
    // Test validation rules
    $validator = Illuminate\Support\Facades\Validator::make($data, [
        'entry_date' => 'required|date',
        'receives' => 'sometimes|array',
        'receives.*.account_id' => 'required|exists:chart_of_accounts,id',
        'receives.*.vehicle_id' => 'nullable|exists:vehicles,id',
        'receives.*.description' => 'required|string|max:500',
        'receives.*.amount' => 'required|numeric|min:0.01',
        'payments' => 'sometimes|array',
        'payments.*.payment_type' => 'required|in:Advance,Expense,Shortage',
        'payments.*.account_id' => 'required|exists:chart_of_accounts,id',
        'payments.*.vehicle_id' => 'nullable|exists:vehicles,id',
        'payments.*.description' => 'required|string|max:500',
        'payments.*.amount' => 'required|numeric|min:0.01',
    ]);
    
    if ($validator->fails()) {
        echo "Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- " . $error . "\n";
        }
    } else {
        echo "Validation passed!\n";
        
        // Try to create the entries
        $previousBalance = App\Models\CashBook::getPreviousDayBalance($data['entry_date']);
        echo "Previous balance: PKR " . number_format($previousBalance, 2) . "\n";
        
        $entryNumber = 1;
        $totalReceives = 0;
        $totalPayments = 0;
        
        // Process receives
        if (isset($data['receives']) && count($data['receives']) > 0) {
            foreach ($data['receives'] as $receive) {
                $totalReceives += $receive['amount'];
                
                $entry = App\Models\CashBook::create([
                    'entry_date' => $data['entry_date'],
                    'transaction_type' => 'receive',
                    'account_id' => $receive['account_id'],
                    'vehicle_id' => $receive['vehicle_id'],
                    'description' => $receive['description'],
                    'amount' => $receive['amount'],
                    'previous_day_balance' => $previousBalance,
                    'total_cash_in_hand' => $previousBalance + $totalReceives,
                    'created_by' => 1,
                ]);
                
                echo "Created receive entry ID: " . $entry->id . "\n";
            }
        }
        
        // Process payments
        if (isset($data['payments']) && count($data['payments']) > 0) {
            foreach ($data['payments'] as $payment) {
                $totalPayments += $payment['amount'];
                
                $entry = App\Models\CashBook::create([
                    'entry_date' => $data['entry_date'],
                    'transaction_type' => 'payment',
                    'account_id' => $payment['account_id'],
                    'vehicle_id' => $payment['vehicle_id'],
                    'payment_type' => $payment['payment_type'],
                    'description' => $payment['description'],
                    'amount' => $payment['amount'],
                    'previous_day_balance' => $previousBalance,
                    'total_cash_in_hand' => $previousBalance + $totalReceives - $totalPayments,
                    'created_by' => 1,
                ]);
                
                echo "Created payment entry ID: " . $entry->id . "\n";
            }
        }
        
        $finalBalance = $previousBalance + $totalReceives - $totalPayments;
        echo "Final balance: PKR " . number_format($finalBalance, 2) . "\n";
        echo "SUCCESS: Daily entries created successfully!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}