#!/bin/bash

echo "🔧 Fixing production deployment issues..."

# Set proper permissions
echo "📁 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/

# Create storage link if it doesn't exist
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear all caches
echo "🗑️ Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Set proper ownership (adjust user/group as needed)
echo "👤 Setting ownership..."
# sudo chown -R www-data:www-data storage/
# sudo chown -R www-data:www-data bootstrap/cache/

# Check storage directory permissions
echo "🔍 Checking storage permissions..."
ls -la storage/
ls -la public/

# Test image access
echo "🖼️ Testing image access..."
curl -I http://localhost/storage/app/public/test.jpg 2>/dev/null || echo "Storage images may need manual configuration"

echo "✅ Production fixes applied!"
echo "📝 Next steps:"
echo "1. Restart your web server (Apache/Nginx)"
echo "2. Check .htaccess is being read"
echo "3. Verify storage link exists"
echo "4. Test image uploads and display" 