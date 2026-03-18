#!/bin/bash
# BearCom Drupal — First-time install script
# Run inside the PHP container: docker compose exec php bash scripts/install.sh

set -e

echo "========================================="
echo "  BearCom Drupal — Installation"
echo "========================================="

cd /var/www

# 1. Install Composer dependencies
echo "[1/6] Installing Composer dependencies..."
COMPOSER_PROCESS_TIMEOUT=600 composer install --no-interaction --prefer-dist

# 2. Install Drupal
echo "[2/6] Installing Drupal..."
cd web
../vendor/bin/drush site:install standard \
    --db-url="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE}" \
    --site-name="BearCom" \
    --account-name="admin" \
    --account-pass="admin" \
    --no-interaction \
    -y

# 3. Set file permissions
echo "[3/6] Setting file permissions..."
chmod -R 775 sites/default/files
chown -R www-data:www-data sites/default/files
chmod 644 sites/default/settings.php

# 4. Configure settings.php (config sync + Redis)
echo "[4/6] Configuring settings.php..."
php -r '
$f = "sites/default/settings.php";
$c = file_get_contents($f);
$c .= "\n";
$c .= "\$settings[\"config_sync_directory\"] = \"../config/sync\";\n";
$c .= "\n";
$c .= "// Redis cache backend.\n";
$c .= "\$settings[\"redis.connection\"][\"host\"] = \"redis\";\n";
$c .= "\$settings[\"redis.connection\"][\"port\"] = \"6379\";\n";
$c .= "\$settings[\"cache\"][\"default\"] = \"cache.backend.redis\";\n";
$c .= "\$settings[\"container_yamls\"][] = \"modules/contrib/redis/example.services.yml\";\n";
file_put_contents($f, $c);
echo "  settings.php updated.\n";
'

# 5. Import config from git (if exists)
echo "[5/6] Importing configuration..."
../vendor/bin/drush cr
if [ -f "../config/sync/core.extension.yml" ]; then
    ../vendor/bin/drush cim -y
    echo "  Config imported."
else
    echo "  No config found, enabling modules manually..."
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
fi

# 6. Set themes
echo "[6/6] Setting up themes..."
../vendor/bin/drush theme:enable bearcom -y
../vendor/bin/drush config:set system.theme default bearcom -y
../vendor/bin/drush config:set system.theme admin claro -y
../vendor/bin/drush config:set node.settings use_admin_theme 1 -y

# Clear cache
../vendor/bin/drush cr

echo ""
echo "========================================="
echo "  Done! Site is ready at http://localhost"
echo "  Admin: admin / admin"
echo ""
echo "  Next: bash scripts/create-menu.sh"
echo "========================================="
