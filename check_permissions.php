<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the first user (assuming this is the test user)
    $user = App\Models\User::first();
    
    if (!$user) {
        echo "Error: No users found\n";
        exit(1);
    }
    
    echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
    echo "Email: " . $user->email . "\n";
    
    // Check permissions
    $hasCreatePermission = $user->can('create-cash-book');
    $hasViewPermission = $user->can('view-cash-book');
    
    echo "Has create-cash-book permission: " . ($hasCreatePermission ? 'YES' : 'NO') . "\n";
    echo "Has view-cash-book permission: " . ($hasViewPermission ? 'YES' : 'NO') . "\n";
    
    // Get all permissions
    $permissions = $user->getAllPermissions();
    echo "All permissions: " . $permissions->pluck('name')->implode(', ') . "\n";
    
    // Get all roles
    $roles = $user->getRoleNames();
    echo "All roles: " . $roles->implode(', ') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}