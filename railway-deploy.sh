#!/bin/bash

# Railway Deployment Script
# This script runs after deployment to set up the application

echo "🚀 Starting Railway deployment setup..."

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "⚙️ Optimizing configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database with initial data
echo "🌱 Seeding database..."
php artisan db:seed --force

# Cache for production
echo "🚀 Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app/public

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "✅ Railway deployment setup complete!"
echo "🌐 Your application should now be accessible!"
