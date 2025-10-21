#!/bin/bash

# Render Build Script for Laravel
echo "🚀 Starting Render build process..."

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

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Create admin user
php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Admin';
\$user->email = 'admin@example.com';
\$user->password = Hash::make('password123');
\$user->is_active = true;
\$user->save();
echo 'Admin user created successfully!';
"

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Render build completed successfully!"
