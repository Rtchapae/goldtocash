#!/bin/bash
set -e

echo "Starting database import..."

# Проверяем, существует ли файл базы данных
if [ -f "/docker-entrypoint-initdb.d/gold2cash.sql.gz" ]; then
    echo "Found gold2cash.sql.gz, starting import..."
    gzip -dc /docker-entrypoint-initdb.d/gold2cash.sql.gz | mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"
    echo "Database import completed successfully!"
else
    echo "Warning: gold2cash.sql.gz not found in /docker-entrypoint-initdb.d/"
fi
