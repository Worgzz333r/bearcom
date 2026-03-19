<?php

use Drupal\Core\Config\FileStorage;

$source = new FileStorage('/var/www/config/sync');
$config_storage = \Drupal::service('config.storage');

$configs = [
  'node.type.flexible_page',
  'field.storage.node.field_paragraphs',
  'field.storage.node.field_heading',
  'field.storage.node.field_description',
  'field.storage.node.field_hero_style',
  'field.storage.node.field_benefits',
  'field.storage.node.field_webform',
  'field.field.node.flexible_page.field_paragraphs',
  'field.field.node.flexible_page.field_heading',
  'field.field.node.flexible_page.field_description',
  'field.field.node.flexible_page.field_hero_style',
  'field.field.node.flexible_page.field_image',
  'field.field.node.flexible_page.field_benefits',
  'field.field.node.flexible_page.field_webform',
  'core.entity_form_display.node.flexible_page.default',
  'core.entity_view_display.node.flexible_page.default',
];

foreach ($configs as $name) {
  $data = $source->read($name);
  if ($data) {
    $config_storage->write($name, $data);
    echo "Imported: $name\n";
  } else {
    echo "NOT FOUND: $name\n";
  }
}

echo "\nDone!\n";
