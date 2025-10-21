<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Loading Points
        $loadingPoints = [
            'Karachi Port',
            'Port Qasim',
            'Lahore Dry Port',
            'Islamabad Dry Port',
            'Peshawar Dry Port',
            'Quetta Dry Port',
            'Faisalabad Industrial Area',
            'Sialkot Industrial Area',
            'Gujranwala Industrial Area',
            'Multan Industrial Area'
        ];

        foreach ($loadingPoints as $point) {
            MasterData::create([
                'type' => 'loading_point',
                'name' => $point,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Destinations
        $destinations = [
            'Karachi',
            'Lahore',
            'Islamabad',
            'Rawalpindi',
            'Faisalabad',
            'Multan',
            'Peshawar',
            'Quetta',
            'Sialkot',
            'Gujranwala',
            'Hyderabad',
            'Sukkur',
            'Bahawalpur',
            'Sargodha',
            'Jhang',
            'Sheikhupura',
            'Rahim Yar Khan',
            'Gujrat',
            'Kasur',
            'Mardan'
        ];

        foreach ($destinations as $destination) {
            MasterData::create([
                'type' => 'destination',
                'name' => $destination,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Products
        $products = [
            'Wheat',
            'Rice',
            'Sugar',
            'Cotton',
            'Textiles',
            'Cement',
            'Steel',
            'Fertilizer',
            'Chemicals',
            'Machinery',
            'Electronics',
            'Food Items',
            'Pharmaceuticals',
            'Construction Materials',
            'Agricultural Products',
            'Industrial Goods',
            'Consumer Goods',
            'Raw Materials',
            'Finished Products',
            'General Cargo'
        ];

        foreach ($products as $product) {
            MasterData::create([
                'type' => 'product',
                'name' => $product,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Companies
        $companies = [
            'Pakistan State Oil (PSO)',
            'Shell Pakistan',
            'Total Parco',
            'Attock Petroleum',
            'Engro Corporation',
            'Fauji Fertilizer',
            'Lucky Cement',
            'DG Khan Cement',
            'Maple Leaf Cement',
            'Bestway Cement',
            'Nishat Mills',
            'Gul Ahmed Textiles',
            'Kohinoor Textiles',
            'Sapphire Textiles',
            'Fazal Textiles',
            'Nestle Pakistan',
            'Unilever Pakistan',
            'Procter & Gamble',
            'Coca Cola Pakistan',
            'PepsiCo Pakistan'
        ];

        foreach ($companies as $company) {
            MasterData::create([
                'type' => 'company',
                'name' => $company,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Carriages
        $carriages = [
            'Al-Haider Transport',
            'Bismillah Carriage',
            'Fazal Transport',
            'Karachi Carriage',
            'Lahore Transport',
            'Punjab Carriage',
            'Sindh Transport',
            'Balochistan Carriage',
            'KPK Transport',
            'United Carriage',
            'National Transport',
            'City Carriage',
            'Metro Transport',
            'Express Carriage',
            'Speed Transport',
            'Reliable Carriage',
            'Trust Transport',
            'Safe Carriage',
            'Secure Transport',
            'Premium Carriage'
        ];

        foreach ($carriages as $carriage) {
            MasterData::create([
                'type' => 'carriage',
                'name' => $carriage,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Vehicle Types
        $vehicleTypes = [
            'Truck',
            'Trailer',
            'Container',
            'Tanker',
            'Flatbed',
            'Refrigerated',
            'Cargo Van',
            'Pickup Truck',
            'Bus',
            'Mini Truck'
        ];

        foreach ($vehicleTypes as $vehicleType) {
            MasterData::create([
                'type' => 'vehicle_type',
                'name' => $vehicleType,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        // Payment Types
        $paymentTypes = [
            'Cash',
            'Bank Transfer',
            'Cheque',
            'Credit Card',
            'Online Payment',
            'Advance Payment',
            'Postpaid',
            'COD (Cash on Delivery)'
        ];

        foreach ($paymentTypes as $paymentType) {
            MasterData::create([
                'type' => 'payment_type',
                'name' => $paymentType,
                'is_active' => true,
                'created_by' => 1
            ]);
        }

        $this->command->info('Master data seeded successfully!');
    }
}
