# 🚀 Transport Fleet Management - Deployment Guide

## Free Hosting Options

### Option 1: Railway (Recommended)
- **Free Tier**: $5 credit monthly
- **Database**: MySQL included
- **Domain**: Free subdomain
- **Deployment**: Git-based

### Option 2: Render
- **Free Tier**: 750 hours/month
- **Database**: PostgreSQL (can use MySQL)
- **Domain**: Free subdomain
- **Deployment**: Git-based

### Option 3: Heroku
- **Free Tier**: Limited (paid plans available)
- **Database**: PostgreSQL addon
- **Domain**: Free subdomain
- **Deployment**: Git-based

## 🚀 Railway Deployment (Step-by-Step)

### Step 1: Prepare Your Repository
1. Push your code to GitHub
2. Make sure all files are committed
3. Ensure `.env` is in `.gitignore`

### Step 2: Create Railway Account
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub
3. Connect your GitHub account

### Step 3: Deploy Application
1. Click "New Project"
2. Select "Deploy from GitHub repo"
3. Choose your repository
4. Railway will automatically detect Laravel

### Step 4: Add MySQL Database
1. In your project dashboard
2. Click "New" → "Database" → "MySQL"
3. Railway will create a MySQL database
4. Copy the connection details

### Step 5: Configure Environment Variables
In Railway dashboard, go to Variables tab and add:

```bash
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
```

### Step 6: Deploy
1. Railway will automatically build and deploy
2. Check the logs for any errors
3. Your app will be available at `https://your-app-name.railway.app`

### Step 7: Run Database Setup
1. Go to your app's URL
2. The app should automatically run migrations
3. If not, you can run them manually via Railway console

## 🚀 Render Deployment (Alternative)

### Step 1: Create Render Account
1. Go to [render.com](https://render.com)
2. Sign up with GitHub

### Step 2: Create Web Service
1. Click "New" → "Web Service"
2. Connect your GitHub repository
3. Choose "Docker" as environment

### Step 3: Configure Build Settings
```bash
Build Command: composer install --optimize-autoloader --no-dev
Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
```

### Step 4: Add Database
1. Create a new PostgreSQL database
2. Note the connection details
3. Update environment variables

## 🔧 Post-Deployment Setup

### 1. Generate Application Key
```bash
php artisan key:generate
```

### 2. Run Migrations
```bash
php artisan migrate --force
```

### 3. Seed Database
```bash
php artisan db:seed --force
```

### 4. Create Admin User
```bash
php artisan tinker
```
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('password');
$user->is_active = true;
$user->save();
$user->assignRole('Super Admin');
```

## 🌐 Custom Domain (Optional)

### Railway
1. Go to project settings
2. Click "Domains"
3. Add your custom domain
4. Update DNS records

### Render
1. Go to service settings
2. Click "Custom Domains"
3. Add your domain
4. Update DNS records

## 📊 Monitoring & Maintenance

### Health Checks
- Monitor application logs
- Check database connections
- Verify file permissions

### Updates
- Push changes to GitHub
- Platform will auto-deploy
- Monitor deployment logs

## 🔒 Security Considerations

1. **Environment Variables**: Never commit `.env` files
2. **Database**: Use strong passwords
3. **HTTPS**: Enable SSL certificates
4. **Updates**: Keep dependencies updated

## 🆘 Troubleshooting

### Common Issues
1. **Database Connection**: Check environment variables
2. **File Permissions**: Ensure storage is writable
3. **Memory Limits**: Check platform limits
4. **Build Failures**: Check logs for errors

### Support
- Railway: [docs.railway.app](https://docs.railway.app)
- Render: [render.com/docs](https://render.com/docs)
- Laravel: [laravel.com/docs](https://laravel.com/docs)

## 🎯 Expected Results

After successful deployment, you should have:
- ✅ Working Laravel application
- ✅ MySQL database with all tables
- ✅ Seeded data (users, roles, permissions)
- ✅ Accessible via web URL
- ✅ All features working (dashboards, reports, etc.)

## 📱 Access Information

Once deployed, you'll get:
- **Application URL**: `https://your-app-name.railway.app`
- **Admin Login**: `admin@example.com` / `password`
- **Database**: Accessible via Railway dashboard
- **Logs**: Available in platform dashboard

---

**Ready to deploy? Follow the steps above and you'll have a fully working Transport Fleet Management system online!** 🚀