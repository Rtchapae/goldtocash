#!/bin/bash

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Fix permissions for storage directory (skip chown as files are mounted from host)
chmod -R 775 /var/www/html/storage/ 2>/dev/null || true

# Fix permissions for public storage directory (ignore if doesn't exist)
chmod -R 775 /var/www/html/public/storage 2>/dev/null || true

echo "Permissions fixed successfully"
