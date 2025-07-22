#!/bin/bash

# Fastify Deployment Configuration
# Update these values with your server information

# Server Configuration
REMOTE_HOST="66.29.153.202"
REMOTE_USER="bubbeduc"
REMOTE_PATH="/var/www/html/fastify"

# Database Configuration (if needed for backup)
DB_NAME="fastify_db"
DB_USER="fastify_user"
DB_PASSWORD="your-db-password"

# Application Configuration
APP_ENV="production"
APP_DEBUG="false"

# Backup Configuration
BACKUP_RETENTION_DAYS=7
BACKUP_PATH="/var/backups/fastify"

# SSH Configuration
SSH_KEY_PATH="~/.ssh/id_rsa"  # Path to your SSH private key
SSH_PORT="22"                 # SSH port (usually 22)

# Notification Configuration (optional)
SLACK_WEBHOOK_URL=""          # Slack webhook for deployment notifications
EMAIL_NOTIFICATIONS="false"   # Enable email notifications

# Advanced Configuration
MAINTENANCE_MODE="false"      # Enable maintenance mode during deployment
ROLLBACK_ENABLED="true"       # Enable automatic rollback on failure
HEALTH_CHECK_URL="/health"    # Health check endpoint

# Export variables for use in deploy.sh
export REMOTE_HOST
export REMOTE_USER
export REMOTE_PATH
export DB_NAME
export DB_USER
export DB_PASSWORD
export APP_ENV
export APP_DEBUG
export BACKUP_RETENTION_DAYS
export BACKUP_PATH
export SSH_KEY_PATH
export SSH_PORT
export SLACK_WEBHOOK_URL
export EMAIL_NOTIFICATIONS
export MAINTENANCE_MODE
export ROLLBACK_ENABLED
export HEALTH_CHECK_URL 