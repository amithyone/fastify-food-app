#!/bin/bash

# cPanel Deployment Script
# This script deploys your Fastify app to cPanel via SSH

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 cPanel Deployment Script${NC}"
echo "================================="

# Configuration (update these with your details)
CPANEL_HOST="your-server.com"
CPANEL_USER="your-cpanel-username"
CPANEL_PATH="/home/your-cpanel-username/public_html/fastify"
GIT_REPO="https://github.com/amithyone/fastify-food-app.git"

echo -e "${YELLOW}📋 Configuration:${NC}"
echo "Host: $CPANEL_HOST"
echo "User: $CPANEL_USER"
echo "Path: $CPANEL_PATH"
echo "Repo: $GIT_REPO"
echo ""

# Function to deploy via SSH
deploy_via_ssh() {
    echo -e "${YELLOW}🚀 Deploying to cPanel...${NC}"
    
    # SSH commands to execute on the server
    ssh_commands="
        cd $CPANEL_PATH || mkdir -p $CPANEL_PATH && cd $CPANEL_PATH
        if [ ! -d '.git' ]; then
            git clone $GIT_REPO .
        else
            git fetch origin
            git reset --hard origin/main
        fi
        composer install --no-dev --optimize-autoloader
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        chmod -R 755 storage bootstrap/cache
        echo 'Deployment completed successfully!'
    "
    
    # Execute SSH commands
    ssh $CPANEL_USER@$CPANEL_HOST "$ssh_commands"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Deployment successful!${NC}"
    else
        echo -e "${RED}❌ Deployment failed${NC}"
        exit 1
    fi
}

# Function to setup initial deployment
setup_initial_deployment() {
    echo -e "${YELLOW}🔧 Setting up initial deployment...${NC}"
    
    ssh_commands="
        mkdir -p $CPANEL_PATH
        cd $CPANEL_PATH
        git clone $GIT_REPO .
        composer install --no-dev --optimize-autoloader
        cp .env.example .env
        php artisan key:generate
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        chmod -R 755 storage bootstrap/cache
        echo 'Initial setup completed!'
    "
    
    ssh $CPANEL_USER@$CPANEL_HOST "$ssh_commands"
}

# Main menu
echo -e "${BLUE}Choose an option:${NC}"
echo "1) Initial deployment (first time)"
echo "2) Update existing deployment"
echo "3) Exit"
echo ""
read -p "Enter your choice (1-3): " choice

case $choice in
    1)
        setup_initial_deployment
        ;;
    2)
        deploy_via_ssh
        ;;
    3)
        echo -e "${BLUE}Exiting...${NC}"
        exit 0
        ;;
    *)
        echo -e "${RED}Invalid choice${NC}"
        exit 1
        ;;
esac 