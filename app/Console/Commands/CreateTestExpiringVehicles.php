<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\VehicleOwner;

class CreateTestExpiringVehicles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-expiring-vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test vehicles with expiring documents to demonstrate notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test vehicles with expiring documents...');

        // Get or create a test owner
        $owner = VehicleOwner::where('cnic', '12345-1234567-1')->first();
        
        if (!$owner) {
            // Get the next serial number manually
            $lastOwner = VehicleOwner::orderBy('id', 'desc')->first();
            $nextId = $lastOwner ? $lastOwner->id + 1 : 1;
            $serialNumber = 'OWN-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            
            $owner = VehicleOwner::create([
                'serial_number' => $serialNumber,
                'name' => 'Test Owner',
                'cnic' => '12345-1234567-1',
                'contact_number' => '03001234567',
                'address' => 'Test Address, Test City',
                'is_active' => true
            ]);
        }

        $testVehicles = [
            [
                'vrn' => 'TEST-001',
                'driver_name' => 'Ahmed Ali',
                'driver_contact' => '03009876543',
                'capacity' => 5000,
                'tracker_expiry' => now()->addDays(2), // Critical - 2 days
                'dip_chart_expiry' => now()->addDays(12), // Info - 12 days
                'token_tax_expiry' => now()->addDays(25), // Not expiring
            ],
            [
                'vrn' => 'TEST-002', 
                'driver_name' => 'Muhammad Hassan',
                'driver_contact' => '03007654321',
                'capacity' => 7000,
                'tracker_expiry' => now()->addDays(6), // Warning - 6 days
                'dip_chart_expiry' => now()->addDays(1), // Critical - 1 day
                'token_tax_expiry' => now()->addDays(10), // Info - 10 days
            ],
            [
                'vrn' => 'TEST-003',
                'driver_name' => 'Ali Khan',
                'driver_contact' => '03005432167',
                'capacity' => 4500,
                'tracker_expiry' => now()->addDays(14), // Info - 14 days
                'dip_chart_expiry' => now()->subDays(2), // Expired
                'token_tax_expiry' => now()->addDays(30), // Not expiring
            ]
        ];

        foreach ($testVehicles as $index => $vehicleData) {
            $vehicle = Vehicle::where('vrn', $vehicleData['vrn'])->first();
            
            if (!$vehicle) {
                // Generate serial number manually
                $lastVehicle = Vehicle::orderBy('id', 'desc')->first();
                $nextId = $lastVehicle ? $lastVehicle->id + 1 : 1;
                $serialNumber = 'VH-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
                
                $vehicle = Vehicle::create(array_merge($vehicleData, [
                    'serial_number' => $serialNumber,
                    'owner_id' => $owner->id,
                    'is_active' => true
                ]));
            } else {
                $vehicle->update(array_merge($vehicleData, [
                    'owner_id' => $owner->id,
                    'is_active' => true
                ]));
            }

            $this->info("Created/Updated vehicle: {$vehicle->vrn}");
            
            // Show expiring documents for this vehicle
            $expiringDocs = $vehicle->getExpiringDocuments();
            if (!empty($expiringDocs)) {
                foreach ($expiringDocs as $doc) {
                    $status = $doc['days_remaining'] <= 0 ? 'EXPIRED' : 
                             ($doc['days_remaining'] <= 3 ? 'CRITICAL' : 
                             ($doc['days_remaining'] <= 7 ? 'WARNING' : 'INFO'));
                    
                    $this->warn("  - {$doc['type']}: {$doc['days_remaining']} days ({$status})");
                }
            } else {
                $this->line("  - No expiring documents");
            }
        }

        $this->info("\nTest vehicles created successfully!");
        $this->info("You can now test the notification system:");
        $this->info("1. Run: php artisan notifications:send-expiry");
        $this->info("2. Visit the dashboard to see notifications");
        $this->info("3. Visit /notifications to see detailed alerts");

        return 0;
    }
}
