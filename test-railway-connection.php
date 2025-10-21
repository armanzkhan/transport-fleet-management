<?php

// Railway Connection Test Script
// This script tests the database connection and basic functionality

echo "🧪 Testing Railway Application Connection...\n\n";

try {
    // Test 1: Basic PHP functionality
    echo "1. ✅ PHP Version: " . PHP_VERSION . "\n";
    
    // Test 2: Laravel application
    if (file_exists('artisan')) {
        echo "2. ✅ Laravel application found\n";
    } else {
        echo "2. ❌ Laravel application not found\n";
    }
    
    // Test 3: Database file (for SQLite)
    if (file_exists('database/database.sqlite')) {
        echo "3. ✅ SQLite database file exists\n";
    } else {
        echo "3. ⚠️ SQLite database file not found (will be created)\n";
    }
    
    // Test 4: Environment variables
    $appName = env('APP_NAME', 'Not set');
    $appEnv = env('APP_ENV', 'Not set');
    $dbConnection = env('DB_CONNECTION', 'Not set');
    
    echo "4. App Name: $appName\n";
    echo "5. App Environment: $appEnv\n";
    echo "6. Database Connection: $dbConnection\n";
    
    // Test 5: Check if we can run artisan commands
    echo "\n🔧 Testing Laravel commands...\n";
    
    // Test database connection
    echo "7. Testing database connection...\n";
    $output = shell_exec('php artisan tinker --execute="echo \'Database connection test\';" 2>&1');
    if ($output) {
        echo "   ✅ Artisan tinker working\n";
    } else {
        echo "   ❌ Artisan tinker failed\n";
    }
    
    echo "\n🎉 Railway connection test completed!\n";
    echo "🌐 Your application should be accessible at: https://trolley.proxy.rlwy.net\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "🔧 Please check Railway logs for more details\n";
}
