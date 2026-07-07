# Shortzz Backend Setup Script (Windows PowerShell)
# Run this script after Docker containers are up

$ErrorActionPreference = "Stop"

Write-Host "=== Shortzz Backend Setup ===" -ForegroundColor Cyan

# Wait for MySQL to be ready
Write-Host "Waiting for MySQL to be ready..." -ForegroundColor Yellow
$maxAttempts = 30
$attempt = 0
do {
    $attempt++
    try {
        $result = docker exec shortzz-db mysqladmin ping -h localhost --silent 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "MySQL is ready!" -ForegroundColor Green
            break
        }
    } catch {}
    Write-Host "  Attempt $attempt/$maxAttempts - waiting 3 seconds..."
    Start-Sleep -Seconds 3
} while ($attempt -lt $maxAttempts)

if ($attempt -ge $maxAttempts) {
    Write-Host "ERROR: MySQL did not become ready in time" -ForegroundColor Red
    exit 1
}

# Run composer install
Write-Host "Installing PHP dependencies..." -ForegroundColor Yellow
docker exec shortzz-app composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key
Write-Host "Generating application key..." -ForegroundColor Yellow
docker exec shortzz-app php artisan key:generate --force

# Cache configurations
Write-Host "Caching configurations..." -ForegroundColor Yellow
docker exec shortzz-app php artisan config:cache
docker exec shortzz-app php artisan route:cache
docker exec shortzz-app php artisan view:cache

# Create storage link
Write-Host "Creating storage link..." -ForegroundColor Yellow
docker exec shortzz-app php artisan storage:link

# Set permissions
Write-Host "Setting permissions..." -ForegroundColor Yellow
docker exec shortzz-app chmod -R 775 storage bootstrap/cache

Write-Host ""
Write-Host "=== Setup Complete! ===" -ForegroundColor Green
Write-Host "Backend is running at: http://localhost:8000" -ForegroundColor Cyan
Write-Host "API endpoint: http://localhost:8000/api/" -ForegroundColor Cyan
