#!/bin/bash

# Pre-start script for Railway
echo "🚀 Pre-start script running..."

# Create necessary directories
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p database

# Create database file
touch database/database.sqlite
chmod 664 database/database.sqlite

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Generate app key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Create admin user if not exists
php artisan tinker --execute="
try {
    \$user = App\Models\User::where('email', 'admin@example.com')->first();
    if (!\$user) {
        \$user = new App\Models\User();
        \$user->name = 'Admin';
        \$user->email = 'admin@example.com';
        \$user->password = Hash::make('password123');
        \$user->is_active = true;
        \$user->save();
        echo 'Admin user created successfully!';
    } else {
        echo 'Admin user already exists!';
    }
} catch (Exception \$e) {
    echo 'User creation failed: ' . \$e->getMessage();
}
"

# Cache for production
php artisan config:cache
php artisan route:cache

echo "✅ Pre-start script completed!"
