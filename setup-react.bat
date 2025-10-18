@echo off
echo ================================================
echo 🚀 Setting up React + Inertia + PrimeReact
echo ================================================
echo.

REM Step 1: Remove old node_modules
echo 📦 Step 1: Cleaning old dependencies...
if exist node_modules (
    echo    Removing node_modules...
    rmdir /s /q node_modules
)

if exist package-lock.json (
    echo    Removing package-lock.json...
    del package-lock.json
)

echo    ✅ Clean complete
echo.

REM Step 2: Install new dependencies
echo 📦 Step 2: Installing React dependencies...
call npm install
if %errorlevel% neq 0 (
    echo    ❌ Installation failed!
    exit /b 1
)
echo    ✅ Dependencies installed successfully
echo.

REM Step 3: Clear Laravel caches
echo 🧹 Step 3: Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo    ✅ Caches cleared
echo.

REM Step 4: Build assets
echo 🔨 Step 4: Building assets...
call npm run build
if %errorlevel% neq 0 (
    echo    ❌ Build failed!
    exit /b 1
)
echo    ✅ Assets built successfully
echo.

echo ================================================
echo ✅ Setup Complete!
echo ================================================
echo.
echo Next steps:
echo 1. Start Laravel:  php artisan serve
echo 2. Start Vite:     npm run dev
echo 3. Test Generator: php artisan blueprint:build example-blueprint-test.yaml
echo.
echo 📚 Read INSTALLATION.md for more details
echo ================================================
pause

