#!/bin/bash

# Emergency Railway Fix Script
echo "🚨 Emergency Railway Fix Starting..."

# Set basic environment
export APP_ENV=production
export APP_DEBUG=true
export APP_KEY=base64:$(openssl rand -base64 32)
export DB_CONNECTION=sqlite
export DB_DATABASE=/app/database/database.sqlite

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

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate key
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Create basic admin user
php artisan tinker --execute="
try {
    \$user = new App\Models\User();
    \$user->name = 'Admin';
    \$user->email = 'admin@example.com';
    \$user->password = Hash::make('password123');
    \$user->is_active = true;
    \$user->save();
    echo 'Admin user created successfully!';
} catch (Exception \$e) {
    echo 'User creation failed: ' . \$e->getMessage();
}
"

# Cache for production
php artisan config:cache
php artisan route:cache

echo "✅ Emergency fix completed!"
echo "🚀 Starting Laravel server..."

# Start the server
exec php artisan serve --host=0.0.0.0 --port=$PORT
