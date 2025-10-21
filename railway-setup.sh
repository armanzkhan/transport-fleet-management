#!/bin/bash

# Railway Setup Script
echo "🚀 Setting up Railway deployment..."

# Create database file
echo "📁 Creating database file..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# Generate app key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Create admin user
echo "👤 Creating admin user..."
php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Admin';
\$user->email = 'admin@example.com';
\$user->password = Hash::make('password123');
\$user->is_active = true;
\$user->save();
\$user->assignRole('Super Admin');
echo 'Admin user created successfully!';
"

# Cache for production
echo "🚀 Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Railway setup complete!"
echo "🌐 Your application should now be accessible!"
