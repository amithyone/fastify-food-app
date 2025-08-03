#!/bin/bash

# Update PWA Logos Script
echo "🔄 Updating PWA logos..."

# Check if logo files exist
if [ ! -f "applogo.png" ]; then
    echo "❌ applogo.png not found. Please upload the logo files first."
    exit 1
fi

if [ ! -f "applogo72x72.png" ]; then
    echo "❌ applogo72x72.png not found. Please upload the logo files first."
    exit 1
fi

if [ ! -f "applogo92x92.png" ]; then
    echo "❌ applogo92x92.png not found. Please upload the logo files first."
    exit 1
fi

if [ ! -f "applogo192x192.png" ]; then
    echo "❌ applogo192x192.png not found. Please upload the logo files first."
    exit 1
fi

if [ ! -f "icon-512x512.png" ]; then
    echo "❌ icon-512x512.png not found. Please upload the logo files first."
    exit 1
fi

echo "✅ All logo files found. Updating..."

# Copy main logo to favicon
cp applogo.png public/favicon.png
echo "✅ Updated favicon.png"

# Copy to images directory
cp applogo.png public/images/fastify-logo.png
echo "✅ Updated fastify-logo.png"

# Update icon files
cp applogo72x72.png public/icons/icon-72x72.png
echo "✅ Updated icon-72x72.png"

cp applogo92x92.png public/icons/icon-96x96.png
echo "✅ Updated icon-96x96.png"

cp applogo192x192.png public/icons/icon-192x192.png
echo "✅ Updated icon-192x192.png"

cp icon-512x512.png public/icons/icon-512x512.png
echo "✅ Updated icon-512x512.png"

# Clear caches
php artisan config:clear
php artisan view:clear
echo "✅ Cleared caches"

echo "🎉 All logos updated successfully!"
echo "📱 Your PWA will now use the new Fastify logos"
echo "🔄 Restart your server to see the changes" 