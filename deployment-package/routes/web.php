<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleOwnerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\JourneyVoucherController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\JourneySummaryController;
use App\Http\Controllers\VehicleBillingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SecondaryJourneyVoucherController;
use App\Http\Controllers\SmartSuggestionsController;
use App\Http\Controllers\ShortcutDictionaryController;
use App\Http\Controllers\DeveloperAccessController;

// Authentication routes
Auth::routes();

// Language routes
Route::prefix('language')->name('language.')->group(function () {
    Route::post('/switch', [LanguageController::class, 'switch'])->name('switch');
    Route::get('/current', [LanguageController::class, 'current'])->name('current');
    Route::get('/translations', [LanguageController::class, 'translations'])->name('translations');
});

// Notification routes
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::get('/dashboard-alerts', [NotificationController::class, 'getDashboardAlerts'])->name('dashboard-alerts');
    Route::get('/count', [NotificationController::class, 'getCount'])->name('count');
    Route::post('/check-expiry', [NotificationController::class, 'checkExpiryNotifications'])->name('check-expiry');
});

// Smart Suggestions routes
Route::prefix('smart-suggestions')->name('smart-suggestions.')->group(function () {
    Route::get('/get', [SmartSuggestionsController::class, 'getSuggestions'])->name('get');
    Route::get('/route', [SmartSuggestionsController::class, 'getRouteSuggestions'])->name('route');
    Route::post('/clear-cache', [SmartSuggestionsController::class, 'clearCache'])->name('clear-cache');
});

// Shortcut Dictionary routes
Route::prefix('shortcut-dictionary')->name('shortcut-dictionary.')->group(function () {
    Route::get('/', [ShortcutDictionaryController::class, 'index'])->name('index');
    Route::get('/create', [ShortcutDictionaryController::class, 'create'])->name('create');
    Route::post('/', [ShortcutDictionaryController::class, 'store'])->name('store');
    Route::get('/{shortcutDictionary}/edit', [ShortcutDictionaryController::class, 'edit'])->name('edit');
    Route::put('/{shortcutDictionary}', [ShortcutDictionaryController::class, 'update'])->name('update');
    Route::delete('/{shortcutDictionary}', [ShortcutDictionaryController::class, 'destroy'])->name('destroy');
    Route::get('/api/shortcuts', [ShortcutDictionaryController::class, 'getShortcuts'])->name('get-shortcuts');
    Route::get('/api/category/{category}', [ShortcutDictionaryController::class, 'getShortcutsByCategory'])->name('get-by-category');
});

// Developer Access Management routes
Route::prefix('developer-access')->name('developer-access.')->group(function () {
    Route::get('/', [DeveloperAccessController::class, 'index'])->name('index');
    Route::get('/create', [DeveloperAccessController::class, 'create'])->name('create');
    Route::post('/', [DeveloperAccessController::class, 'store'])->name('store');
    Route::post('/{developerAccess}/approve', [DeveloperAccessController::class, 'approve'])->name('approve');
    Route::post('/{developerAccess}/activate', [DeveloperAccessController::class, 'activate'])->name('activate');
    Route::post('/{developerAccess}/revoke', [DeveloperAccessController::class, 'revoke'])->name('revoke');
    Route::delete('/{developerAccess}', [DeveloperAccessController::class, 'destroy'])->name('destroy');
    Route::get('/api/active', [DeveloperAccessController::class, 'getActiveAccesses'])->name('active');
    Route::get('/api/statistics', [DeveloperAccessController::class, 'getStatistics'])->name('statistics');
    Route::post('/api/auto-expire', [DeveloperAccessController::class, 'autoExpireAccesses'])->name('auto-expire');
});

        // Debug route
        Route::get('/debug-test', function () {
            return view('debug-test');
        });

        // Translation test route
        Route::get('/test-translation', function () {
            return view('test-translation');
        });
        
        // Test carriage data route
        Route::get('/test-carriage-data', function () {
            $carriages = \App\Models\MasterData::where('type', 'carriage')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'count' => $carriages->count(),
                'carriages' => $carriages
            ]);
        });

