#!/bin/bash
# BearCom Drupal — First-time install script
# Run inside the PHP container: docker compose exec php bash scripts/install.sh

set -e

echo "========================================="
echo "  BearCom Drupal — Installation"
echo "========================================="

cd /var/www

# 1. Install Composer dependencies
echo "[1/4] Installing Composer dependencies..."
composer install --no-interaction --prefer-dist

# 2. Install Drupal
echo "[2/4] Installing Drupal..."
cd web
../vendor/bin/drush site:install standard \
    --db-url="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE}" \
    --site-name="BearCom" \
    --account-name="admin" \
    --account-pass="admin" \
    --no-interaction \
    -y

# 3. Set file permissions
echo "[3/4] Setting file permissions..."
chmod -R 775 sites/default/files
chown -R www-data:www-data sites/default/files

# 4. Enable required modules
echo "[4/4] Enabling modules..."
../vendor/bin/drush en -y \
    paragraphs \
    entity_reference_revisions \
    admin_toolbar \
    admin_toolbar_tools \
    pathauto \
    metatag \
    webform \
    search_api \
    twig_tweak \
    field_group \
    focal_point \
    geofield \
    leaflet \
    redirect \
    redis \
    media_library \
    address \
    captcha \
    recaptcha \
    better_exposed_filters \
    views_ajax_history

# Clear cache
../vendor/bin/drush cr

echo ""
echo "========================================="
echo "  Done! Site is ready at http://localhost"
echo "  Admin: admin / admin"
echo "========================================="
