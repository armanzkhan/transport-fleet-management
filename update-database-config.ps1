# PowerShell script to update .env file for MySQL configuration
# Run this script: .\update-database-config.ps1

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  MySQL Database Configuration Setup" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Check if .env file exists
if (-not (Test-Path ".env")) {
    Write-Host "❌ Error: .env file not found!" -ForegroundColor Red
    Write-Host "   Please create .env file first by copying .env.example" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Found .env file" -ForegroundColor Green
Write-Host ""

# Get MySQL configuration from user
$dbHost = Read-Host "Enter MySQL Host [127.0.0.1]"
if ([string]::IsNullOrWhiteSpace($dbHost)) { $dbHost = "127.0.0.1" }

$dbPort = Read-Host "Enter MySQL Port [3306]"
if ([string]::IsNullOrWhiteSpace($dbPort)) { $dbPort = "3306" }

$dbDatabase = Read-Host "Enter Database Name [transport_fleet_management]"
if ([string]::IsNullOrWhiteSpace($dbDatabase)) { $dbDatabase = "transport_fleet_management" }

$dbUsername = Read-Host "Enter MySQL Username [root]"
if ([string]::IsNullOrWhiteSpace($dbUsername)) { $dbUsername = "root" }

$securePassword = Read-Host "Enter MySQL Password" -AsSecureString
$dbPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
)

Write-Host ""
Write-Host "Configuration Summary:" -ForegroundColor Yellow
Write-Host "  Host: $dbHost"
Write-Host "  Port: $dbPort"
Write-Host "  Database: $dbDatabase"
Write-Host "  Username: $dbUsername"
Write-Host ""

$confirm = Read-Host "Apply these settings? (Y/N)"
if ($confirm -ne "Y" -and $confirm -ne "y") {
    Write-Host "❌ Configuration cancelled" -ForegroundColor Red
    exit 0
}

# Read .env file
$envContent = Get-Content ".env" -Raw

# Update or add database configuration
$dbConfig = @"
DB_CONNECTION=mysql
DB_HOST=$dbHost
DB_PORT=$dbPort
DB_DATABASE=$dbDatabase
DB_USERNAME=$dbUsername
DB_PASSWORD=$dbPassword
"@

# Remove old database configuration if exists
$envContent = $envContent -replace "(?m)^DB_CONNECTION=.*$", ""
$envContent = $envContent -replace "(?m)^DB_HOST=.*$", ""
$envContent = $envContent -replace "(?m)^DB_PORT=.*$", ""
$envContent = $envContent -replace "(?m)^DB_DATABASE=.*$", ""
$envContent = $envContent -replace "(?m)^DB_USERNAME=.*$", ""
$envContent = $envContent -replace "(?m)^DB_PASSWORD=.*$", ""
$envContent = $envContent -replace "(?m)^DB_URL=.*$", ""

# Remove extra blank lines
$envContent = $envContent -replace "(?m)^\s*$\r?\n", ""

# Add new database configuration
$envContent += "`n`n" + $dbConfig

# Write back to .env file
Set-Content -Path ".env" -Value $envContent -NoNewline

Write-Host ""
Write-Host "✅ .env file updated successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Create the database: CREATE DATABASE $dbDatabase;" -ForegroundColor White
Write-Host "  2. Run migrations: php artisan migrate" -ForegroundColor White
Write-Host "  3. Run seeders (if any): php artisan migrate --seed" -ForegroundColor White
Write-Host ""

