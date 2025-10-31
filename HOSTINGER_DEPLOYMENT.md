# 🚀 Hostinger Business Web Hosting Deployment Guide

## 📋 **Overview**

This guide will help you deploy the Transport Fleet Management System on Hostinger Business Web Hosting.

---

## ✅ **Prerequisites**

1. **Hostinger Business Web Hosting Account**
   - Access to cPanel
   - MySQL database access
   - SSH access (optional but recommended)

2. **Requirements:**
   - PHP 8.2 or higher
   - MySQL 5.7+ or MariaDB 10.3+
   - Composer installed (or use cPanel's installer)
   - Git access (or FTP for file upload)

---

## 📦 **Step 1: Prepare Your Application**

### **1.1 Update Environment Configuration**

Your application is already configured for MySQL. Ensure your `.env` file has:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

---

## 🗄️ **Step 2: Create MySQL Database**

### **2.1 Via cPanel:**

1. Log into your Hostinger cPanel
2. Navigate to **"MySQL Databases"**
3. Create a new database:
   - Database name: `fleet_management` (or your preferred name)
   - Note the full database name (usually: `username_fleet_management`)
4. Create a database user:
   - Username: Create a new user
   - Password: Set a strong password
   - Save the credentials!
5. Add user to database:
   - Select the user and database
   - Grant **ALL PRIVILEGES**
   - Click **"Make Changes"**

### **2.2 Note Database Credentials:**

```
Database Host: localhost (usually)
Database Name: username_fleet_management
Database User: username_dbuser
Database Password: your_password
```

---

## 📤 **Step 3: Upload Files**

### **Option A: Using Git (Recommended)**

If you have SSH access:

```bash
# SSH into your hosting account
ssh username@yourdomain.com

# Navigate to your public_html or domain folder
cd public_html

# Clone your repository (if you have Git on hosting)
git clone https://github.com/yourusername/transport-fleet-management.git .

# Or pull latest changes if already cloned
git pull origin main
```

### **Option B: Using FTP/SFTP**

1. **Connect via FTP Client:**
   - Host: `ftp.yourdomain.com` or your server IP
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload Files:**
   - Upload all files to `public_html/` or your domain's root folder
   - **Important:** Do NOT upload:
     - `node_modules/`
     - `vendor/` (we'll install via Composer)
     - `.env` (create on server)
     - `.git/`
     - `storage/logs/`
     - `storage/framework/cache/`
     - `storage/framework/sessions/`
     - `storage/framework/views/`

---

## ⚙️ **Step 4: Configure Application**

### **4.1 Set Up .env File**

1. Create `.env` file in project root (via cPanel File Manager or FTP)

2. Copy from `.env.example` and update:

```env
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_fleet_management
DB_USERNAME=username_dbuser
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
```

### **4.2 Generate Application Key**

Via SSH:
```bash
php artisan key:generate
```

Or via cPanel Terminal:
```bash
cd public_html
php artisan key:generate
```

### **4.3 Install Dependencies**

Via SSH or cPanel Terminal:
```bash
cd public_html
composer install --no-dev --optimize-autoloader
```

---

## 🔧 **Step 5: Configure Web Server**

### **5.1 Set Document Root**

The `public/` folder should be your document root.

**Option A: If you have domain root access:**
- Move contents of `public/` to `public_html/`
- Update paths accordingly

**Option B: Configure via .htaccess (Recommended):**

1. Create `.htaccess` in `public_html/`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

2. Your structure should be:
```
public_html/
├── .htaccess (redirects to public/)
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── ...
├── app/
├── config/
├── database/
└── ...
```

### **5.2 Set Permissions**

Via SSH:
```bash
cd public_html
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

## 🗄️ **Step 6: Run Migrations**

Via SSH or cPanel Terminal:
```bash
cd public_html
php artisan migrate --force
php artisan migrate --seed
```

**Note:** `--force` is needed in production environment.

---

## 🔐 **Step 7: Create Admin User**

Via SSH or cPanel Terminal:
```bash
cd public_html
php artisan tinker
```

Then in tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@yourdomain.com';
$user->password = Hash::make('your_secure_password');
$user->is_active = true;
$user->save();

// Assign admin role
$user->assignRole('admin');
```

---

## ✅ **Step 8: Optimize Application**

Via SSH or cPanel Terminal:
```bash
cd public_html

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## 🌐 **Step 9: Configure Domain**

### **9.1 Update .env:**
```env
APP_URL=https://yourdomain.com
```

### **9.2 Clear and Rebuild Cache:**
```bash
php artisan config:clear
php artisan config:cache
```

---

## 🔒 **Step 10: Security Checklist**

1. ✅ Set `APP_DEBUG=false` in `.env`
2. ✅ Set proper file permissions (755 for directories, 644 for files)
3. ✅ Protect `.env` file (ensure it's not publicly accessible)
4. ✅ Use strong database passwords
5. ✅ Enable SSL/HTTPS (usually automatic on Hostinger)
6. ✅ Set up regular backups
7. ✅ Keep Laravel and dependencies updated

---

## 📝 **Hostinger-Specific Settings**

### **PHP Version:**
1. Go to cPanel → **Select PHP Version**
2. Choose **PHP 8.2** or higher
3. Enable required extensions:
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `zip`
   - `fileinfo`
   - `gd`

### **Memory Limit:**
- Recommended: 256M or higher
- Set in `.htaccess` or `php.ini`:
```apache
php_value memory_limit 256M
```

### **Max Upload Size:**
- Recommended: 50M or higher
- Set in `.htaccess`:
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
```

---

## 🚨 **Troubleshooting**

### **Error: 500 Internal Server Error**
- Check `.env` file exists and has correct permissions
- Check `storage/` and `bootstrap/cache/` permissions (755)
- Check error logs in cPanel → Error Log
- Verify `APP_KEY` is set

### **Error: Database Connection Failed**
- Verify database credentials in `.env`
- Check database host (usually `localhost`)
- Ensure database user has proper privileges
- Test connection via cPanel → MySQL Databases

### **Error: File Permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### **Error: Composer Not Found**
- Use cPanel's Composer installer
- Or install via SSH: `curl -sS https://getcomposer.org/installer | php`
- Use: `php composer.phar install` instead of `composer install`

### **Error: Route Not Found**
- Clear route cache: `php artisan route:clear`
- Rebuild cache: `php artisan route:cache`

---

## 📊 **File Structure on Hostinger**

```
public_html/
├── .htaccess (redirects to public/)
├── .env (your environment config)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── ...
├── resources/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
└── vendor/
```

---

## ✅ **Post-Deployment Checklist**

- [ ] Database created and configured
- [ ] `.env` file created with production settings
- [ ] `APP_KEY` generated
- [ ] Dependencies installed (`composer install`)
- [ ] Migrations run successfully
- [ ] Admin user created
- [ ] File permissions set correctly
- [ ] Application optimized (config/route/view cache)
- [ ] SSL/HTTPS enabled
- [ ] Application accessible via domain
- [ ] Login page works
- [ ] All features tested

---

## 🔄 **Updating Application**

When you need to update:

1. **Via Git (if using):**
```bash
cd public_html
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Via FTP:**
- Upload new files
- Run migrations via SSH/terminal
- Clear and rebuild cache

---

## 📞 **Support**

- **Hostinger Support:** https://www.hostinger.com/contact
- **Laravel Documentation:** https://laravel.com/docs
- **Project Issues:** Create an issue in your repository

---

**Last Updated:** 2025-01-21  
**Status:** ✅ Ready for Hostinger Deployment

