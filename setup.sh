#!/bin/bash

# Shortzz Backend Setup Script
# Run this script after Docker containers are up

set -e

echo "=== Shortzz Backend Setup ==="

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until docker exec shortzz-db mysqladmin ping -h localhost --silent; do
    sleep 2
done
echo "MySQL is ready!"

# Run composer install
echo "Installing PHP dependencies..."
docker exec shortzz-app composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key
echo "Generating application key..."
docker exec shortzz-app php artisan key:generate --force

# Run database migrations (if needed)
echo "Running database migrations..."
docker exec shortzz-app php artisan migrate --force

# Cache configurations
echo "Caching configurations..."
docker exec shortzz-app php artisan config:cache
docker exec shortzz-app php artisan route:cache
docker exec shortzz-app php artisan view:cache

# Create storage link
echo "Creating storage link..."
docker exec shortzz-app php artisan storage:link

# Set permissions
echo "Setting permissions..."
docker exec shortzz-app chmod -R 775 storage bootstrap/cache

echo "=== Setup Complete! ==="
echo "Backend is running at: http://localhost:8000"
echo "API endpoint: http://localhost:8000/api/"
