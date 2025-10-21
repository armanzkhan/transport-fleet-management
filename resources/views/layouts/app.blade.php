<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app('App\Services\LanguageService')::getDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Transport Fleet Management') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS for dropdown fixes -->
    <style>
        .dropdown-menu {
            display: none !important;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
        }
        
        .dropdown-menu.show {
            display: block !important;
        }
        
        .dropdown-menu[style*="display: block"] {
            display: block !important;
        }
        
        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        
        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
        
        /* Visual feedback for dropdown state */
        .dropdown-toggle:hover {
            background-color: rgba(0,0,0,.1);
        }
        
        .dropdown-menu {
            animation: fadeIn 0.2s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Ensure dropdowns work on mobile */
        @media (max-width: 768px) {
            .dropdown-menu {
                position: static;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: transparent;
                border: 0;
                box-shadow: none;
            }
        }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="fas fa-truck me-2"></i>
                    Transport Fleet
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('fleet.dashboard') || request()->routeIs('finance.dashboard') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    {{ __t('dashboard') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-home me-2"></i>
                                        {{ __t('main_dashboard') }}
                                    </a></li>
                                    @if(Auth::user()->hasAdminAccess())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-crown me-2"></i>
                                        {{ __t('admin_dashboard') }}
                                    </a></li>
                                    @endif
                                    @if(Auth::user()->isFleetManager() || Auth::user()->hasAdminAccess())
                                    <li><a class="dropdown-item" href="{{ route('fleet.dashboard') }}">
                                        <i class="fas fa-truck me-2"></i>
                                        {{ __t('fleet_dashboard') }}
                                    </a></li>
                                    @endif
                                    @if(Auth::user()->isAccountant() || Auth::user()->hasAdminAccess())
                                    <li><a class="dropdown-item" href="{{ route('finance.dashboard') }}">
                                        <i class="fas fa-chart-line me-2"></i>
                                        {{ __t('finance_dashboard') }}
                                    </a></li>
                                    @endif
                                </ul>
                            </li>
                            
                            @can('view-vehicles')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                                    <i class="fas fa-truck me-1"></i>
                                    {{ __t('vehicles') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-vehicle-owners')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('vehicle-owners.*') ? 'active' : '' }}" href="{{ route('vehicle-owners.index') }}">
                                    <i class="fas fa-users me-1"></i>
                                    {{ __t('owners') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-cash-book')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cash-books.*') ? 'active' : '' }}" href="{{ route('cash-books.index') }}">
                                    <i class="fas fa-book me-1"></i>
                                    {{ __t('cash_book') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-journey-vouchers')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('journey-vouchers.*') || request()->routeIs('secondary-journey-vouchers.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-invoice me-1"></i>
                                    {{ __t('journey_vouchers') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('journey-vouchers.index') }}">
                                        <i class="fas fa-truck me-2"></i>
                                        {{ __t('primary_journey_vouchers') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('secondary-journey-vouchers.index') }}">
                                        <i class="fas fa-gas-pump me-2"></i>
                                        {{ __t('secondary_journey_vouchers') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endcan
                            
                            @can('view-tariffs')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tariffs.*') ? 'active' : '' }}" href="{{ route('tariffs.index') }}">
                                    <i class="fas fa-route me-1"></i>
                                    {{ __t('tariffs') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-journey-summary')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('journey-summary.*') ? 'active' : '' }}" href="{{ route('journey-summary.index') }}">
                                    <i class="fas fa-list-check me-1"></i>
                                    {{ __t('journey_summary') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-vehicle-billing')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('vehicle-billing.*') ? 'active' : '' }}" href="{{ route('vehicle-billing.index') }}">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>
                                    {{ __t('vehicle_billing') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('view-reports')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    {{ __t('reports') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('reports.index') }}">{{ __t('all_reports') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.general-ledger') }}">{{ __t('general_ledger') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.company-summary') }}">{{ __t('company_summary') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.carriage-summary') }}">{{ __t('carriage_summary') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.monthly-vehicle-bills') }}">{{ __t('monthly_vehicle_bills') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.income-reports') }}">{{ __t('income_reports') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.pending-trips') }}">{{ __t('pending_trips') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.vehicle-database') }}">{{ __t('vehicle_database') }}</a></li>
                                </ul>
                            </li>
                            @endcan
                            
                            @can('manage-shortcuts')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('shortcut-dictionary.*') ? 'active' : '' }}" href="{{ route('shortcut-dictionary.index') }}">
                                    <i class="fas fa-keyboard me-1"></i>
                                    {{ __t('shortcuts') }}
                                </a>
                            </li>
                            @endcan
                            
                            @can('manage-developer-access')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('developer-access.*') ? 'active' : '' }}" href="{{ route('developer-access.index') }}">
                                    <i class="fas fa-user-shield me-1"></i>
                                    {{ __t('developer_access') }}
                                </a>
                            </li>
                            @endcan
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('data-export-import.*') ? 'active' : '' }}" href="{{ route('data-export-import.index') }}">
                                    <i class="fas fa-database me-1"></i>
                                    Export/Import
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- Language Toggle -->
                            <li class="nav-item me-3">
                                <x-language-toggle />
                            </li>
                            
                            <!-- Notifications -->
                            <li class="nav-item me-3">
                                <a href="{{ route('notifications') }}" id="notification-btn" class="btn btn-outline-secondary position-relative">
                                    <i class="fas fa-bell"></i>
                                    <span id="notification-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                                </a>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>
                                        Profile
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>
                                        Settings
                                    </a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Load notifications
        function loadNotifications() {
            @auth
            fetch('{{ route("notifications.api") }}')
                .then(response => response.json())
                .then(data => {
                    const count = data.length;
                    const countElement = document.getElementById('notification-count');
                    const notificationBtn = document.getElementById('notification-btn');
                    
                    if (count > 0) {
                        countElement.textContent = count;
                        countElement.style.display = 'block';
                        
                        // Change button color based on urgency
                        const hasCritical = data.some(vehicle => 
                            vehicle.expiring_documents.some(doc => doc.days_remaining <= 3)
                        );
                        
                        if (hasCritical) {
                            notificationBtn.className = 'btn btn-danger position-relative';
                        } else {
                            notificationBtn.className = 'btn btn-warning position-relative';
                        }
                    } else {
                        countElement.style.display = 'none';
                        notificationBtn.className = 'btn btn-outline-secondary position-relative';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
            @endauth
        }

        // Load notifications on page load
        loadNotifications();

        // Auto-refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    </script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
    
    <!-- Smart Suggestions Component -->
    <x-smart-suggestions />
    
    <!-- Keyboard Navigation Component -->
    <x-keyboard-navigation />
    
    <!-- Text Expansion Component -->
    <x-text-expansion />
    
    <!-- Bootstrap JS - Load after all other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Simple Dropdown Fix Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing dropdowns...');
            
            // Get all dropdown toggles
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            console.log('Found', dropdownToggles.length, 'dropdown toggles');
            
            // Simple manual dropdown implementation
            dropdownToggles.forEach((toggle, index) => {
                console.log(`Setting up dropdown ${index + 1}:`, toggle.textContent.trim());
                
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('🎯 Dropdown clicked:', this.textContent.trim());
                    console.log('🎯 Event target:', e.target);
                    console.log('🎯 Current element:', this);
                    
                    const dropdown = this.closest('.dropdown');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    
                    console.log('🎯 Dropdown container:', dropdown);
                    console.log('🎯 Menu element:', menu);
                    
                    if (menu) {
                        // Close all other dropdowns first
                        document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                            if (otherMenu !== menu) {
                                otherMenu.style.display = 'none';
                                otherMenu.classList.remove('show');
                            }
                        });
                        
                        // Toggle current dropdown
                        const isVisible = menu.style.display === 'block' || menu.classList.contains('show');
                        
                        console.log('🎯 Current visibility:', isVisible);
                        console.log('🎯 Menu display style:', menu.style.display);
                        console.log('🎯 Menu classes:', menu.classList.toString());
                        
                        if (isVisible) {
                            menu.style.display = 'none';
                            menu.classList.remove('show');
                            this.setAttribute('aria-expanded', 'false');
                            console.log('✅ Dropdown closed');
                        } else {
                            menu.style.display = 'block';
                            menu.classList.add('show');
                            this.setAttribute('aria-expanded', 'true');
                            console.log('✅ Dropdown opened');
                            console.log('✅ Menu display after open:', menu.style.display);
                        }
                    } else {
                        console.error('❌ Menu not found for dropdown:', this.textContent.trim());
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.style.display = 'none';
                        menu.classList.remove('show');
                    });
                    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                        toggle.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            console.log('✅ Dropdown initialization complete');
            
            // Initialize dynamic translation system
            initializeDynamicTranslation();
            
            // Test dropdown functionality
            setTimeout(() => {
                console.log('🧪 Testing dropdown functionality...');
                const testToggle = document.querySelector('.dropdown-toggle');
                if (testToggle) {
                    console.log('Test toggle found:', testToggle.textContent.trim());
                    console.log('Test toggle parent:', testToggle.closest('.dropdown'));
                    console.log('Test menu found:', testToggle.closest('.dropdown')?.querySelector('.dropdown-menu'));
                } else {
                    console.error('❌ No dropdown toggles found!');
                }
            }, 1000);
        });
        
        // Dynamic Translation System
        function initializeDynamicTranslation() {
            console.log('🌐 Initializing dynamic translation system...');
            
            // Translation mappings for common text
            const translations = {
                'en': {
                    'Cash Book': 'Cash Book',
                    'Manage daily cash transactions and entries.': 'Manage daily cash transactions and entries.',
                    'Simple Entry': 'Simple Entry',
                    'Daily Entry': 'Daily Entry',
                    'Receive Entry': 'Receive Entry',
                    'Payment Entry': 'Payment Entry',
                    'Export': 'Export',
                    'Print': 'Print',
                    'Search': 'Search',
                    'Filter': 'Filter',
                    'Add': 'Add',
                    'Edit': 'Edit',
                    'Delete': 'Delete',
                    'View': 'View',
                    'Save': 'Save',
                    'Cancel': 'Cancel',
                    'Create': 'Create',
                    'Update': 'Update',
                    'Back': 'Back',
                    'Submit': 'Submit',
                    'Reset': 'Reset',
                    'Close': 'Close',
                    'Open': 'Open',
                    'Show': 'Show',
                    'Hide': 'Hide',
                    'Loading...': 'Loading...',
                    'Please wait...': 'Please wait...',
                    'Primary Journey Voucher': 'Primary Journey Voucher',
                    'Secondary Journey Voucher': 'Secondary Journey Voucher',
                    'Create a new primary journey voucher for cargo transport.': 'Create a new primary journey voucher for cargo transport.',
                    'Create a new secondary journey voucher for return cargo transport.': 'Create a new secondary journey voucher for return cargo transport.',
                    'Journey Date': 'Journey Date',
                    'Vehicle': 'Vehicle',
                    'Carriage Name': 'Carriage Name',
                    'Loading Point': 'Loading Point',
                    'Destination': 'Destination',
                    'Product': 'Product',
                    'Company': 'Company',
                    'Capacity': 'Capacity',
                    'Company Freight Rate': 'Company Freight Rate',
                    'Vehicle Freight Rate': 'Vehicle Freight Rate',
                    'Shortage Quantity': 'Shortage Quantity',
                    'Shortage Rate': 'Shortage Rate',
                    'Commission Amount': 'Commission Amount',
                    'Total Amount': 'Total Amount',
                    'Select Vehicle': 'Select Vehicle',
                    'Select Carriage': 'Select Carriage',
                    'Select Loading Point': 'Select Loading Point',
                    'Select Destination': 'Select Destination',
                    'Select Product': 'Select Product',
                    'Select Company': 'Select Company',
                    'Back to List': 'Back to List',
                    'Create Primary Journey': 'Create Primary Journey',
                    'Create Secondary Journey': 'Create Secondary Journey',
                },
                'ur': {
                    'Cash Book': 'کیش بک',
                    'Manage daily cash transactions and entries.': 'روزانہ کیش ٹرانزیکشن اور انٹریز کا انتظام کریں۔',
                    'Simple Entry': 'سادہ انٹری',
                    'Daily Entry': 'روزانہ انٹری',
                    'Receive Entry': 'وصولی انٹری',
                    'Payment Entry': 'ادائیگی انٹری',
                    'Export': 'ایکسپورٹ',
                    'Print': 'پرنٹ',
                    'Search': 'تلاش',
                    'Filter': 'فلٹر',
                    'Add': 'شامل کریں',
                    'Edit': 'ترمیم',
                    'Delete': 'حذف',
                    'View': 'دیکھیں',
                    'Save': 'محفوظ',
                    'Cancel': 'منسوخ',
                    'Create': 'بنائیں',
                    'Update': 'اپڈیٹ',
                    'Back': 'واپس',
                    'Submit': 'جمع کریں',
                    'Reset': 'ری سیٹ',
                    'Close': 'بند',
                    'Open': 'کھولیں',
                    'Show': 'دکھائیں',
                    'Hide': 'چھپائیں',
                    'Loading...': 'لوڈ ہو رہا ہے...',
                    'Please wait...': 'براہ کرم انتظار کریں...',
                    'Primary Journey Voucher': 'پرائمری سفر واؤچر',
                    'Secondary Journey Voucher': 'سیکنڈری سفر واؤچر',
                    'Create a new primary journey voucher for cargo transport.': 'کارگو ٹرانسپورٹ کے لیے نیا پرائمری سفر واؤچر بنائیں۔',
                    'Create a new secondary journey voucher for return cargo transport.': 'واپسی کارگو ٹرانسپورٹ کے لیے نیا سیکنڈری سفر واؤچر بنائیں۔',
                    'Journey Date': 'سفر کی تاریخ',
                    'Vehicle': 'گاڑی',
                    'Carriage Name': 'کیریج کا نام',
                    'Loading Point': 'لوڈنگ پوائنٹ',
                    'Destination': 'منزل',
                    'Product': 'پروڈکٹ',
                    'Company': 'کمپنی',
                    'Capacity': 'گنجائش',
                    'Company Freight Rate': 'کمپنی فریٹ ریٹ',
                    'Vehicle Freight Rate': 'گاڑی فریٹ ریٹ',
                    'Shortage Quantity': 'کمی کی مقدار',
                    'Shortage Rate': 'کمی کی شرح',
                    'Commission Amount': 'کمیشن کی رقم',
                    'Total Amount': 'کل رقم',
                    'Select Vehicle': 'گاڑی منتخب کریں',
                    'Select Carriage': 'کیریج منتخب کریں',
                    'Select Loading Point': 'لوڈنگ پوائنٹ منتخب کریں',
                    'Select Destination': 'منزل منتخب کریں',
                    'Select Product': 'پروڈکٹ منتخب کریں',
                    'Select Company': 'کمپنی منتخب کریں',
                    'Back to List': 'فہرست میں واپس',
                    'Create Primary Journey': 'پرائمری سفر بنائیں',
                    'Create Secondary Journey': 'سیکنڈری سفر بنائیں',
                }
            };
            
            // Function to translate text
            function translateText(text, language) {
                const langTranslations = translations[language] || translations['en'];
                return langTranslations[text] || text;
            }
            
            // Function to translate all text on page
            function translatePage(language) {
                console.log('🔄 Translating page to:', language);
                
                // Get all text nodes
                const walker = document.createTreeWalker(
                    document.body,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );
                
                const textNodes = [];
                let node;
                while (node = walker.nextNode()) {
                    if (node.textContent.trim() && !node.parentElement.closest('script, style, code')) {
                        textNodes.push(node);
                    }
                }
                
                // Translate text nodes
                textNodes.forEach(textNode => {
                    const originalText = textNode.textContent.trim();
                    const translatedText = translateText(originalText, language);
                    if (translatedText !== originalText) {
                        textNode.textContent = translatedText;
                    }
                });
                
                // Translate button text and placeholders
                document.querySelectorAll('button, input[type="submit"], input[type="button"]').forEach(element => {
                    if (element.textContent) {
                        const originalText = element.textContent.trim();
                        const translatedText = translateText(originalText, language);
                        if (translatedText !== originalText) {
                            element.textContent = translatedText;
                        }
                    }
                });
                
                // Translate placeholders
                document.querySelectorAll('input[placeholder], textarea[placeholder]').forEach(element => {
                    if (element.placeholder) {
                        const originalText = element.placeholder.trim();
                        const translatedText = translateText(originalText, language);
                        if (translatedText !== originalText) {
                            element.placeholder = translatedText;
                        }
                    }
                });
                
                console.log('✅ Page translation completed');
            }
            
            // Listen for language changes
            document.addEventListener('languageChanged', function(event) {
                translatePage(event.detail.language);
            });
            
            // Initial translation
            const currentLanguage = '{{ app("App\\Services\\LanguageService")::getCurrentLanguage() }}';
            if (currentLanguage === 'ur') {
                translatePage('ur');
            }
            
            console.log('✅ Dynamic translation system initialized');
        }
    </script>
</body>
</html>
