<?php

use Drupal\Core\Config\FileStorage;

$source = new FileStorage('/var/www/config/sync');
$config_storage = \Drupal::service('config.storage');

$configs = [
  'paragraphs.paragraphs_type.hero_banner',
  'paragraphs.paragraphs_type.cta_block',
  'field.storage.paragraph.field_style',
  'field.storage.paragraph.field_title',
  'field.storage.paragraph.field_subtitle',
  'field.storage.paragraph.field_image',
  'field.storage.paragraph.field_cta_text',
  'field.storage.paragraph.field_cta_url',
  'field.storage.paragraph.field_product_image',
  'field.storage.paragraph.field_button_text',
  'field.storage.paragraph.field_button_url',
  'field.storage.paragraph.field_description',
  'field.field.paragraph.hero_banner.field_style',
  'field.field.paragraph.hero_banner.field_title',
  'field.field.paragraph.hero_banner.field_subtitle',
  'field.field.paragraph.hero_banner.field_image',
  'field.field.paragraph.hero_banner.field_cta_text',
  'field.field.paragraph.hero_banner.field_cta_url',
  'field.field.paragraph.hero_banner.field_product_image',
  'field.field.paragraph.cta_block.field_style',
  'field.field.paragraph.cta_block.field_title',
  'field.field.paragraph.cta_block.field_description',
  'field.field.paragraph.cta_block.field_image',
  'field.field.paragraph.cta_block.field_button_text',
  'field.field.paragraph.cta_block.field_button_url',
  'core.entity_form_display.paragraph.hero_banner.default',
  'core.entity_view_display.paragraph.hero_banner.default',
  'core.entity_form_display.paragraph.cta_block.default',
  'core.entity_view_display.paragraph.cta_block.default',
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

echo "\nDone! Run drush cr to apply.\n";
