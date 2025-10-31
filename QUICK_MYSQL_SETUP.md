# 🚀 Quick MySQL Setup

## ✅ **Configuration Updated**

I've updated `config/database.php` to default to MySQL. Now you need to update your `.env` file.

---

## ⚡ **Quick Steps**

### **1. Update .env File**

Open your `.env` file and replace the database section with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transport_fleet_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Important:** Replace `your_mysql_password` with your actual MySQL root password!

---

### **2. Create MySQL Database**

Open MySQL command line or MySQL Workbench and run:

```sql
CREATE DATABASE transport_fleet_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### **3. Run Migrations**

```bash
php artisan migrate
```

If you have seeders:

```bash
php artisan migrate --seed
```

---

### **4. Verify Connection**

Test that it works:

```bash
php artisan tinker
```

Then run:
```php
DB::connection()->getPdo();
DB::select('SELECT DATABASE()');
```

---

## 🔧 **Alternative: Use the PowerShell Script**

I've created an automated script for you:

```powershell
.\update-database-config.ps1
```

This will:
1. Prompt you for MySQL credentials
2. Automatically update your `.env` file
3. Set up all database configuration

---

## 📝 **What Was Changed**

✅ **`config/database.php`** - Changed default from `sqlite` to `mysql`  
⚠️ **`.env`** - **YOU NEED TO UPDATE THIS** (see steps above)

---

## 🚨 **If MySQL is Not Installed**

1. **Download MySQL:** https://dev.mysql.com/downloads/installer/
2. **Install MySQL** with root password
3. **Verify:** `mysql --version`

---

## ✅ **Current Status**

- ✅ Config updated to MySQL default
- ⚠️ `.env` file needs manual update
- 📝 Scripts and documentation created

**Next Step:** Update your `.env` file as shown in Step 1 above!

