#!/bin/bash

# Clear existing cache
php artisan optimize:clear

# Cache configuration for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations automatically
php artisan migrate --force

# Start the Apache server
apache2-foreground