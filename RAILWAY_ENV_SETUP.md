# 🔧 RAILWAY ENVIRONMENT SETUP

## **🚨 CRITICAL: Set These Environment Variables**

Go to Railway dashboard → **Variables** tab and add these **EXACT** variables:

```bash
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_DEBUG=true
APP_URL=https://trolley.proxy.rlwy.net

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
LOG_LEVEL=debug
```

## **🔧 ALTERNATIVE CONFIGURATIONS**

### **Option 1: Use Simple Start Script**
- Current configuration uses `start.sh`
- This should work with proper environment variables

### **Option 2: Use No Script (Simplest)**
1. **Rename `railway-no-script.json` to `railway.json`**
2. **Commit and push**
3. **Set environment variables manually in Railway console**

### **Option 3: Use Ultra Simple**
1. **Rename `railway-ultra-simple.json` to `railway.json`**
2. **Commit and push**
3. **Set environment variables manually in Railway console**

## **📋 MANUAL SETUP (If Scripts Fail)**

### **Step 1: Access Railway Console**
1. **Go to Railway dashboard**
2. **Click on your service**
3. **Go to "Deployments" tab**
4. **Click "View Logs"**
5. **Click "Open Console"**

### **Step 2: Run Manual Commands**
In Railway console, run these commands one by one:

```bash
# Create directories
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

# Generate app key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Create admin user
php artisan tinker
```

### **Step 3: Create Admin User**
In tinker, run:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->is_active = true;
$user->save();
exit
```

### **Step 4: Start Server**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

## **🎯 EXPECTED RESULTS**

After setting environment variables and running manual setup:
- ✅ **Application accessible** at https://trolley.proxy.rlwy.net
- ✅ **No more healthcheck failures**
- ✅ **Laravel welcome page** or login page
- ✅ **Admin login** works with admin@example.com / password123

## **📞 TROUBLESHOOTING**

### **If Still Getting 404:**
1. **Check environment variables** are set correctly
2. **Try the no-script configuration**
3. **Run manual setup** in Railway console
4. **Check Railway logs** for specific errors

### **If Healthcheck Still Fails:**
1. **Use `railway-no-script.json`** configuration
2. **Set environment variables** manually
3. **Run manual setup** in Railway console
4. **Start server manually**

**The key is setting the environment variables correctly in Railway dashboard!** 🔑
