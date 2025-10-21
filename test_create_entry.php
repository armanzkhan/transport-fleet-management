<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $account = App\Models\ChartOfAccount::first();
    $vehicle = App\Models\Vehicle::first();
    $previousBalance = App\Models\CashBook::getPreviousDayBalance(date('Y-m-d'));
    
    echo "Account: " . ($account ? $account->account_name : 'Not found') . "\n";
    echo "Vehicle: " . ($vehicle ? $vehicle->vehicle_number : 'Not found') . "\n";
    echo "Previous Balance: " . $previousBalance . "\n";
    
    if (!$account) {
        echo "Error: No accounts found\n";
        exit(1);
    }
    
    if (!$vehicle) {
        echo "Error: No vehicles found\n";
        exit(1);
    }
    
    $entry = App\Models\CashBook::create([
        'entry_date' => date('Y-m-d'),
        'transaction_type' => 'receive',
        'account_id' => $account->id,
        'vehicle_id' => $vehicle->id,
        'payment_type' => 'Advance',
        'description' => 'Test entry from PHP script',
        'amount' => 100.00,
        'previous_day_balance' => $previousBalance,
        'total_cash_in_hand' => $previousBalance + 100.00,
        'created_by' => 1,
    ]);
    
    echo "Success: Created entry with ID " . $entry->id . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}