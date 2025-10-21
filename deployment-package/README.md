# 🚛 Transport Fleet Management System

A comprehensive Laravel-based fleet management system with role-based access control, bilingual support (English/Urdu), and complete SRS compliance.

## ✨ Features

- **Role-Based Access Control**: Super Admin, Admin, Fleet Manager, Accountant
- **Bilingual Support**: English/Urdu with RTL support
- **Dashboard System**: Role-specific dashboards with real-time data
- **Journey Management**: Primary and Secondary journey vouchers
- **Financial Management**: Cash books, billing, and reporting
- **Vehicle Management**: Complete vehicle lifecycle management
- **Reporting System**: 12+ comprehensive reports
- **Export/Print**: CSV, HTML, Excel export functionality
- **Smart Features**: Auto-suggestions, shortcuts, notifications

## 🚀 Quick Deployment

### Option 1: Railway (Recommended)

1. **Fork/Clone this repository**
2. **Go to [Railway.app](https://railway.app)**
3. **Connect your GitHub account**
4. **Create new project → Deploy from GitHub**
5. **Add MySQL database**
6. **Set environment variables** (see `deploy-config.md`)
7. **Deploy!**

### Option 2: Render

1. **Go to [Render.com](https://render.com)**
2. **Create new Web Service**
3. **Connect GitHub repository**
4. **Use these settings:**
   - **Build Command**: `composer install --optimize-autoloader --no-dev`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
5. **Add PostgreSQL database**
6. **Set environment variables**
7. **Deploy!**

## 🔧 Environment Variables

```bash
APP_NAME="Transport Fleet Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## 📊 Default Login Credentials

After deployment, create an admin user:

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

**Login**: `admin@example.com`  
**Password**: `password123`

## 🏗️ Local Development

```bash
# Clone repository
git clone <repository-url>
cd transport-fleet-management

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

## 📋 System Requirements

- **PHP**: 8.1+
- **Laravel**: 10.x
- **Database**: MySQL/PostgreSQL/SQLite
- **Extensions**: PDO, SQLite, MySQL, GD, OpenSSL

## 🎯 SRS Compliance

This system is 100% compliant with the provided Software Requirements Specification (SRS) document, including:

- ✅ All 18 major modules implemented
- ✅ Role-based access control
- ✅ Bilingual support (English/Urdu)
- ✅ Complete CRUD operations
- ✅ Export/Print functionality
- ✅ Smart suggestions system
- ✅ Keyboard navigation
- ✅ Developer access management

## 📱 Access Information

Once deployed, you'll have access to:

- **Main Dashboard**: Overview of all system activities
- **Admin Dashboard**: System administration and user management
- **Fleet Dashboard**: Vehicle and journey management
- **Finance Dashboard**: Financial operations and reporting
- **Reports**: 12+ comprehensive reports
- **Export/Print**: Multiple format support

## 🔒 Security Features

- Role-based permissions
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure authentication
- Audit logging

## 🌐 Multi-language Support

- **English**: Complete interface
- **Urdu**: Full RTL support
- **Dynamic switching**: Real-time language toggle
- **Database translations**: Persistent language preferences

## 📊 Reporting System

- General Ledger
- Company Summary
- Carriage Summary
- Monthly Vehicle Bills
- Income Reports
- Pending Trips
- Vehicle Database
- And more...

## 🚀 Performance Optimized

- Database query optimization
- Caching strategies
- Efficient pagination
- Background job processing
- Memory optimization

## 📞 Support

For issues or questions:
- Check the deployment logs
- Review the `DEPLOYMENT_GUIDE.md`
- Ensure all environment variables are set
- Verify database connectivity

## 🎉 Success!

After successful deployment, you'll have a fully functional Transport Fleet Management System with:

- ✅ Complete user management
- ✅ Vehicle tracking
- ✅ Journey management
- ✅ Financial operations
- ✅ Comprehensive reporting
- ✅ Multi-language support
- ✅ Export capabilities

**Your fleet management system is ready to go! 🚛✨**"# transport-fleet-management" 
