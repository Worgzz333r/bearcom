#!/bin/bash
# BearCom Drupal — Deploy script
# Run on the server: cd /var/www/html && bash scripts/deploy.sh
# Or from local: ssh user@server "cd /var/www/html && bash scripts/deploy.sh"

set -e

cd /var/www/html

COMPOSE="docker compose"
DRUSH="$COMPOSE exec -T php /var/www/vendor/bin/drush"

echo "========================================="
echo "  BearCom — Deploy"
echo "========================================="

# 1. Pull latest code
echo "[1/5] Pulling latest code..."
git pull origin master

# 2. Install dependencies (skip dev packages)
echo "[2/5] Composer install..."
$COMPOSE exec -T php composer install --no-dev --no-interaction --prefer-dist --working-dir=/var/www

# 3. Run database updates
echo "[3/5] Running database updates..."
$DRUSH updb -y

# 4. Import config
echo "[4/5] Importing configuration..."
$DRUSH cim -y

# 5. Clear cache
echo "[5/5] Clearing cache..."
$DRUSH cr

echo ""
echo "========================================="
echo "  Deploy complete!"
echo "========================================="
