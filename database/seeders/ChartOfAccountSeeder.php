<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main account categories
        $assets = ChartOfAccount::firstOrCreate([
            'account_code' => '1000',
        ], [
            'account_name' => 'Assets',
            'account_type' => 'Assets',
            'is_active' => true,
        ]);

        $expenses = ChartOfAccount::firstOrCreate([
            'account_code' => '2000',
        ], [
            'account_name' => 'Expenses',
            'account_type' => 'Expenses',
            'is_active' => true,
        ]);

        $liabilities = ChartOfAccount::firstOrCreate([
            'account_code' => '3000',
        ], [
            'account_name' => 'Liabilities',
            'account_type' => 'Liabilities',
            'is_active' => true,
        ]);

        $revenue = ChartOfAccount::firstOrCreate([
            'account_code' => '4000',
        ], [
            'account_name' => 'Revenue',
            'account_type' => 'Revenue',
            'is_active' => true,
        ]);

        // Create sub-accounts under Assets
        $currentAssets = ChartOfAccount::firstOrCreate([
            'account_code' => '1100',
        ], [
            'account_name' => 'Current Assets',
            'account_type' => 'Assets',
            'parent_id' => $assets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '1110',
        ], [
            'account_name' => 'Cash in Hand',
            'account_type' => 'Assets',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '1120',
        ], [
            'account_name' => 'Bank Account',
            'account_type' => 'Assets',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '1130',
        ], [
            'account_name' => 'Accounts Receivable',
            'account_type' => 'Assets',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        // Create sub-accounts under Expenses
        $operatingExpenses = ChartOfAccount::firstOrCreate([
            'account_code' => '2100',
        ], [
            'account_name' => 'Operating Expenses',
            'account_type' => 'Expenses',
            'parent_id' => $expenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '2110',
        ], [
            'account_name' => 'Fuel Expenses',
            'account_type' => 'Expenses',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '2120',
        ], [
            'account_name' => 'Maintenance Expenses',
            'account_type' => 'Expenses',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '2130',
        ], [
            'account_name' => 'Driver Salaries',
            'account_type' => 'Expenses',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        // Create sub-accounts under Liabilities
        $currentLiabilities = ChartOfAccount::firstOrCreate([
            'account_code' => '3100',
        ], [
            'account_name' => 'Current Liabilities',
            'account_type' => 'Liabilities',
            'parent_id' => $liabilities->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '3110',
        ], [
            'account_name' => 'Accounts Payable',
            'account_type' => 'Liabilities',
            'parent_id' => $currentLiabilities->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '3120',
        ], [
            'account_name' => 'Advance Payments',
            'account_type' => 'Liabilities',
            'parent_id' => $currentLiabilities->id,
            'is_active' => true,
        ]);

        // Create sub-accounts under Revenue
        $operatingRevenue = ChartOfAccount::firstOrCreate([
            'account_code' => '4100',
        ], [
            'account_name' => 'Operating Revenue',
            'account_type' => 'Revenue',
            'parent_id' => $revenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '4110',
        ], [
            'account_name' => 'Freight Income',
            'account_type' => 'Revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '4120',
        ], [
            'account_name' => 'Commission Income',
            'account_type' => 'Revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '4130',
        ], [
            'account_name' => 'Freight Difference Income',
            'account_type' => 'Revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::firstOrCreate([
            'account_code' => '4140',
        ], [
            'account_name' => 'Shortage Difference Income',
            'account_type' => 'Revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);
    }
}
