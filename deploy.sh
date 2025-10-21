#!/bin/bash

# 🚀 Transport Fleet Management - Deployment Script
# This script prepares the Laravel application for deployment

echo "🚀 Preparing Transport Fleet Management for deployment..."

# Clear all caches
echo "📦 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "✅ Deployment preparation complete!"
echo "🌐 Your application is ready for production!"
echo ""
echo "📋 Next steps:"
echo "1. Push code to GitHub repository"
echo "2. Connect to Railway/Render"
echo "3. Set environment variables"
echo "4. Deploy!"