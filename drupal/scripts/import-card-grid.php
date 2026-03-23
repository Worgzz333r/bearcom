<?php

use Drupal\Core\Config\FileStorage;

$source = new FileStorage('/var/www/config/sync');
$config_storage = \Drupal::service('config.storage');

$configs = [
  'paragraphs.paragraphs_type.card_grid',
  'paragraphs.paragraphs_type.card_item',
  'field.storage.paragraph.field_cards',
  'field.storage.paragraph.field_link',
  'field.field.paragraph.card_grid.field_title',
  'field.field.paragraph.card_grid.field_subtitle',
  'field.field.paragraph.card_grid.field_cards',
  'field.field.paragraph.card_item.field_title',
  'field.field.paragraph.card_item.field_description',
  'field.field.paragraph.card_item.field_icon',
  'field.field.paragraph.card_item.field_link',
  'core.entity_form_display.paragraph.card_grid.default',
  'core.entity_view_display.paragraph.card_grid.default',
  'core.entity_form_display.paragraph.card_item.default',
  'core.entity_view_display.paragraph.card_item.default',
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

// Install field tables
$manager = \Drupal::entityDefinitionUpdateManager();
$fields_to_install = ['field_cards', 'field_link'];
foreach ($fields_to_install as $fname) {
  $storage_def = \Drupal\field\Entity\FieldStorageConfig::loadByName('paragraph', $fname);
  if ($storage_def) {
    try {
      $manager->installFieldStorageDefinition($fname, 'paragraph', 'paragraph', $storage_def);
      echo "Installed table: $fname\n";
    } catch (\Exception $e) {
      echo "Skip $fname: " . $e->getMessage() . "\n";
    }
  }
}

echo "Done!\n";
