-- Production Database Setup for Transport Fleet Management System
-- This file contains the database configuration for production deployment

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS transport_fleet_management;

-- Use the database
USE transport_fleet_management;

-- Create admin user for production
-- This will be run after deployment to create the initial admin user

-- Production Environment Variables Template:
-- Copy these to your hosting platform's environment variables

/*
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

DB_CONNECTION=mysql
DB_HOST=your-railway-db-host
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-railway-db-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
*/

-- Post-deployment commands to run:
-- php artisan migrate --force
-- php artisan db:seed --force
-- php artisan key:generate --force
-- php artisan config:cache
-- php artisan route:cache
-- php artisan view:cache
