#!/bin/bash
# BearCom Drupal — Deploy script
# Run on the server: cd /var/www/html && bash scripts/deploy.sh
# Or from local: ssh user@server "cd /var/www/html && bash scripts/deploy.sh"
#
# bearcom_sync module auto-imports structure (taxonomies, menus, blocks) on drush cim

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

# 4. Import config + structure (taxonomies, menus, blocks auto-imported by bearcom_sync)
echo "[4/5] Importing configuration + structure..."
$DRUSH cim -y

# 5. Run custom post-import scripts
echo "[5/6] Running post-import scripts..."
for script in /var/www/web/scripts/post-deploy/*.php; do
  [ -f "$script" ] && $DRUSH php-script "$script" && echo "  Executed: $(basename $script)"
done

# 6. Clear cache
echo "[6/6] Clearing cache..."
$DRUSH cr

echo ""
echo "========================================="
echo "  Deploy complete!"
echo "========================================="
