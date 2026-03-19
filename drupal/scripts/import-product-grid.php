<?php

use Drupal\Core\Config\FileStorage;

$source = new FileStorage('/var/www/config/sync');
$config_storage = \Drupal::service('config.storage');

$configs = [
  'paragraphs.paragraphs_type.product_grid',
  'field.storage.paragraph.field_view_id',
  'field.storage.paragraph.field_limit',
  'field.field.paragraph.product_grid.field_title',
  'field.field.paragraph.product_grid.field_view_id',
  'field.field.paragraph.product_grid.field_limit',
  'core.entity_form_display.paragraph.product_grid.default',
  'core.entity_view_display.paragraph.product_grid.default',
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

echo "Done!\n";
