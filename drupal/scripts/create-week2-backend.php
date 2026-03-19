<?php

/**
 * @file
 * Week 2: Creates paragraph types + flexible_page CT with all fields.
 *
 * Usage: docker compose exec php ./vendor/bin/drush scr /var/www/scripts/create-week2-backend.php
 */

use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\NodeType;

// -- Helpers --

function _create_storage($entity_type, $name, $type, $settings = [], $cardinality = 1) {
  if (FieldStorageConfig::loadByName($entity_type, $name)) {
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
}

function _create_field($entity_type, $bundle, $name, $label, $settings = [], $required = FALSE) {
  if (FieldConfig::loadByName($entity_type, $bundle, $name)) {
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
}

function _media_settings($bundles = ['image']) {
  $target = [];
  foreach ($bundles as $b) {
    $target[$b] = $b;
  }
  return ['handler' => 'default:media', 'handler_settings' => ['target_bundles' => $target]];
}

function _paragraph_settings($bundles) {
  $target = [];
  foreach ($bundles as $b) {
    $target[$b] = $b;
  }
  return ['handler' => 'default:paragraph', 'handler_settings' => ['negate' => 0, 'target_bundles' => $target]];
}

// ============================================================
// PHASE 1: Paragraph Types
// ============================================================
echo "=== Phase 1: Paragraph Types ===\n";

$types = [
  'hero_banner'        => 'Hero Banner',
  'cta_block'          => 'CTA Block',
  'card_grid'          => 'Card Grid',
  'card_item'          => 'Card Item',
  'stats_counter'      => 'Stats Counter',
  'stat_item'          => 'Stat Item',
  'guided_journey'     => 'Guided Journey',
  'gj_tab'             => 'GJ Tab',
  'rentals_connected'  => 'Rentals Connected',
  'content_block'      => 'Content Block',
  'product_grid'       => 'Product Grid',
  'video_block'        => 'Video Block',
  'checklist_item'     => 'Checklist Item',
  'faq_item'           => 'FAQ Item',
];

foreach ($types as $id => $label) {
  if (!ParagraphsType::load($id)) {
    ParagraphsType::create(['id' => $id, 'label' => $label])->save();
    echo "  + $label\n";
  } else {
    echo "  = $label\n";
  }
}

// ============================================================
// PHASE 2: Field Storages — paragraph
// ============================================================
echo "\n=== Phase 2: Field Storages (paragraph) ===\n";

// Text fields
_create_storage('paragraph', 'field_title', 'string');
_create_storage('paragraph', 'field_subtitle', 'string');
_create_storage('paragraph', 'field_description', 'text_long');
_create_storage('paragraph', 'field_body', 'text_long');
_create_storage('paragraph', 'field_text', 'text_long');
_create_storage('paragraph', 'field_question', 'string');
_create_storage('paragraph', 'field_answer', 'text_long');
_create_storage('paragraph', 'field_number', 'string');
_create_storage('paragraph', 'field_label', 'string');
_create_storage('paragraph', 'field_tab_title', 'string');
_create_storage('paragraph', 'field_cta_text', 'string');
_create_storage('paragraph', 'field_button_text', 'string');
_create_storage('paragraph', 'field_view_id', 'string');
_create_storage('paragraph', 'field_card_1_title', 'string');
_create_storage('paragraph', 'field_card_2_title', 'string');

// Multi-value text
_create_storage('paragraph', 'field_checklist', 'string', [], -1);

// Integer
_create_storage('paragraph', 'field_limit', 'integer');

// Links
_create_storage('paragraph', 'field_cta_url', 'link');
_create_storage('paragraph', 'field_button_url', 'link');
_create_storage('paragraph', 'field_link', 'link');
_create_storage('paragraph', 'field_card_1_link', 'link');
_create_storage('paragraph', 'field_card_2_link', 'link');

// Media references
_create_storage('paragraph', 'field_image', 'entity_reference', ['target_type' => 'media']);
_create_storage('paragraph', 'field_icon', 'entity_reference', ['target_type' => 'media']);
_create_storage('paragraph', 'field_product_image', 'entity_reference', ['target_type' => 'media']);
_create_storage('paragraph', 'field_video', 'entity_reference', ['target_type' => 'media']);
_create_storage('paragraph', 'field_card_1_image', 'entity_reference', ['target_type' => 'media']);
_create_storage('paragraph', 'field_card_2_image', 'entity_reference', ['target_type' => 'media']);

// Select lists
_create_storage('paragraph', 'field_style', 'list_string', [
  'allowed_values' => [
    'product' => 'Product',
    'image' => 'Image',
    'color' => 'Solid Color',
    'default' => 'Default',
    'orange' => 'Orange',
  ],
]);
_create_storage('paragraph', 'field_layout', 'list_string', [
  'allowed_values' => [
    'image-left' => 'Image Left',
    'image-right' => 'Image Right',
  ],
]);

// Paragraph references (unlimited)
_create_storage('paragraph', 'field_cards', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);
_create_storage('paragraph', 'field_items', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);
_create_storage('paragraph', 'field_tabs', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);

echo "  Field storages created.\n";

// ============================================================
// PHASE 3: Attach fields to paragraph types
// ============================================================
echo "\n=== Phase 3: Fields → Paragraphs ===\n";

$img = _media_settings(['image']);

// hero_banner
_create_field('paragraph', 'hero_banner', 'field_title', 'Title', [], TRUE);
_create_field('paragraph', 'hero_banner', 'field_subtitle', 'Subtitle');
_create_field('paragraph', 'hero_banner', 'field_image', 'Background Image', $img);
_create_field('paragraph', 'hero_banner', 'field_product_image', 'Product Image', $img);
_create_field('paragraph', 'hero_banner', 'field_cta_text', 'CTA Text');
_create_field('paragraph', 'hero_banner', 'field_cta_url', 'CTA URL');
_create_field('paragraph', 'hero_banner', 'field_style', 'Style');
echo "  + hero_banner (7 fields)\n";

// cta_block
_create_field('paragraph', 'cta_block', 'field_image', 'Image', $img);
_create_field('paragraph', 'cta_block', 'field_title', 'Title', [], TRUE);
_create_field('paragraph', 'cta_block', 'field_description', 'Description');
_create_field('paragraph', 'cta_block', 'field_button_text', 'Button Text');
_create_field('paragraph', 'cta_block', 'field_button_url', 'Button URL');
_create_field('paragraph', 'cta_block', 'field_style', 'Style');
echo "  + cta_block (6 fields)\n";

// card_grid
_create_field('paragraph', 'card_grid', 'field_title', 'Title');
_create_field('paragraph', 'card_grid', 'field_subtitle', 'Subtitle');
_create_field('paragraph', 'card_grid', 'field_cards', 'Cards', _paragraph_settings(['card_item']));
echo "  + card_grid (3 fields)\n";

// card_item
_create_field('paragraph', 'card_item', 'field_icon', 'Icon', $img);
_create_field('paragraph', 'card_item', 'field_title', 'Title', [], TRUE);
_create_field('paragraph', 'card_item', 'field_description', 'Description');
_create_field('paragraph', 'card_item', 'field_link', 'Link');
echo "  + card_item (4 fields)\n";

// stats_counter
_create_field('paragraph', 'stats_counter', 'field_items', 'Items', _paragraph_settings(['stat_item']));
echo "  + stats_counter (1 field)\n";

// stat_item
_create_field('paragraph', 'stat_item', 'field_number', 'Number', [], TRUE);
_create_field('paragraph', 'stat_item', 'field_label', 'Label', [], TRUE);
echo "  + stat_item (2 fields)\n";

// guided_journey
_create_field('paragraph', 'guided_journey', 'field_title', 'Title');
_create_field('paragraph', 'guided_journey', 'field_tabs', 'Tabs', _paragraph_settings(['gj_tab']));
echo "  + guided_journey (2 fields)\n";

// gj_tab
_create_field('paragraph', 'gj_tab', 'field_tab_title', 'Tab Title', [], TRUE);
_create_field('paragraph', 'gj_tab', 'field_body', 'Body');
_create_field('paragraph', 'gj_tab', 'field_checklist', 'Checklist Items');
_create_field('paragraph', 'gj_tab', 'field_image', 'Image', $img);
echo "  + gj_tab (4 fields)\n";

// rentals_connected
_create_field('paragraph', 'rentals_connected', 'field_title', 'Title');
_create_field('paragraph', 'rentals_connected', 'field_card_1_image', 'Card 1 Image', $img);
_create_field('paragraph', 'rentals_connected', 'field_card_1_title', 'Card 1 Title');
_create_field('paragraph', 'rentals_connected', 'field_card_1_link', 'Card 1 Link');
_create_field('paragraph', 'rentals_connected', 'field_card_2_image', 'Card 2 Image', $img);
_create_field('paragraph', 'rentals_connected', 'field_card_2_title', 'Card 2 Title');
_create_field('paragraph', 'rentals_connected', 'field_card_2_link', 'Card 2 Link');
echo "  + rentals_connected (7 fields)\n";

// content_block (shared — used by Dev 1 and Dev 2)
_create_field('paragraph', 'content_block', 'field_title', 'Title');
_create_field('paragraph', 'content_block', 'field_body', 'Body');
_create_field('paragraph', 'content_block', 'field_image', 'Image', $img);
_create_field('paragraph', 'content_block', 'field_layout', 'Layout');
echo "  + content_block (4 fields)\n";

// product_grid
_create_field('paragraph', 'product_grid', 'field_title', 'Title');
_create_field('paragraph', 'product_grid', 'field_view_id', 'View Machine Name');
_create_field('paragraph', 'product_grid', 'field_limit', 'Max Items');
echo "  + product_grid (3 fields)\n";

// video_block
_create_field('paragraph', 'video_block', 'field_title', 'Title');
_create_field('paragraph', 'video_block', 'field_video', 'Video', _media_settings(['remote_video']));
echo "  + video_block (2 fields)\n";

// checklist_item
_create_field('paragraph', 'checklist_item', 'field_text', 'Text');
echo "  + checklist_item (1 field)\n";

// faq_item (shared — used by Dev 1 and Dev 2)
_create_field('paragraph', 'faq_item', 'field_question', 'Question', [], TRUE);
_create_field('paragraph', 'faq_item', 'field_answer', 'Answer');
echo "  + faq_item (2 fields)\n";

// ============================================================
// PHASE 4: Content Type — flexible_page
// ============================================================
echo "\n=== Phase 4: Content Type ===\n";

if (!NodeType::load('flexible_page')) {
  NodeType::create([
    'type' => 'flexible_page',
    'name' => 'Flexible Page',
    'description' => 'For Homepage, Location Parent, Contact Us — each page uses the fields it needs.',
    'display_submitted' => FALSE,
  ])->save();

  // Disable default body field (we use paragraphs instead).
  if ($body = FieldConfig::loadByName('node', 'flexible_page', 'body')) {
    $body->delete();
  }

  echo "  + flexible_page\n";
} else {
  echo "  = flexible_page\n";
}

// ============================================================
// PHASE 5: Field Storages — node
// ============================================================
echo "\n=== Phase 5: Field Storages (node) ===\n";

_create_storage('node', 'field_paragraphs', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);
_create_storage('node', 'field_heading', 'string');
_create_storage('node', 'field_description', 'text_long');
_create_storage('node', 'field_benefits', 'entity_reference_revisions', ['target_type' => 'paragraph'], -1);
_create_storage('node', 'field_image', 'entity_reference', ['target_type' => 'media']);
_create_storage('node', 'field_webform', 'webform');
_create_storage('node', 'field_hero_style', 'list_string', [
  'allowed_values' => [
    'product' => 'Product',
    'image' => 'Image',
    'color' => 'Solid Color',
  ],
]);

echo "  Field storages created.\n";

// ============================================================
// PHASE 6: Attach fields to flexible_page
// ============================================================
echo "\n=== Phase 6: Fields → flexible_page ===\n";

_create_field('node', 'flexible_page', 'field_paragraphs', 'Content', _paragraph_settings([
  'hero_banner', 'cta_block', 'card_grid', 'stats_counter',
  'rentals_connected', 'guided_journey', 'content_block',
  'product_grid', 'video_block',
]));
_create_field('node', 'flexible_page', 'field_heading', 'Heading');
_create_field('node', 'flexible_page', 'field_description', 'Description');
_create_field('node', 'flexible_page', 'field_benefits', 'Benefits', _paragraph_settings(['checklist_item']));
_create_field('node', 'flexible_page', 'field_image', 'Image', _media_settings(['image']));
_create_field('node', 'flexible_page', 'field_webform', 'Webform');
_create_field('node', 'flexible_page', 'field_hero_style', 'Hero Style');

echo "  + flexible_page (7 fields)\n";

echo "\n=== Done! ===\n";
echo "Created: 14 paragraph types, 1 content type, all fields.\n";
echo "Next: drush export:all -y && drush cex -y\n";
