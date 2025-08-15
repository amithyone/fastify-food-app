#!/bin/bash

# Laravel Asset Deployment Script
# This script helps deploy assets without requiring Node.js on the production server

echo "🚀 Laravel Asset Deployment Script"
echo "=================================="

# Check if we're in the Laravel project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the Laravel project root directory"
    exit 1
fi

# Check if build directory exists
if [ ! -d "public/build" ]; then
    echo "❌ Error: Build directory not found. Please run 'npm run build' first."
    exit 1
fi

echo "✅ Build directory found"

# Set proper permissions
echo "🔧 Setting permissions..."
chmod -R 755 public/build
chmod 644 public/build/manifest.json

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Set asset mode to built
echo "⚙️  Setting asset mode to 'built'..."
if grep -q "ASSET_MODE" .env; then
    sed -i 's/ASSET_MODE=.*/ASSET_MODE=built/' .env
else
    echo "ASSET_MODE=built" >> .env
fi

echo "✅ Assets deployed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Upload the entire project to your production server"
echo "2. Run 'php artisan migrate' on the production server"
echo "3. Set proper permissions: chmod -R 755 storage bootstrap/cache"
echo "4. Test your application"
echo ""
echo "🎉 Your Laravel app is now ready to run without Node.js!"
