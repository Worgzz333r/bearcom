#!/bin/bash
# BearCom Drupal — First-time install script
# Run: docker compose exec php bash /var/www/scripts/install.sh

set -e

echo "========================================="
echo "  BearCom Drupal — Installation"
echo "========================================="

cd /var/www

# 1. Install Composer dependencies
echo "[1/7] Installing Composer dependencies..."
COMPOSER_PROCESS_TIMEOUT=600 composer install --no-interaction --prefer-dist

# 2. Install Drupal
echo "[2/7] Installing Drupal..."
cd web
../vendor/bin/drush site:install standard \
    --db-url="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE}" \
    --site-name="BearCom" \
    --account-name="admin" \
    --account-pass="admin" \
    --no-interaction \
    -y

# 3. Set file permissions
echo "[3/7] Setting file permissions..."
chmod -R 775 sites/default/files
chown -R www-data:www-data sites/default/files
chmod 644 sites/default/settings.php

# 4. Configure settings.php
echo "[4/7] Configuring settings.php..."
php -r '
$f = "sites/default/settings.php";
$c = file_get_contents($f);
$c .= "\n";
$c .= "\$settings[\"config_sync_directory\"] = \"../config/sync\";\n";
file_put_contents($f, $c);
echo "  Config sync directory set.\n";
'

# 5. Import config from git
echo "[5/7] Importing configuration..."

../vendor/bin/drush cr
if [ -f "../config/sync/system.site.yml" ]; then
    # Match site UUID to config so drush cim doesn't reject the import
    CONFIG_UUID=$(grep "^uuid:" ../config/sync/system.site.yml | awk '{print $2}')
    ../vendor/bin/drush config:set system.site uuid "$CONFIG_UUID" -y

    # Remove default shortcuts that block config import
    ../vendor/bin/drush ev '\Drupal::entityTypeManager()->getStorage("shortcut")->delete(\Drupal::entityTypeManager()->getStorage("shortcut")->loadMultiple());'

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
echo "[6/7] Setting up themes..."
../vendor/bin/drush theme:enable bearcom -y
../vendor/bin/drush config:set system.theme default bearcom -y
../vendor/bin/drush config:set system.theme admin claro -y
../vendor/bin/drush config:set node.settings use_admin_theme 1 -y

# 7. Now enable Redis cache backend (modules are available after cim)
echo "[7/7] Enabling Redis cache..."
php -r '
$f = "sites/default/settings.php";
$c = file_get_contents($f);
$c .= "\n";
$c .= "// Redis cache backend.\n";
$c .= "\$settings[\"redis.connection\"][\"interface\"] = \"PhpRedis\";\n";
$c .= "\$settings[\"redis.connection\"][\"host\"] = \"redis\";\n";
$c .= "\$settings[\"redis.connection\"][\"port\"] = \"6379\";\n";
$c .= "\$settings[\"cache\"][\"default\"] = \"cache.backend.redis\";\n";
$c .= "\$settings[\"container_yamls\"][] = \"modules/contrib/redis/example.services.yml\";\n";
file_put_contents($f, $c);
echo "  Redis enabled.\n";
'

# Clear cache
../vendor/bin/drush cr

echo ""
echo "========================================="
echo "  Done! Site is ready."
echo "  Admin: admin / admin (change immediately!)"
echo ""
echo "  Next steps:"
echo "  1. bash /var/www/scripts/create-menu.sh"
echo "  2. drush user:password admin 'new_password'"
echo "========================================="
