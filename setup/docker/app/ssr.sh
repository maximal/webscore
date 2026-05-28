#!/bin/bash

# Setup
bun run build:ssr

php /var/www/artisan inertia:start-ssr --runtime=bun
