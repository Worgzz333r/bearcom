<?php

/**
 * Add 'soundwave' option to field_style allowed values.
 * Run: drush php-script scripts/add-soundwave-style.php
 */

$storage = \Drupal\field\Entity\FieldStorageConfig::loadByName('paragraph', 'field_style');
if ($storage) {
  $values = $storage->getSetting('allowed_values');
  if (!isset($values['soundwave'])) {
    $values['soundwave'] = 'Soundwave (grey bg, wave illustration)';
    $storage->setSetting('allowed_values', $values);
    $storage->save();
    echo "Added 'soundwave' to field_style allowed values.\n";
  } else {
    echo "'soundwave' already exists in field_style.\n";
  }
} else {
  echo "field_style storage not found.\n";
}
