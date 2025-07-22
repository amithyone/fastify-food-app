#!/bin/bash

# Fastify Deployment Workflow
# This script handles the complete deployment workflow from local development to server

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Fastify Deployment Workflow${NC}"
echo "====================================="

# Load configuration
if [ -f "deploy-config.sh" ]; then
    source deploy-config.sh
    echo -e "${GREEN}✅ Configuration loaded${NC}"
else
    echo -e "${RED}❌ deploy-config.sh not found${NC}"
    exit 1
fi

# Function to check Git status
check_git_status() {
    echo -e "${YELLOW}📋 Checking Git status...${NC}"
    
    if [ -n "$(git status --porcelain)" ]; then
        echo -e "${YELLOW}⚠️  You have uncommitted changes. Do you want to commit them? (y/n)${NC}"
        read -r response
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            echo -e "${YELLOW}💬 Enter commit message:${NC}"
            read -r commit_message
            git add .
            git commit -m "$commit_message"
            echo -e "${GREEN}✅ Changes committed${NC}"
        else
            echo -e "${YELLOW}⚠️  Proceeding with uncommitted changes${NC}"
        fi
    else
        echo -e "${GREEN}✅ Working directory is clean${NC}"
    fi
}

# Function to test local application
test_local_app() {
    echo -e "${YELLOW}🧪 Testing local application...${NC}"
    
    # Check if Laravel is working
    if php artisan --version > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Laravel is working${NC}"
    else
        echo -e "${RED}❌ Laravel is not working${NC}"
        return 1
    fi
    
    # Check if routes are working
    if php artisan route:list > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Routes are working${NC}"
    else
        echo -e "${RED}❌ Routes are not working${NC}"
        return 1
    fi
}

# Function to build assets
build_assets() {
    echo -e "${YELLOW}🔨 Building assets...${NC}"
    
    if npm run build; then
        echo -e "${GREEN}✅ Assets built successfully${NC}"
    else
        echo -e "${RED}❌ Asset build failed${NC}"
        return 1
    fi
}

# Function to run deployment
run_deployment() {
    echo -e "${YELLOW}🚀 Starting deployment...${NC}"
    
    if ./deploy.sh deploy; then
        echo -e "${GREEN}✅ Deployment completed successfully${NC}"
    else
        echo -e "${RED}❌ Deployment failed${NC}"
        return 1
    fi
}

# Function to test deployment
test_deployment() {
    echo -e "${YELLOW}🧪 Testing deployment...${NC}"
    
    if ./deploy.sh test; then
        echo -e "${GREEN}✅ Deployment test passed${NC}"
    else
        echo -e "${RED}❌ Deployment test failed${NC}"
        return 1
    fi
}

# Main workflow
main() {
    echo -e "${BLUE}Starting deployment workflow...${NC}"
    
    # Step 1: Check Git status
    check_git_status || exit 1
    
    # Step 2: Test local application
    test_local_app || exit 1
    
    # Step 3: Build assets
    build_assets || exit 1
    
    # Step 4: Run deployment
    run_deployment || exit 1
    
    # Step 5: Test deployment
    test_deployment || exit 1
    
    echo -e "${GREEN}🎉 Deployment workflow completed successfully!${NC}"
    echo -e "${BLUE}Your application is now live at: http://$REMOTE_HOST${NC}"
}

# Parse command line arguments
case "${1:-deploy}" in
    "deploy")
        main
        ;;
    "check")
        check_git_status
        test_local_app
        ;;
    "build")
        build_assets
        ;;
    "test")
        test_deployment
        ;;
    "help"|"-h"|"--help")
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  deploy          Run complete deployment workflow"
        echo "  check           Check Git status and test local app"
        echo "  build           Build assets only"
        echo "  test            Test deployment only"
        echo "  help            Show this help message"
        ;;
    *)
        echo -e "${RED}Unknown option: $1${NC}"
        echo "Use '$0 help' for usage information"
        exit 1
        ;;
esac 