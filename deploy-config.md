# 🚀 Production Environment Configuration

## Environment Variables for Railway/Render

Copy these environment variables to your hosting platform:

```bash
# Application
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

# Database (Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=your-railway-db-host
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-railway-db-password

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Transport Fleet Management"
```

## Database Setup Commands

After deployment, run these commands in your hosting platform's console:

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed the database
php artisan db:seed --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Admin User Creation

Create an admin user after deployment:

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->is_active = true;
$user->save();
$user->assignRole('Super Admin');
exit
```

## File Permissions

Ensure these directories are writable:
- `storage/`
- `bootstrap/cache/`

## Performance Optimization

For production, consider:
- Enable Redis for caching
- Use database sessions
- Set up queue workers
- Configure proper logging