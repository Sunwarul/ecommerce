#!/bin/bash

echo "================================================"
echo "🚀 Setting up React + Inertia + PrimeReact"
echo "================================================"
echo ""

# Step 1: Remove old node_modules
echo "📦 Step 1: Cleaning old dependencies..."
if [ -d "node_modules" ]; then
    echo "   Removing node_modules..."
    rm -rf node_modules
fi

if [ -f "package-lock.json" ]; then
    echo "   Removing package-lock.json..."
    rm package-lock.json
fi

echo "   ✅ Clean complete"
echo ""

# Step 2: Install new dependencies
echo "📦 Step 2: Installing React dependencies..."
npm install
if [ $? -eq 0 ]; then
    echo "   ✅ Dependencies installed successfully"
else
    echo "   ❌ Installation failed!"
    exit 1
fi
echo ""

# Step 3: Clear Laravel caches
echo "🧹 Step 3: Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "   ✅ Caches cleared"
echo ""

# Step 4: Build assets
echo "🔨 Step 4: Building assets..."
npm run build
if [ $? -eq 0 ]; then
    echo "   ✅ Assets built successfully"
else
    echo "   ❌ Build failed!"
    exit 1
fi
echo ""

echo "================================================"
echo "✅ Setup Complete!"
echo "================================================"
echo ""
echo "Next steps:"
echo "1. Start Laravel:  php artisan serve"
echo "2. Start Vite:     npm run dev"
echo "3. Test Generator: php artisan blueprint:build example-blueprint-test.yaml"
echo ""
echo "📚 Read INSTALLATION.md for more details"
echo "================================================"

