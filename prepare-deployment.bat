@echo off
echo ========================================
echo Transport Fleet Management System
echo Deployment Preparation Script
echo ========================================
echo.

echo Creating deployment package...
echo.

REM Create a clean deployment folder
if exist "deployment-package" rmdir /s /q "deployment-package"
mkdir "deployment-package"

REM Copy all necessary files
echo Copying application files...
xcopy /E /I /Y "app" "deployment-package\app"
xcopy /E /I /Y "bootstrap" "deployment-package\bootstrap"
xcopy /E /I /Y "config" "deployment-package\config"
xcopy /E /I /Y "database" "deployment-package\database"
xcopy /E /I /Y "public" "deployment-package\public"
xcopy /E /I /Y "resources" "deployment-package\resources"
xcopy /E /I /Y "routes" "deployment-package\routes"
xcopy /E /I /Y "storage" "deployment-package\storage"

REM Copy configuration files
echo Copying configuration files...
copy "composer.json" "deployment-package\"
copy "composer.lock" "deployment-package\"
copy "artisan" "deployment-package\"
copy "Procfile" "deployment-package\"
copy "railway.json" "deployment-package\"
copy ".env.example" "deployment-package\"

REM Copy deployment documentation
echo Copying deployment documentation...
copy "DEPLOYMENT_PACKAGE.md" "deployment-package\"
copy "DEPLOY_NOW.md" "deployment-package\"
copy "README.md" "deployment-package\"

echo.
echo ========================================
echo DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo Your deployment package is ready in the 'deployment-package' folder.
echo.
echo Next steps:
echo 1. Create a GitHub repository
echo 2. Upload the contents of 'deployment-package' folder
echo 3. Deploy on Railway
echo.
echo See DEPLOYMENT_PACKAGE.md for detailed instructions.
echo.
pause
