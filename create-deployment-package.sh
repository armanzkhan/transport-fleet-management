#!/bin/bash

# Create Deployment Package for Free Hosting
echo "📦 Creating deployment package for free hosting..."

# Create deployment directory
mkdir -p deployment-package
cd deployment-package

# Copy all project files
cp -r ../app .
cp -r ../bootstrap .
cp -r ../config .
cp -r ../database .
cp -r ../lang .
cp -r ../public .
cp -r ../resources .
cp -r ../routes .
cp -r ../storage .
cp -r ../tests .
cp -r ../vendor .

# Copy essential files
cp ../artisan .
cp ../composer.json .
cp ../composer.lock .
cp ../package.json .
cp ../package-lock.json .
cp ../phpunit.xml .
cp ../vite.config.js .

# Create .htaccess for public folder
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

# Create environment file template
cat > .env.example << 'EOF'
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.000webhostapp.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
EOF

# Create setup instructions
cat > SETUP_INSTRUCTIONS.md << 'EOF'
# 🚀 FREE HOSTING SETUP INSTRUCTIONS

## **Step 1: Upload Files**
1. Upload all files to your hosting provider
2. Ensure all files are in the correct directory structure

## **Step 2: Configure Database**
1. Create MySQL database in your hosting panel
2. Note the database credentials
3. Update .env file with correct database details

## **Step 3: Set Environment Variables**
1. Rename .env.example to .env
2. Update APP_URL with your domain
3. Update database credentials

## **Step 4: Run Laravel Commands**
1. Access your hosting control panel
2. Open terminal/console
3. Run these commands:

```bash
# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create admin user
php artisan tinker
```

In tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->is_active = true;
$user->save();
exit
```

## **Step 5: Test Your App**
1. Visit your domain
2. Login with admin@example.com / password123
3. Test all features

## **🎯 SUCCESS!**
Your Transport Fleet Management System is now live!
EOF

# Create ZIP package
cd ..
zip -r transport-fleet-management-deployment.zip deployment-package/

echo "✅ Deployment package created successfully!"
echo "📦 Package: transport-fleet-management-deployment.zip"
echo "🚀 Ready to upload to free hosting!"
