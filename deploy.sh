#!/bin/bash

# Fastify Deployment Script
# This script helps deploy your local Laravel application to a remote server

# Configuration
REMOTE_HOST="${REMOTE_HOST:-your-server-ip-or-domain}"
REMOTE_USER="${REMOTE_USER:-your-username}"
REMOTE_PATH="${REMOTE_PATH:-/var/www/html/fastify}"
LOCAL_PATH="."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Fastify Deployment Script${NC}"
echo "=================================="

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check prerequisites
echo -e "${YELLOW}Checking prerequisites...${NC}"

if ! command_exists rsync; then
    echo -e "${RED}❌ rsync is not installed. Please install it first.${NC}"
    exit 1
fi

if ! command_exists ssh; then
    echo -e "${RED}❌ ssh is not installed. Please install it first.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Prerequisites check passed${NC}"

# Function to backup remote database
backup_remote_database() {
    echo -e "${YELLOW}📦 Creating remote database backup...${NC}"
    ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_PATH && php artisan db:backup --filename=backup_$(date +%Y%m%d_%H%M%S).sql"
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Database backup created${NC}"
    else
        echo -e "${RED}❌ Database backup failed${NC}"
        return 1
    fi
}

# Function to sync files
sync_files() {
    echo -e "${YELLOW}📤 Syncing files to remote server...${NC}"
    
    # Create a temporary directory for files to sync
    TEMP_DIR=$(mktemp -d)
    
    # Copy files to temp directory, excluding unnecessary files
    rsync -av --exclude='.git' \
              --exclude='node_modules' \
              --exclude='vendor' \
              --exclude='storage/logs/*' \
              --exclude='storage/framework/cache/*' \
              --exclude='storage/framework/sessions/*' \
              --exclude='storage/framework/views/*' \
              --exclude='.env' \
              --exclude='.DS_Store' \
              --exclude='*.log' \
              --exclude='deploy.sh' \
              $LOCAL_PATH/ $TEMP_DIR/
    
    # Sync to remote server
    rsync -avz --delete $TEMP_DIR/ $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/
    
    # Clean up temp directory
    rm -rf $TEMP_DIR
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Files synced successfully${NC}"
    else
        echo -e "${RED}❌ File sync failed${NC}"
        return 1
    fi
}

# Function to run remote commands
run_remote_commands() {
    echo -e "${YELLOW}🔧 Running remote setup commands...${NC}"
    
    ssh $REMOTE_USER@$REMOTE_HOST << 'EOF'
        cd $REMOTE_PATH
        
        echo "Installing Composer dependencies..."
        composer install --no-dev --optimize-autoloader
        
        echo "Installing NPM dependencies..."
        npm install
        
        echo "Building assets..."
        npm run build
        
        echo "Running database migrations..."
        php artisan migrate --force
        
        echo "Clearing caches..."
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
        
        echo "Optimizing application..."
        php artisan optimize
        
        echo "Setting proper permissions..."
        chmod -R 755 storage bootstrap/cache
        chown -R www-data:www-data storage bootstrap/cache
        
        echo "Restarting services..."
        sudo systemctl restart php8.1-fpm
        sudo systemctl restart nginx
EOF
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Remote commands completed successfully${NC}"
    else
        echo -e "${RED}❌ Remote commands failed${NC}"
        return 1
    fi
}

# Function to test deployment
test_deployment() {
    echo -e "${YELLOW}🧪 Testing deployment...${NC}"
    
    # Test if the application is accessible
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://$REMOTE_HOST)
    
    if [ "$HTTP_STATUS" = "200" ]; then
        echo -e "${GREEN}✅ Application is accessible (HTTP $HTTP_STATUS)${NC}"
    else
        echo -e "${RED}❌ Application returned HTTP $HTTP_STATUS${NC}"
        return 1
    fi
}

# Main deployment function
deploy() {
    echo -e "${BLUE}Starting deployment process...${NC}"
    
    # Step 1: Backup database
    backup_remote_database || exit 1
    
    # Step 2: Sync files
    sync_files || exit 1
    
    # Step 3: Run remote commands
    run_remote_commands || exit 1
    
    # Step 4: Test deployment
    test_deployment || exit 1
    
    echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${BLUE}Your application is now live at: http://$REMOTE_HOST${NC}"
}

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  deploy          Deploy the application"
    echo "  backup          Create database backup only"
    echo "  sync            Sync files only"
    echo "  setup           Run remote setup commands only"
    echo "  test            Test deployment only"
    echo "  help            Show this help message"
    echo ""
    echo "Before running, please update the configuration variables at the top of this script:"
    echo "  - REMOTE_HOST: Your server IP or domain"
    echo "  - REMOTE_USER: Your SSH username"
    echo "  - REMOTE_PATH: Path to your application on the server"
}

# Parse command line arguments
case "${1:-deploy}" in
    "deploy")
        deploy
        ;;
    "backup")
        backup_remote_database
        ;;
    "sync")
        sync_files
        ;;
    "setup")
        run_remote_commands
        ;;
    "test")
        test_deployment
        ;;
    "help"|"-h"|"--help")
        show_usage
        ;;
    *)
        echo -e "${RED}Unknown option: $1${NC}"
        show_usage
        exit 1
        ;;
esac 