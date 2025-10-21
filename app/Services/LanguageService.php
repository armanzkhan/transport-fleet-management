<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageService
{
    const LANGUAGES = [
        'en' => 'English',
        'ur' => 'اردو'
    ];

    const DEFAULT_LANGUAGE = 'en';

    /**
     * Get current language
     */
    public static function getCurrentLanguage()
    {
        return Session::get('language', self::DEFAULT_LANGUAGE);
    }

    /**
     * Set language
     */
    public static function setLanguage($language)
    {
        if (array_key_exists($language, self::LANGUAGES)) {
            Session::put('language', $language);
            App::setLocale($language);
            return true;
        }
        return false;
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages()
    {
        return self::LANGUAGES;
    }

    /**
     * Get translation for a key
     */
    public static function translate($key, $default = null)
    {
        $language = self::getCurrentLanguage();
        
        // Load translations from database or config
        $translations = self::getTranslations($language);
        
        return $translations[$key] ?? $default ?? $key;
    }

    /**
     * Get all translations for current language
     */
    public static function getTranslations($language = null)
    {
        $language = $language ?? self::getCurrentLanguage();
        
        // Try to load from translation files first
        $filePath = lang_path($language . '/messages.php');
        if (file_exists($filePath)) {
            return include $filePath;
        }
        
        // Fallback to static translations
        return self::getStaticTranslations($language);
    }

    /**
     * Get static translations
     */
    private static function getStaticTranslations($language)
    {
        $translations = [
            'en' => [
                // Navigation
                'dashboard' => 'Dashboard',
                'main_dashboard' => 'Main Dashboard',
                'admin_dashboard' => 'Admin Dashboard',
                'fleet_dashboard' => 'Fleet Dashboard',
                'finance_dashboard' => 'Finance Dashboard',
                'vehicles' => 'Vehicles',
                'owners' => 'Vehicle Owners',
                'cash_books' => 'Cash Books',
                'cash_book' => 'Cash Book',
                'journey_vouchers' => 'Journey Vouchers',
                'primary_journey_vouchers' => 'Primary Journey Vouchers',
                'secondary_journey_vouchers' => 'Secondary Journey Vouchers',
                'tariffs' => 'Tariffs',
                'journey_summary' => 'Journey Summary',
                'vehicle_billing' => 'Vehicle Billing',
                'reports' => 'Reports',
                'all_reports' => 'All Reports',
                'users' => 'Users',
                'settings' => 'Settings',
                'shortcuts' => 'Shortcuts',
                'developer_access' => 'Developer Access',
                
                // Common Actions
                'add' => 'Add',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'view' => 'View',
                'print' => 'Print',
                'export' => 'Export',
                'save' => 'Save',
                'cancel' => 'Cancel',
                'search' => 'Search',
                'filter' => 'Filter',
                'create' => 'Create',
                'update' => 'Update',
                'back' => 'Back',
                'next' => 'Next',
                'previous' => 'Previous',
                'submit' => 'Submit',
                'reset' => 'Reset',
                'close' => 'Close',
                'open' => 'Open',
                'show' => 'Show',
                'hide' => 'Hide',
                'loading' => 'Loading...',
                'please_wait' => 'Please wait...',
                
                // Cash Book
                'cash_book' => 'Cash Book',
                'receive_entry' => 'Receive Entry',
                'payment_entry' => 'Payment Entry',
                'daily_entry' => 'Daily Entry',
                'simple_entry' => 'Simple Entry',
                'transaction_number' => 'Transaction Number',
                'account_selection' => 'Account Selection',
                'description' => 'Description',
                'amount' => 'Amount',
                'previous_day_balance' => 'Previous Day Balance',
                'total_cash_in_hand' => 'Total Cash in Hand',
                
                // Journey Vouchers
                'journey_number' => 'Journey Number',
                'vrn' => 'VRN',
                'carriage_name' => 'Carriage Name',
                'loading_point' => 'Loading Point',
                'destination' => 'Destination',
                'product' => 'Product',
                'company' => 'Company',
                'capacity' => 'Capacity',
                'company_freight_rate' => 'Company Freight Rate',
                'vehicle_freight_rate' => 'Vehicle Freight Rate',
                'shortage_quantity' => 'Shortage Quantity',
                'shortage_rate' => 'Shortage Rate',
                
                // Reports
                'general_ledger' => 'General Ledger',
                'company_summary' => 'Company Summary',
                'carriage_summary' => 'Carriage Summary',
                'monthly_vehicle_bills' => 'Monthly Vehicle Bills',
                'income_reports' => 'Income Reports',
                'pending_trips' => 'Pending Trips',
                'vehicle_database' => 'Vehicle Database',
                
                // Status
                'active' => 'Active',
                'inactive' => 'Inactive',
                'pending' => 'Pending',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                
                // Currency
                'currency' => 'PKR',
                'currency_symbol' => '₨',
            ],
            
            'ur' => [
                // Navigation
                'dashboard' => 'ڈیش بورڈ',
                'main_dashboard' => 'مین ڈیش بورڈ',
                'admin_dashboard' => 'ایڈمن ڈیش بورڈ',
                'fleet_dashboard' => 'فلیٹ ڈیش بورڈ',
                'finance_dashboard' => 'فنانس ڈیش بورڈ',
                'vehicles' => 'گاڑیاں',
                'owners' => 'گاڑی کے مالک',
                'cash_books' => 'کیش بک',
                'cash_book' => 'کیش بک',
                'journey_vouchers' => 'سفر کے واؤچر',
                'primary_journey_vouchers' => 'پرائمری سفر واؤچر',
                'secondary_journey_vouchers' => 'سیکنڈری سفر واؤچر',
                'tariffs' => 'ٹیرف',
                'journey_summary' => 'سفر کا خلاصہ',
                'vehicle_billing' => 'گاڑی کی بلنگ',
                'reports' => 'رپورٹس',
                'all_reports' => 'تمام رپورٹس',
                'users' => 'صارفین',
                'settings' => 'سیٹنگز',
                'shortcuts' => 'شارٹ کٹس',
                'developer_access' => 'ڈیولپر رسائی',
                
                // Common Actions
                'add' => 'شامل کریں',
                'edit' => 'ترمیم',
                'delete' => 'حذف',
                'view' => 'دیکھیں',
                'print' => 'پرنٹ',
                'export' => 'ایکسپورٹ',
                'save' => 'محفوظ',
                'cancel' => 'منسوخ',
                'search' => 'تلاش',
                'filter' => 'فلٹر',
                'create' => 'بنائیں',
                'update' => 'اپڈیٹ',
                'back' => 'واپس',
                'next' => 'اگلا',
                'previous' => 'پچھلا',
                'submit' => 'جمع کریں',
                'reset' => 'ری سیٹ',
                'close' => 'بند',
                'open' => 'کھولیں',
                'show' => 'دکھائیں',
                'hide' => 'چھپائیں',
                'loading' => 'لوڈ ہو رہا ہے...',
                'please_wait' => 'براہ کرم انتظار کریں...',
                
                // Cash Book
                'cash_book' => 'کیش بک',
                'receive_entry' => 'وصولی انٹری',
                'payment_entry' => 'ادائیگی انٹری',
                'daily_entry' => 'روزانہ انٹری',
                'simple_entry' => 'سادہ انٹری',
                'transaction_number' => 'ٹرانزیکشن نمبر',
                'account_selection' => 'اکاؤنٹ کا انتخاب',
                'description' => 'تفصیل',
                'amount' => 'رقم',
                'previous_day_balance' => 'پچھلے دن کا بیلنس',
                'total_cash_in_hand' => 'کل نقد رقم',
                
                // Journey Vouchers
                'journey_number' => 'سفر نمبر',
                'vrn' => 'گاڑی نمبر',
                'carriage_name' => 'کیریج کا نام',
                'loading_point' => 'لوڈنگ پوائنٹ',
                'destination' => 'منزل',
                'product' => 'پروڈکٹ',
                'company' => 'کمپنی',
                'capacity' => 'گنجائش',
                'company_freight_rate' => 'کمپنی فریٹ ریٹ',
                'vehicle_freight_rate' => 'گاڑی فریٹ ریٹ',
                'shortage_quantity' => 'کمی کی مقدار',
                'shortage_rate' => 'کمی کی شرح',
                
                // Reports
                'general_ledger' => 'جنرل لیجر',
                'company_summary' => 'کمپنی کا خلاصہ',
                'carriage_summary' => 'کیریج کا خلاصہ',
                'monthly_vehicle_bills' => 'ماہانہ گاڑی کے بل',
                'income_reports' => 'آمدنی کی رپورٹس',
                'pending_trips' => 'زیر التواء سفر',
                'vehicle_database' => 'گاڑی کا ڈیٹابیس',
                
                // Status
                'active' => 'فعال',
                'inactive' => 'غیر فعال',
                'pending' => 'زیر التواء',
                'completed' => 'مکمل',
                'cancelled' => 'منسوخ',
                
                // Currency
                'currency' => 'پاکستانی روپے',
                'currency_symbol' => '₨',
            ]
        ];

        return $translations[$language] ?? $translations['en'];
    }

    /**
     * Get language direction
     */
    public static function getDirection()
    {
        $language = self::getCurrentLanguage();
        return $language === 'ur' ? 'rtl' : 'ltr';
    }

    /**
     * Get language name
     */
    public static function getLanguageName($code = null)
    {
        $code = $code ?? self::getCurrentLanguage();
        return self::LANGUAGES[$code] ?? self::LANGUAGES[self::DEFAULT_LANGUAGE];
    }
}
