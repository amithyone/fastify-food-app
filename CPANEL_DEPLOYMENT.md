# 🚀 cPanel Deployment Guide

## 📋 **Prerequisites**

Before deploying to cPanel, ensure you have:
- ✅ **cPanel access** with Git Version Control
- ✅ **PHP 8.1+** support
- ✅ **Composer** support
- ✅ **MySQL/MariaDB** database
- ✅ **SSL certificate** (recommended)

## 🎯 **Method 1: cPanel Git Version Control (Easiest)**

### **Step 1: Access Git Version Control**
1. Login to your cPanel
2. Find "Git Version Control" in the Files section
3. Click "Git Version Control"

### **Step 2: Create Repository**
```
Repository Name: fastify-food-app
Repository URL: https://github.com/amithyone/fastify-food-app.git
Branch: main
Directory: /public_html/fastify
```

### **Step 3: Initial Setup**
1. Click "Create"
2. Wait for initial clone to complete
3. Click "Update from Remote" to pull latest changes

### **Step 4: Configure Application**
1. **Set up database** in cPanel MySQL Databases
2. **Configure .env file** with database credentials
3. **Run Laravel commands** via SSH or Terminal

## 🔧 **Method 2: SSH Deployment (Advanced)**

### **Step 1: Enable SSH Access**
1. In cPanel, go to "SSH Access"
2. Generate SSH key or use password authentication
3. Note your SSH connection details

### **Step 2: Deploy via SSH**
```bash
# Connect to your server
ssh username@your-server.com

# Navigate to public_html
cd public_html

# Clone repository
git clone https://github.com/amithyone/fastify-food-app.git fastify
cd fastify

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure database
# Edit .env file with your database credentials

# Run migrations
php artisan migrate

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage bootstrap/cache
```

## 📁 **Method 3: Manual Upload**

### **Step 1: Download from GitHub**
1. Go to: https://github.com/amithyone/fastify-food-app
2. Click "Code" → "Download ZIP"
3. Extract the ZIP file

### **Step 2: Upload to cPanel**
1. Open cPanel File Manager
2. Navigate to `/public_html/`
3. Create folder: `fastify`
4. Upload all files from extracted ZIP

### **Step 3: Configure via SSH/Terminal**
```bash
cd public_html/fastify
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Configure .env with database details
php artisan migrate
php artisan config:cache
```

## ⚙️ **Configuration Steps**

### **1. Database Setup**
1. **Create database** in cPanel MySQL Databases
2. **Create database user** with full privileges
3. **Note credentials** for .env file

### **2. Environment Configuration**
Edit `.env` file:
```env
APP_NAME="Fastify"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com/fastify

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email
MAIL_FROM_NAME="${APP_NAME}"
```

### **3. File Permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### **4. Laravel Commands**
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## 🔄 **Updating Your Application**

### **Method 1: cPanel Git Version Control**
1. Go to Git Version Control
2. Click on your repository
3. Click "Update from Remote"
4. Select "main" branch
5. Click "Update"

### **Method 2: SSH Update**
```bash
ssh username@your-server.com
cd public_html/fastify
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Method 3: Manual Update**
1. Download latest ZIP from GitHub
2. Upload new files via File Manager
3. Run Laravel commands via SSH

## 🛡️ **Security Considerations**

### **Production Settings**
- ✅ Set `APP_ENV=production`
- ✅ Set `APP_DEBUG=false`
- ✅ Use strong database passwords
- ✅ Enable SSL/HTTPS
- ✅ Set proper file permissions

### **File Permissions**
```bash
# Directories
chmod 755 storage
chmod 755 bootstrap/cache
chmod 755 public

# Files
chmod 644 .env
chmod 644 composer.json
chmod 644 package.json
```

## 🚨 **Troubleshooting**

### **Common Issues**

**1. 500 Internal Server Error**
- Check file permissions
- Check .env configuration
- Check error logs in cPanel

**2. Database Connection Error**
- Verify database credentials
- Check database exists
- Verify user privileges

**3. Composer Issues**
- Ensure Composer is available
- Check PHP version compatibility
- Clear Composer cache

**4. Permission Denied**
- Set proper file permissions
- Check ownership
- Verify directory structure

### **Useful Commands**
```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Check Laravel
php artisan --version

# View logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📞 **Support**

If you encounter issues:
1. Check cPanel error logs
2. Verify all prerequisites
3. Test locally first
4. Contact your hosting provider

Your Fastify application is now ready for cPanel deployment! 🚀 