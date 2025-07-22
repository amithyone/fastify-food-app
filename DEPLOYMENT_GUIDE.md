# 🚀 Fastify Deployment Guide

## 📋 Prerequisites

Before deploying, ensure you have:

1. **SSH Access**: Your SSH key is already provided
2. **Server Access**: Access to your remote server
3. **Local Development**: Your local Laravel application is working

## 🔧 Step 1: Configure Deployment Settings

### 1.1 Update Configuration File

Edit `deploy-config.sh` with your server details:

```bash
# Server Configuration
REMOTE_HOST="your-actual-server-ip-or-domain.com"
REMOTE_USER="your-server-username"
REMOTE_PATH="/var/www/html/fastify"  # or your preferred path
```

### 1.2 Set Up SSH Key

Add your SSH key to the server:

```bash
# Copy your SSH key to the server
ssh-copy-id -i ~/.ssh/id_rsa.pub your-username@your-server-ip

# Or manually add the key to ~/.ssh/authorized_keys on the server
```

## 🚀 Step 2: Initial Server Setup

### 2.1 Connect to Your Server

```bash
ssh your-username@your-server-ip
```

### 2.2 Install Required Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Nginx
sudo apt install nginx -y

# Install MySQL (if not already installed)
sudo apt install mysql-server -y
```

### 2.3 Create Application Directory

```bash
# Create application directory
sudo mkdir -p /var/www/html/fastify
sudo chown -R $USER:$USER /var/www/html/fastify
```

## 📦 Step 3: Deploy Your Application

### 3.1 Make Deployment Script Executable

```bash
chmod +x deploy.sh
chmod +x deploy-config.sh
```

### 3.2 Run Initial Deployment

```bash
# Source the configuration
source deploy-config.sh

# Run full deployment
./deploy.sh deploy
```

## 🔄 Step 4: Update Application (Future Deployments)

### 4.1 Quick Deploy (Recommended)

```bash
# Deploy all changes
./deploy.sh deploy
```

### 4.2 Selective Deployments

```bash
# Only sync files (no database changes)
./deploy.sh sync

# Only run setup commands (after manual file upload)
./deploy.sh setup

# Only create database backup
./deploy.sh backup

# Test if deployment is working
./deploy.sh test
```

## ⚙️ Step 5: Server Configuration

### 5.1 Nginx Configuration

Create `/etc/nginx/sites-available/fastify`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/html/fastify/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/fastify /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5.2 Environment Configuration

Create `.env` file on the server:

```bash
# Connect to server
ssh your-username@your-server-ip

# Navigate to application directory
cd /var/www/html/fastify

# Copy example environment file
cp .env.example .env

# Edit environment file
nano .env
```

Update the `.env` file with your production settings:

```env
APP_NAME="Fastify"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fastify_db
DB_USERNAME=fastify_user
DB_PASSWORD=your-secure-password

# Restaurant Configuration
RESTAURANT_NAME="Your Restaurant Name"
RESTAURANT_DISPLAY_NAME="Your Restaurant - Food Ordering"
RESTAURANT_SHORT_NAME="Restaurant"
RESTAURANT_THEME_COLOR="#f97316"

# Twilio Configuration
TWILIO_ACCOUNT_SID=your-twilio-sid
TWILIO_AUTH_TOKEN=your-twilio-token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

### 5.3 Generate Application Key

```bash
php artisan key:generate
```

### 5.4 Set Up Database

```bash
# Create database
mysql -u root -p
CREATE DATABASE fastify_db;
CREATE USER 'fastify_user'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON fastify_db.* TO 'fastify_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

## 🔒 Step 6: Security Setup

### 6.1 Set Proper Permissions

```bash
sudo chown -R www-data:www-data /var/www/html/fastify
sudo chmod -R 755 /var/www/html/fastify
sudo chmod -R 775 /var/www/html/fastify/storage
sudo chmod -R 775 /var/www/html/fastify/bootstrap/cache
```

### 6.2 SSL Certificate (Optional but Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 📱 Step 7: PWA Configuration

### 7.1 Update PWA Settings

Edit your `.env` file to include PWA settings:

```env
# PWA Configuration
PWA_NAME="Your Restaurant - Food Ordering"
PWA_SHORT_NAME="Restaurant"
PWA_DESCRIPTION="Order delicious food from Your Restaurant"
PWA_THEME_COLOR="#f97316"
PWA_BACKGROUND_COLOR="#ffffff"
```

### 7.2 Generate PWA Assets

```bash
# On your local machine, generate icons
php artisan pwa:generate-icons

# Upload the generated icons to the server
scp -r public/icons your-username@your-server-ip:/var/www/html/fastify/public/
```

## 🔄 Step 8: Continuous Deployment

### 8.1 Automated Deployment Script

Create a simple deployment alias in your `~/.bashrc`:

```bash
echo 'alias deploy="cd /path/to/your/local/project && ./deploy.sh deploy"' >> ~/.bashrc
source ~/.bashrc
```

Now you can simply run:

```bash
deploy
```

### 8.2 Git Integration (Optional)

If you're using Git, you can set up automatic deployment:

```bash
# On your server, create a deployment hook
mkdir -p /var/www/html/fastify/.git/hooks

# Create post-receive hook
cat > /var/www/html/fastify/.git/hooks/post-receive << 'EOF'
#!/bin/bash
cd /var/www/html/fastify
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan optimize
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
EOF

chmod +x /var/www/html/fastify/.git/hooks/post-receive
```

## 🚨 Troubleshooting

### Common Issues

1. **Permission Denied**: Check file permissions and ownership
2. **Database Connection**: Verify database credentials and connection
3. **500 Error**: Check Laravel logs in `storage/logs/laravel.log`
4. **Assets Not Loading**: Run `npm run build` and check file permissions

### Useful Commands

```bash
# Check Laravel logs
tail -f /var/www/html/fastify/storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.1-fpm.log

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart mysql
```

## 📞 Support

If you encounter issues:

1. Check the logs mentioned above
2. Verify all configuration files
3. Ensure all dependencies are installed
4. Test each step individually

Your application should now be successfully deployed and accessible at `http://your-domain.com`! 