<?php
// drush php:script import_menu_content.php

use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\file\Entity\File;

$json_path = dirname(__DIR__) . '/menu_export.json';
if (!file_exists($json_path)) {
  echo "menu_export.json not found!\n";
  return;
}

$data = json_decode(file_get_contents($json_path), TRUE);
echo "Found " . count($data) . " menu links.\n";

// Clear existing
foreach (['main', 'mega-menu-sidebar'] as $menu_name) {
  $existing = \Drupal::entityTypeManager()->getStorage('menu_link_content')
    ->loadByProperties(['menu_name' => $menu_name]);
  foreach ($existing as $link) {
    $link->delete();
  }
  echo "Cleared $menu_name.\n";
}

// Build old UUID -> new UUID map
$uuid_map = [];

// Sort by depth: items without parent first
usort($data, function($a, $b) {
  return (empty($a['parent']) ? 0 : 1) - (empty($b['parent']) ? 0 : 1);
});

$pending = $data;
$max_passes = 5;

for ($pass = 0; $pass < $max_passes && !empty($pending); $pass++) {
  $next_pending = [];
  foreach ($pending as $item) {
    $parent_value = '';
    if (!empty($item['parent'])) {
      $old_parent_uuid = str_replace('menu_link_content:', '', $item['parent']);
      if (isset($uuid_map[$old_parent_uuid])) {
        $parent_value = 'menu_link_content:' . $uuid_map[$old_parent_uuid];
      } else {
        $next_pending[] = $item;
        continue;
      }
    }

    $values = [
      'title' => $item['title'],
      'link' => ['uri' => $item['link_uri']],
      'menu_name' => $item['menu_name'],
      'weight' => $item['weight'],
      'expanded' => $item['expanded'],
      'enabled' => $item['enabled'],
      'parent' => $parent_value,
      'description' => $item['description'],
    ];

    if (isset($item['field_full_width'])) $values['field_full_width'] = $item['field_full_width'];
    if (isset($item['field_sidebar_style'])) $values['field_sidebar_style'] = $item['field_sidebar_style'];

    if (!empty($item['icon_uri'])) {
      $file = _import_get_file($item['icon_uri']);
      if ($file) $values['field_icon'] = ['target_id' => $file->id()];
    }

    if (!empty($item['image_uri'])) {
      $file = _import_get_file($item['image_uri']);
      if ($file) $values['field_image'] = ['target_id' => $file->id(), 'alt' => $item['title']];
    }

    $link = MenuLinkContent::create($values);
    $link->save();

    $uuid_map[$item['uuid']] = $link->uuid();
    echo "  {$item['title']}\n";
  }
  $pending = $next_pending;
}

if (!empty($pending)) {
  echo "WARNING: " . count($pending) . " items skipped (missing parents).\n";
}

echo "\n--- Done: imported " . count($uuid_map) . " links ---\n";

function _import_get_file($uri) {
  $path = \Drupal::service('file_system')->realpath($uri);
  if (!$path || !file_exists($path)) return NULL;

  $existing = \Drupal::entityTypeManager()->getStorage('file')
    ->loadByProperties(['uri' => $uri]);
  if ($existing) return reset($existing);

  $file = File::create(['uri' => $uri, 'status' => 1]);
  $file->save();
  return $file;
}
