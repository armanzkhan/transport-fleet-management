# MySQL Database Setup Guide

## 📋 **Overview**

This guide will help you switch from SQLite to MySQL for the Transport Fleet Management System.

---

## 🔧 **Step 1: Install MySQL (if not already installed)**

### **Windows:**
1. Download MySQL Installer from: https://dev.mysql.com/downloads/installer/
2. Run the installer and follow the setup wizard
3. Choose "Developer Default" or "Server only" installation
4. Set root password (remember this!)
5. Complete the installation

### **Verify Installation:**
```bash
mysql --version
```

---

## 🗄️ **Step 2: Create Database**

Open MySQL command line or MySQL Workbench and run:

```sql
CREATE DATABASE transport_fleet_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create a dedicated user (optional but recommended)
CREATE USER 'fleet_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON transport_fleet_management.* TO 'fleet_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## ⚙️ **Step 3: Update .env File**

Update your `.env` file with the following MySQL configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transport_fleet_management
DB_USERNAME=root
# OR use the dedicated user:
# DB_USERNAME=fleet_user
DB_PASSWORD=your_mysql_password
```

**Important:** Replace `your_mysql_password` with your actual MySQL root password (or the password for your dedicated user).

---

## 📦 **Step 4: Run Migrations**

After updating the `.env` file, run the migrations to create all tables:

```bash
php artisan migrate
```

If you have seeders:

```bash
php artisan migrate --seed
```

---

## 🔄 **Step 5: Migrate Existing Data (if applicable)**

If you have existing SQLite data that needs to be migrated:

1. **Export SQLite data:**
   ```bash
   sqlite3 database/database.sqlite .dump > sqlite_backup.sql
   ```

2. **Convert SQLite dump to MySQL format** (manually or using a converter)

3. **Import into MySQL:**
   ```bash
   mysql -u root -p transport_fleet_management < converted_mysql_backup.sql
   ```

**Note:** Direct SQLite to MySQL conversion requires manual adjustment due to syntax differences.

---

## ✅ **Step 6: Verify Connection**

Test the MySQL connection:

```bash
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
// Should return: PDO object

DB::select('SELECT DATABASE()');
// Should show: transport_fleet_management
```

---

## 🚨 **Troubleshooting**

### **Error: SQLSTATE[HY000] [2002] Connection refused**
- Check if MySQL service is running
- Verify `DB_HOST` is correct (usually `127.0.0.1` or `localhost`)
- Check `DB_PORT` (default is `3306`)

### **Error: SQLSTATE[HY000] [1045] Access denied**
- Verify username and password in `.env`
- Check if user has privileges: `SHOW GRANTS FOR 'username'@'localhost';`

### **Error: SQLSTATE[HY000] [1049] Unknown database**
- Create the database first: `CREATE DATABASE transport_fleet_management;`

### **Error: PDOException - could not find driver**
- Install PHP MySQL extension:
  - Windows: Enable `php_pdo_mysql` extension in `php.ini`
  - Linux: `sudo apt-get install php-mysql` or `sudo yum install php-mysql`

---

## 📝 **Configuration Summary**

### **Updated Files:**
- ✅ `config/database.php` - Changed default from `sqlite` to `mysql`
- ⚠️ `.env` - **YOU NEED TO UPDATE THIS MANUALLY**

### **MySQL Connection Settings:**
```
Host: 127.0.0.1 (or localhost)
Port: 3306
Database: transport_fleet_management
Username: root (or your dedicated user)
Password: [your MySQL password]
```

---

## 🔐 **Security Recommendations**

1. **Use a dedicated database user** instead of root:
   ```sql
   CREATE USER 'fleet_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON transport_fleet_management.* TO 'fleet_user'@'localhost';
   ```

2. **Never commit `.env` file** (already in `.gitignore`)

3. **Use strong passwords** for production environments

4. **Enable SSL** for remote MySQL connections (if applicable)

---

## ✅ **Quick Setup Script**

For quick setup, you can also create a temporary setup script:

```php
// Create: setup-mysql.php in project root
// Run: php setup-mysql.php

<?php
require __DIR__.'/vendor/autoload.php';

$database = 'transport_fleet_management';
$username = 'root';
$password = 'your_password'; // Change this!

try {
    $pdo = new PDO("mysql:host=127.0.0.1", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' created successfully!\n";
} catch(PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
```

---

**Last Updated:** 2025-01-21  
**Status:** ✅ Ready for MySQL Setup