// Protected routes that require authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/export', [DashboardController::class, 'exportReport'])->name('dashboard.export');
    
    // Role-specific dashboards
    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/fleet-dashboard', [DashboardController::class, 'fleetDashboard'])->name('fleet.dashboard');
    Route::get('/finance-dashboard', [DashboardController::class, 'financeDashboard'])->name('finance.dashboard');
    
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('/api/notifications', [DashboardController::class, 'notificationsApi'])->name('notifications.api');
    Route::post('/api/notifications/settings', [DashboardController::class, 'updateNotificationSettings'])->name('notifications.settings');

    // User Management (Admin only)
    Route::middleware('can:create-users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Vehicle Owners
    Route::resource('vehicle-owners', VehicleOwnerController::class);
    Route::get('/vehicle-owners-search', [VehicleOwnerController::class, 'search'])->name('vehicle-owners.search');

// Vehicles
Route::resource('vehicles', VehicleController::class);
Route::get('/vehicles-search', [VehicleController::class, 'search'])->name('vehicles.search');
Route::get('/expiring-documents', [VehicleController::class, 'expiringDocuments'])->name('vehicles.expiring-documents');

// Cash Book - Specific routes must come before resource routes
Route::get('/cash-books/receive', [CashBookController::class, 'receive'])->name('cash-books.receive');
Route::get('/cash-books/payment', [CashBookController::class, 'payment'])->name('cash-books.payment');
Route::get('/cash-books/daily', [CashBookController::class, 'daily'])->name('cash-books.daily');
Route::get('/cash-books/simple', [CashBookController::class, 'simple'])->name('cash-books.simple');
Route::get('/cash-books/previous-balance', [CashBookController::class, 'getPreviousDayBalance'])->name('cash-books.previous-balance');
Route::post('/cash-books/receive', [CashBookController::class, 'storeReceive'])->name('cash-books.store-receive');
Route::post('/cash-books/payment', [CashBookController::class, 'storePayment'])->name('cash-books.store-payment');
Route::post('/cash-books/daily', [CashBookController::class, 'storeDaily'])->name('cash-books.store-daily');
Route::post('/cash-books/simple', [CashBookController::class, 'storeSimple'])->name('cash-books.store-simple');
    Route::get('/cash-books/{cashBook}/print', [CashBookController::class, 'print'])->name('cash-books.print');
    Route::get('/cash-books/export/csv', [CashBookController::class, 'exportCSV'])->name('cash-books.export-csv');
    Route::get('/cash-books/export/html', [CashBookController::class, 'exportHTML'])->name('cash-books.export-html');
    Route::resource('cash-books', CashBookController::class);

    // Journey Vouchers - Specific routes first to avoid conflicts with resource routes
    Route::get('/journey-vouchers/primary', [JourneyVoucherController::class, 'primary'])->name('journey-vouchers.primary');
    Route::get('/journey-vouchers/secondary', [JourneyVoucherController::class, 'secondary'])->name('journey-vouchers.secondary');
    Route::post('/journey-vouchers/primary', [JourneyVoucherController::class, 'storePrimary'])->name('journey-vouchers.store-primary');
    Route::post('/journey-vouchers/secondary', [JourneyVoucherController::class, 'storeSecondary'])->name('journey-vouchers.store-secondary');
    
            // Journey Vouchers - Resource routes
            Route::resource('journey-vouchers', JourneyVoucherController::class);
            
            // Secondary Journey Vouchers
            Route::resource('secondary-journey-vouchers', SecondaryJourneyVoucherController::class);
            Route::get('/secondary-journey-vouchers/auto-rate/data', [SecondaryJourneyVoucherController::class, 'getAutoRateData'])->name('secondary-journey-vouchers.auto-rate');

    // Tariffs
    Route::resource('tariffs', TariffController::class);
    Route::get('/tariffs/{tariff}/applicable', [TariffController::class, 'getApplicableTariff'])->name('tariffs.applicable');

    // Journey Summary
    Route::resource('journey-summary', JourneySummaryController::class)->only(['index']);
    Route::post('/journey-summary/process', [JourneySummaryController::class, 'process'])->name('journey-summary.process');
    Route::post('/journey-summary/unprocess', [JourneySummaryController::class, 'unprocess'])->name('journey-summary.unprocess');
    Route::get('/journey-summary/data', [JourneySummaryController::class, 'getSummaryData'])->name('journey-summary.data');

    // Vehicle Billing
    Route::resource('vehicle-billing', VehicleBillingController::class);
    Route::post('/vehicle-billing/{vehicleBill}/finalize', [VehicleBillingController::class, 'finalize'])->name('vehicle-billing.finalize');
    Route::get('/vehicle-billing/{vehicleBill}/print', [VehicleBillingController::class, 'print'])->name('vehicle-billing.print');
    Route::get('/vehicle-billing/data', [VehicleBillingController::class, 'getBillingData'])->name('vehicle-billing.data');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('general-ledger');
        Route::get('/company-summary', [ReportController::class, 'companySummary'])->name('company-summary');
        Route::get('/carriage-summary', [ReportController::class, 'carriageSummary'])->name('carriage-summary');
        Route::get('/monthly-vehicle-bills', [ReportController::class, 'monthlyVehicleBills'])->name('monthly-vehicle-bills');
        Route::get('/debit-account-notifications', [ReportController::class, 'debitAccountNotifications'])->name('debit-account-notifications');
        Route::get('/company-trial-balance', [ReportController::class, 'companyTrialBalance'])->name('company-trial-balance');
        Route::get('/unattached-shortages', [ReportController::class, 'unattachedShortages'])->name('unattached-shortages');
        Route::get('/income-reports', [ReportController::class, 'incomeReports'])->name('income-reports');
        Route::get('/pending-trips', [ReportController::class, 'pendingTrips'])->name('pending-trips');
        Route::get('/outstanding-advances', [ReportController::class, 'outstandingAdvances'])->name('outstanding-advances');
        Route::get('/vehicle-database', [ReportController::class, 'vehicleDatabase'])->name('vehicle-database');
        Route::get('/vehicle-owner-ledger', [ReportController::class, 'vehicleOwnerLedger'])->name('vehicle-owner-ledger');
    });
});
