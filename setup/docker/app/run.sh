#!/bin/bash

# Install Composer packages
composer install --no-interaction --no-progress

# Setup
composer run setup

# Debug (watching changes)
php /var/www/artisan octane:start --no-interaction --host=0.0.0.0 --port=8000 --watch
# Production (speed optimized)
#php /var/www/artisan octane:start --no-interaction --host=0.0.0.0 --port=8000
