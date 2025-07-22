#!/bin/bash

# Fastify Git Update Script
# This script automates the process of updating your GitHub repository

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Fastify Git Update Script${NC}"
echo "=================================="

# Check if there are any changes
if [ -z "$(git status --porcelain)" ]; then
    echo -e "${YELLOW}📋 No changes to commit${NC}"
    exit 0
fi

# Show current status
echo -e "${YELLOW}📋 Current Git Status:${NC}"
git status --short

# Ask for commit message
echo -e "${YELLOW}💬 Enter commit message (or press Enter for default):${NC}"
read -r commit_message

# Use default message if none provided
if [ -z "$commit_message" ]; then
    commit_message="Update Fastify application - $(date '+%Y-%m-%d %H:%M:%S')"
fi

# Add all changes
echo -e "${YELLOW}📦 Adding changes...${NC}"
git add .

# Commit changes
echo -e "${YELLOW}💾 Committing changes...${NC}"
git commit -m "$commit_message"

# Push to GitHub
echo -e "${YELLOW}🚀 Pushing to GitHub...${NC}"
git push origin main

# Check if push was successful
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Successfully updated GitHub repository!${NC}"
    echo -e "${BLUE}🌐 View your repository: https://github.com/amithyone/fastify-food-app${NC}"
else
    echo -e "${RED}❌ Failed to push to GitHub${NC}"
    exit 1
fi 