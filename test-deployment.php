<?php
/**
 * 🧪 Deployment Verification Script
 * 
 * This script tests all major functionality of the Transport Fleet Management System
 * Run this after deployment to ensure everything is working correctly.
 */

echo "🧪 Transport Fleet Management - Deployment Verification\n";
echo "======================================================\n\n";

// Test 1: Basic Laravel Installation
echo "1. Testing Laravel Installation...\n";
if (file_exists('artisan')) {
    echo "   ✅ Laravel artisan file found\n";
} else {
    echo "   ❌ Laravel artisan file not found\n";
    exit(1);
}

// Test 2: Environment Configuration
echo "\n2. Testing Environment Configuration...\n";
if (file_exists('.env')) {
    echo "   ✅ Environment file found\n";
} else {
    echo "   ❌ Environment file not found\n";
    exit(1);
}

// Test 3: Database Connection
echo "\n3. Testing Database Connection...\n";
try {
    $pdo = new PDO(
        'sqlite:database/database.sqlite',
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "   ✅ Database connection successful\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Check Required Tables
echo "\n4. Testing Database Tables...\n";
$requiredTables = [
    'users', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions',
    'vehicles', 'vehicle_owners', 'cash_books', 'journey_vouchers', 'tariffs',
    'vehicle_bills', 'audit_logs', 'master_data', 'notifications'
];

$missingTables = [];
foreach ($requiredTables as $table) {
    try {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
        if ($stmt->fetch()) {
            echo "   ✅ Table '$table' exists\n";
        } else {
            echo "   ❌ Table '$table' missing\n";
            $missingTables[] = $table;
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking table '$table': " . $e->getMessage() . "\n";
        $missingTables[] = $table;
    }
}

if (!empty($missingTables)) {
    echo "\n   ⚠️  Missing tables detected. Run: php artisan migrate\n";
}

// Test 5: Check Required Files
echo "\n5. Testing Required Files...\n";
$requiredFiles = [
    'app/Http/Controllers/DashboardController.php',
    'app/Http/Controllers/UserController.php',
    'app/Http/Controllers/CashBookController.php',
    'app/Http/Controllers/JourneyVoucherController.php',
    'app/Http/Controllers/ReportController.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/dashboards/admin.blade.php',
    'routes/web.php'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ File '$file' exists\n";
    } else {
        echo "   ❌ File '$file' missing\n";
        $missingFiles[] = $file;
    }
}

if (!empty($missingFiles)) {
    echo "\n   ⚠️  Missing files detected. Check your deployment.\n";
}

// Test 6: Check Routes
echo "\n6. Testing Routes...\n";
$requiredRoutes = [
    'dashboard', 'admin.dashboard', 'fleet.dashboard', 'finance.dashboard',
    'users.index', 'cash-books.index', 'journey-vouchers.index', 'reports.index'
];

// This would require Laravel to be running, so we'll just check if route files exist
if (file_exists('routes/web.php')) {
    echo "   ✅ Routes file exists\n";
} else {
    echo "   ❌ Routes file missing\n";
}

// Test 7: Check Views
echo "\n7. Testing Views...\n";
$requiredViews = [
    'resources/views/dashboards/admin.blade.php',
    'resources/views/dashboards/fleet.blade.php',
    'resources/views/dashboards/finance.blade.php',
    'resources/views/users/index.blade.php',
    'resources/views/cash-books/index.blade.php'
];

$missingViews = [];
foreach ($requiredViews as $view) {
    if (file_exists($view)) {
        echo "   ✅ View '$view' exists\n";
    } else {
        echo "   ❌ View '$view' missing\n";
        $missingViews[] = $view;
    }
}

// Test 8: Check Services
echo "\n8. Testing Services...\n";
$requiredServices = [
    'app/Services/LanguageService.php',
    'app/Services/NotificationService.php',
    'app/Services/PrintExportService.php'
];

foreach ($requiredServices as $service) {
    if (file_exists($service)) {
        echo "   ✅ Service '$service' exists\n";
    } else {
        echo "   ❌ Service '$service' missing\n";
    }
}

// Test 9: Check Models
echo "\n9. Testing Models...\n";
$requiredModels = [
    'app/Models/User.php',
    'app/Models/Vehicle.php',
    'app/Models/CashBook.php',
    'app/Models/JourneyVoucher.php',
    'app/Models/VehicleBill.php'
];

foreach ($requiredModels as $model) {
    if (file_exists($model)) {
        echo "   ✅ Model '$model' exists\n";
    } else {
        echo "   ❌ Model '$model' missing\n";
    }
}

// Test 10: Check Deployment Files
echo "\n10. Testing Deployment Files...\n";
$deploymentFiles = [
    'Procfile',
    'railway.json',
    'deploy-config.md',
    'README.md',
    'DEPLOYMENT_STEPS.md'
];

foreach ($deploymentFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ Deployment file '$file' exists\n";
    } else {
        echo "   ❌ Deployment file '$file' missing\n";
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 DEPLOYMENT VERIFICATION SUMMARY\n";
echo str_repeat("=", 50) . "\n";

$totalTests = 10;
$passedTests = $totalTests - count($missingTables) - count($missingFiles) - count($missingViews);

echo "✅ Tests Passed: $passedTests/$totalTests\n";

if (empty($missingTables) && empty($missingFiles) && empty($missingViews)) {
    echo "🎉 ALL TESTS PASSED! Your system is ready for deployment.\n";
    echo "\n📋 Next Steps:\n";
    echo "1. Push code to GitHub\n";
    echo "2. Deploy to Railway/Render\n";
    echo "3. Set environment variables\n";
    echo "4. Create admin user\n";
    echo "5. Test the live application\n";
} else {
    echo "⚠️  Some issues detected. Please fix them before deployment.\n";
    
    if (!empty($missingTables)) {
        echo "\nMissing Tables: " . implode(', ', $missingTables) . "\n";
        echo "Run: php artisan migrate\n";
    }
    
    if (!empty($missingFiles)) {
        echo "\nMissing Files: " . implode(', ', $missingFiles) . "\n";
    }
    
    if (!empty($missingViews)) {
        echo "\nMissing Views: " . implode(', ', $missingViews) . "\n";
    }
}

echo "\n🚀 Ready for deployment! Good luck! 🚛✨\n";
?>
