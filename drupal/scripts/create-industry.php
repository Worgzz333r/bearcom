<?php

/**
 * @file
 * Creates 'industry' content type with all fields, form/view displays,
 * pathauto pattern, and industries_listing view.
 *
 * Usage: docker compose exec php ./vendor/bin/drush php:script scripts/create-industry.php
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\NodeType;
use Drupal\pathauto\Entity\PathautoPattern;

// -- Helpers (same as week2 script) --

function _ensure_storage($entity_type, $name, $type, $settings = [], $cardinality = 1) {
  if (FieldStorageConfig::loadByName($entity_type, $name)) {
    echo "  = storage $entity_type.$name already exists\n";
    return;
  }
  $config = [
    'field_name' => $name,
    'entity_type' => $entity_type,
    'type' => $type,
    'cardinality' => $cardinality,
  ];
  if ($settings) {
    $config['settings'] = $settings;
  }
  FieldStorageConfig::create($config)->save();
  echo "  + storage $entity_type.$name\n";
}

function _ensure_field($entity_type, $bundle, $name, $label, $settings = [], $required = FALSE) {
  if (FieldConfig::loadByName($entity_type, $bundle, $name)) {
    echo "  = field $bundle.$name already exists\n";
    return;
  }
  $config = [
    'field_name' => $name,
    'entity_type' => $entity_type,
    'bundle' => $bundle,
    'label' => $label,
    'required' => $required,
  ];
  if ($settings) {
    $config['settings'] = $settings;
  }
  FieldConfig::create($config)->save();
  echo "  + field $bundle.$name\n";
}

function _media_ref_settings($bundles = ['image']) {
  $target = [];
  foreach ($bundles as $b) {
    $target[$b] = $b;
  }
  return ['handler' => 'default:media', 'handler_settings' => ['target_bundles' => $target]];
}

function _paragraph_ref_settings($bundles) {
  $target = [];
  foreach ($bundles as $b) {
    $target[$b] = $b;
  }
  return ['handler' => 'default:paragraph', 'handler_settings' => ['negate' => 0, 'target_bundles' => $target]];
}

// ============================================================
// 1. Content Type: industry
// ============================================================
echo "=== 1. Content Type ===\n";

if (!NodeType::load('industry')) {
  NodeType::create([
    'type' => 'industry',
    'name' => 'Industry',
    'description' => 'Industry pages — card/teaser with icon + hero detail page.',
    'display_submitted' => FALSE,
  ])->save();

  // Remove default body field if it was auto-created.
  if ($body = FieldConfig::loadByName('node', 'industry', 'body')) {
    $body->delete();
  }
  echo "  + industry content type\n";
}
else {
  echo "  = industry content type already exists\n";
}

// ============================================================
// 2. Field Storages (node-level)
// ============================================================
echo "\n=== 2. Field Storages ===\n";

// field_icon — media reference (may already exist on paragraph entity_type but NOT on node)
_ensure_storage('node', 'field_icon', 'entity_reference', ['target_type' => 'media']);
// field_hero_image — media reference
_ensure_storage('node', 'field_hero_image', 'entity_reference', ['target_type' => 'media']);
// field_description — text_long (already exists from week2 on node)
_ensure_storage('node', 'field_description', 'text_long');
// field_solutions — paragraph reference, unlimited
_ensure_storage('node', 'field_solutions', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);
// field_cta — paragraph reference, single
_ensure_storage('node', 'field_cta', 'entity_reference_revisions', ['target_type' => 'paragraph'], 1);

// ============================================================
// 3. Attach Fields to industry
// ============================================================
echo "\n=== 3. Fields → industry ===\n";

$img = _media_ref_settings(['image']);

_ensure_field('node', 'industry', 'field_icon', 'Icon', $img, FALSE);
_ensure_field('node', 'industry', 'field_hero_image', 'Hero Image', $img, FALSE);
_ensure_field('node', 'industry', 'field_description', 'Description', [], FALSE);
_ensure_field('node', 'industry', 'field_solutions', 'Solutions', _paragraph_ref_settings(['card_grid']), FALSE);
_ensure_field('node', 'industry', 'field_cta', 'CTA', _paragraph_ref_settings(['cta_block']), FALSE);

echo "  industry: 5 custom fields + title\n";

// ============================================================
// 4. Form Display
// ============================================================
echo "\n=== 4. Form Display ===\n";

$formDisplay = \Drupal::entityTypeManager()
  ->getStorage('entity_form_display')
  ->load('node.industry.default');

if (!$formDisplay) {
  $formDisplay = \Drupal::entityTypeManager()
    ->getStorage('entity_form_display')
    ->create([
      'targetEntityType' => 'node',
      'bundle' => 'industry',
      'mode' => 'default',
      'status' => TRUE,
    ]);
}

$formDisplay->setComponent('title', [
  'type' => 'string_textfield',
  'weight' => 0,
]);
$formDisplay->setComponent('field_icon', [
  'type' => 'media_library_widget',
  'weight' => 1,
  'settings' => ['media_types' => ['image']],
]);
$formDisplay->setComponent('field_hero_image', [
  'type' => 'media_library_widget',
  'weight' => 2,
  'settings' => ['media_types' => ['image']],
]);
$formDisplay->setComponent('field_description', [
  'type' => 'text_textarea',
  'weight' => 3,
  'settings' => ['rows' => 3],
]);
$formDisplay->setComponent('field_solutions', [
  'type' => 'paragraphs',
  'weight' => 4,
  'settings' => [
    'title' => 'Solution',
    'title_plural' => 'Solutions',
    'edit_mode' => 'open',
    'add_mode' => 'dropdown',
    'form_display_mode' => 'default',
  ],
]);
$formDisplay->setComponent('field_cta', [
  'type' => 'paragraphs',
  'weight' => 5,
  'settings' => [
    'title' => 'CTA',
    'title_plural' => 'CTAs',
    'edit_mode' => 'open',
    'add_mode' => 'dropdown',
    'form_display_mode' => 'default',
  ],
]);

$formDisplay->save();
echo "  + form display configured\n";

// ============================================================
// 5. View Display — default (full)
// ============================================================
echo "\n=== 5. View Display (default) ===\n";

$viewDisplay = \Drupal::entityTypeManager()
  ->getStorage('entity_view_display')
  ->load('node.industry.default');

if (!$viewDisplay) {
  $viewDisplay = \Drupal::entityTypeManager()
    ->getStorage('entity_view_display')
    ->create([
      'targetEntityType' => 'node',
      'bundle' => 'industry',
      'mode' => 'default',
      'status' => TRUE,
    ]);
}

$viewDisplay->setComponent('field_hero_image', [
  'type' => 'entity_reference_entity_view',
  'weight' => 0,
  'label' => 'hidden',
  'settings' => ['view_mode' => 'default'],
]);
$viewDisplay->setComponent('field_description', [
  'type' => 'text_default',
  'weight' => 1,
  'label' => 'hidden',
]);
$viewDisplay->setComponent('field_solutions', [
  'type' => 'entity_reference_revisions_entity_view',
  'weight' => 2,
  'label' => 'hidden',
  'settings' => ['view_mode' => 'default'],
]);
$viewDisplay->setComponent('field_cta', [
  'type' => 'entity_reference_revisions_entity_view',
  'weight' => 3,
  'label' => 'hidden',
  'settings' => ['view_mode' => 'default'],
]);
// Hide icon from full view (it's for teaser only).
$viewDisplay->removeComponent('field_icon');

$viewDisplay->save();
echo "  + default view display configured\n";

// ============================================================
// 6. View Display — teaser
// ============================================================
echo "\n=== 6. View Display (teaser) ===\n";

$teaserDisplay = \Drupal::entityTypeManager()
  ->getStorage('entity_view_display')
  ->load('node.industry.teaser');

if (!$teaserDisplay) {
  $teaserDisplay = \Drupal::entityTypeManager()
    ->getStorage('entity_view_display')
    ->create([
      'targetEntityType' => 'node',
      'bundle' => 'industry',
      'mode' => 'teaser',
      'status' => TRUE,
    ]);
}

$teaserDisplay->setComponent('field_icon', [
  'type' => 'entity_reference_entity_view',
  'weight' => 0,
  'label' => 'hidden',
  'settings' => ['view_mode' => 'default'],
]);
$teaserDisplay->setComponent('field_description', [
  'type' => 'text_default',
  'weight' => 1,
  'label' => 'hidden',
]);
// Hide fields not needed in teaser.
$teaserDisplay->removeComponent('field_hero_image');
$teaserDisplay->removeComponent('field_solutions');
$teaserDisplay->removeComponent('field_cta');

$teaserDisplay->save();
echo "  + teaser view display configured\n";

// ============================================================
// 7. Pathauto Pattern
// ============================================================
echo "\n=== 7. Pathauto Pattern ===\n";

$patternId = 'industry_path';
$existing = PathautoPattern::load($patternId);
if (!$existing) {
  PathautoPattern::create([
    'id' => $patternId,
    'label' => 'Industry',
    'type' => 'canonical_entities:node',
    'pattern' => '/industries/[node:title]',
    'selection_criteria' => [
      [
        'id' => 'entity_bundle:node',
        'bundles' => ['industry' => 'industry'],
        'negate' => FALSE,
        'context_mapping' => ['node' => 'node'],
      ],
    ],
    'weight' => 0,
  ])->save();
  echo "  + pathauto pattern: /industries/[node:title]\n";
}
else {
  echo "  = pathauto pattern already exists\n";
}

// ============================================================
// 8. View: industries_listing
// ============================================================
echo "\n=== 8. Industries Listing View ===\n";

$viewStorage = \Drupal::entityTypeManager()->getStorage('view');
if (!$viewStorage->load('industries_listing')) {
  $view = $viewStorage->create([
    'id' => 'industries_listing',
    'label' => 'Industries Listing',
    'module' => 'views',
    'description' => 'Grid of industry nodes for /industries',
    'tag' => '',
    'base_table' => 'node_field_data',
    'base_field' => 'nid',
    'display' => [
      'default' => [
        'id' => 'default',
        'display_title' => 'Default',
        'display_plugin' => 'default',
        'position' => 0,
        'display_options' => [
          'title' => 'Industries',
          'fields' => [],
          'pager' => [
            'type' => 'full',
            'options' => [
              'items_per_page' => 9,
              'offset' => 0,
            ],
          ],
          'sorts' => [
            'title' => [
              'id' => 'title',
              'table' => 'node_field_data',
              'field' => 'title',
              'order' => 'ASC',
              'plugin_id' => 'standard',
              'entity_type' => 'node',
              'entity_field' => 'title',
            ],
          ],
          'filters' => [
            'status' => [
              'id' => 'status',
              'table' => 'node_field_data',
              'field' => 'status',
              'value' => '1',
              'plugin_id' => 'boolean',
              'group' => 1,
              'entity_type' => 'node',
              'entity_field' => 'status',
            ],
            'type' => [
              'id' => 'type',
              'table' => 'node_field_data',
              'field' => 'type',
              'value' => ['industry' => 'industry'],
              'plugin_id' => 'bundle',
              'entity_type' => 'node',
              'entity_field' => 'type',
            ],
          ],
          'style' => [
            'type' => 'default',
          ],
          'row' => [
            'type' => 'entity:node',
            'options' => [
              'view_mode' => 'teaser',
            ],
          ],
          'query' => [
            'type' => 'views_query',
          ],
          'access' => [
            'type' => 'perm',
            'options' => [
              'perm' => 'access content',
            ],
          ],
          'cache' => [
            'type' => 'tag',
          ],
          'use_ajax' => FALSE,
        ],
      ],
      'page_1' => [
        'id' => 'page_1',
        'display_title' => 'Page',
        'display_plugin' => 'page',
        'position' => 1,
        'display_options' => [
          'path' => 'industries',
        ],
      ],
    ],
  ]);
  $view->save();
  echo "  + industries_listing view at /industries\n";
}
else {
  echo "  = industries_listing view already exists\n";
}

echo "\n=== All done! ===\n";
echo "Run: docker compose exec php ./vendor/bin/drush cex -y\n";
