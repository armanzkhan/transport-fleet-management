<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',
            'assign-roles',
            
            // Vehicle Management
            'create-vehicles',
            'edit-vehicles',
            'delete-vehicles',
            'view-vehicles',
            'create-vehicle-owners',
            'edit-vehicle-owners',
            'delete-vehicle-owners',
            'view-vehicle-owners',
            
            // Cash Book
            'create-cash-book',
            'edit-cash-book',
            'delete-cash-book',
            'view-cash-book',
            'print-cash-vouchers',
            
            // Journey Vouchers
            'create-journey-vouchers',
            'edit-journey-vouchers',
            'delete-journey-vouchers',
            'view-journey-vouchers',
            'process-journey-vouchers',
            
            // Tariffs
            'create-tariffs',
            'edit-tariffs',
            'delete-tariffs',
            'view-tariffs',
            
            // Vehicle Billing
            'create-vehicle-bills',
            'edit-vehicle-bills',
            'delete-vehicle-bills',
            'view-vehicle-bills',
            'finalize-vehicle-bills',
            
            // Reports
            'view-reports',
            'export-reports',
            'print-reports',
            
            // Master Data
            'manage-master-data',
            
            // Audit Logs
            'view-audit-logs',
            'download-backup',
            
            // System Settings
            'manage-system-settings',
            'toggle-language',
            
            // Dashboard Access
            'access-admin-dashboard',
            'access-fleet-dashboard',
            'access-finance-dashboard',
            
            // Backup Management (Super Admin Only)
            'complete-system-backup',
            'daily-backup-management',
            'backup-restoration',
            
            // Advanced Admin Features
            'manage-backups',
            'system-maintenance',
            'view-system-logs',
            
            // Secondary Journey Vouchers
            'create-secondary-journey-vouchers',
            'edit-secondary-journey-vouchers',
            'delete-secondary-journey-vouchers',
            'view-secondary-journey-vouchers',
            
            // Smart Suggestions
            'use-smart-suggestions',
            'manage-smart-suggestions',
            
            // Shortcut Dictionary
            'manage-shortcuts',
            'use-shortcuts',
            
            // Developer Access Management
            'manage-developer-access',
            'approve-developer-access',
            'revoke-developer-access',
            
            // Notifications
            'manage-notifications',
            'view-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant']);
        $fleetManagerRole = Role::firstOrCreate(['name' => 'Fleet Manager']);

        // Assign all permissions to Super Admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign permissions to Admin (complete system access except complete backups)
        $adminPermissions = Permission::whereNotIn('name', [
            'complete-system-backup',
            'daily-backup-management', 
            'backup-restoration'
        ])->get();
        $adminRole->givePermissionTo($adminPermissions);

        // Assign permissions to Accountant
        $accountantRole->givePermissionTo([
            'create-cash-book',
            'edit-cash-book',
            'view-cash-book',
            'print-cash-vouchers',
            'create-journey-vouchers',
            'edit-journey-vouchers',
            'view-journey-vouchers',
            'process-journey-vouchers',
            'create-vehicle-bills',
            'edit-vehicle-bills',
            'view-vehicle-bills',
            'finalize-vehicle-bills',
            'view-reports',
            'export-reports',
            'print-reports',
            'manage-master-data',
        ]);

        // Assign permissions to Fleet Manager
        $fleetManagerRole->givePermissionTo([
            'create-vehicles',
            'edit-vehicles',
            'view-vehicles',
            'create-vehicle-owners',
            'edit-vehicle-owners',
            'view-vehicle-owners',
            'create-journey-vouchers',
            'edit-journey-vouchers',
            'view-journey-vouchers',
            'view-vehicle-bills',
            'view-reports',
            'export-reports',
            'print-reports',
        ]);
    }
}
