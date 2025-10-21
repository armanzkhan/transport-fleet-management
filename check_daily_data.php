<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check accounts
    $accounts = App\Models\ChartOfAccount::where('is_active', true)
        ->orderBy('account_name')
        ->get();
    
    echo "Active Accounts: " . $accounts->count() . "\n";
    foreach ($accounts->take(5) as $account) {
        echo "- {$account->account_name} (ID: {$account->id})\n";
    }
    
    // Check vehicles
    $vehicles = App\Models\Vehicle::where('is_active', true)
        ->with('owner')
        ->orderBy('vrn')
        ->get();
    
    echo "\nActive Vehicles: " . $vehicles->count() . "\n";
    foreach ($vehicles->take(5) as $vehicle) {
        $owner = $vehicle->owner ? $vehicle->owner->name : 'No Owner';
        echo "- {$vehicle->vrn} - {$owner} (ID: {$vehicle->id})\n";
    }
    
    // Check previous balance calculation
    $todaysPreviousBalance = App\Models\CashBook::getPreviousDayBalance(date('Y-m-d'));
    echo "\nToday's Previous Balance: PKR " . number_format($todaysPreviousBalance, 2) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}