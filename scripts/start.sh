#!/bin/bash
# BearCom — Quick start script
# Run from project root: bash scripts/start.sh

set -e

# Copy .env if not exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    echo "⚠  Edit .env with your settings before production use!"
fi

# Build and start containers
echo "Starting Docker containers..."
docker compose up -d --build

echo ""
echo "Waiting for database to be ready..."
sleep 5

# Check if Drupal is already installed
if docker compose exec php test -f /var/www/web/sites/default/settings.php 2>/dev/null; then
    echo "Drupal config found. Running cache rebuild..."
    docker compose exec php bash -c "cd /var/www/web && ../vendor/bin/drush cr" 2>/dev/null || true
else
    echo "First run detected. Run install script:"
    echo "  docker compose exec php bash scripts/install.sh"
fi

echo ""
echo "========================================="
echo "  BearCom is running!"
echo "  Site:    http://localhost"
echo "  Mail:    http://localhost:8025"
echo "========================================="
