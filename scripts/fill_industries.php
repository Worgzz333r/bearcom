<?php

/**
 * Fill industry nodes with content (CTA, solutions card grids).
 *
 * Run on server:
 *   docker compose exec -T php /var/www/vendor/bin/drush php:script /var/www/scripts/fill_industries.php
 *
 * Run locally:
 *   docker compose exec -T php ./vendor/bin/drush php:script scripts/fill_industries.php
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

$card_desc = '<p>Iuismod vel nisl dapibus, sem dictum mi cras. Suscipit blandit ipsum sollicitudin habitant vedfecfli lacinia habitant maecenas.</p>';
$grid_subtitle = 'Lfaucibus porta duis. Nam vel parturient maecenas imperdiet proin. Et elit natoque duis malesuada egestas nostra lobortis. Posuere ex mattis phasellus per ultricies ipsum rhoncus.';

// Grid config: [style, slider, card_count].
$grid_config = [
  ['industry', 0, 3],
  ['industry_dark', 1, 5],
  ['industry', 1, 5],
];

// Find first media image for CTA and card icons.
$mids = \Drupal::entityTypeManager()->getStorage('media')->getQuery()
  ->condition('bundle', 'image')
  ->accessCheck(FALSE)
  ->range(0, 1)
  ->execute();
$media_id = !empty($mids) ? reset($mids) : 1;
echo "Using media ID $media_id for images.\n\n";

// Get all industry nodes.
$nids = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
  ->condition('type', 'industry')
  ->accessCheck(FALSE)
  ->sort('nid')
  ->execute();

foreach ($nids as $nid) {
  $node = Node::load($nid);
  $title = $node->getTitle();

  // CTA block.
  if ($node->get('field_cta')->isEmpty()) {
    $cta = Paragraph::create([
      'type' => 'cta_block',
      'field_title' => 'Posuere ex mattis phasellus',
      'field_description' => ['value' => '<p>Habitasse egestas felis natoque, cursus nulla amet posuere ipsum</p>', 'format' => 'basic_html'],
      'field_button_text' => 'CTA BUTTON',
      'field_button_url' => ['uri' => 'internal:/contact-us'],
      'field_image' => ['target_id' => $media_id],
      'field_style' => 'credibility',
    ]);
    $cta->save();
    $node->set('field_cta', [['target_id' => $cta->id(), 'target_revision_id' => $cta->getRevisionId()]]);
  }

  // Solutions — 3 card grids.
  if ($node->get('field_solutions')->isEmpty()) {
    $grids = [];

    foreach ($grid_config as $gc) {
      [$style, $slider, $card_count] = $gc;

      $cards = [];
      for ($c = 0; $c < $card_count; $c++) {
        $card = Paragraph::create([
          'type' => 'card_item',
          'field_title' => 'Nulla aenean',
          'field_description' => ['value' => $card_desc, 'format' => 'basic_html'],
          'field_icon' => ['target_id' => $media_id],
        ]);
        $card->save();
        $cards[] = ['target_id' => $card->id(), 'target_revision_id' => $card->getRevisionId()];
      }

      $grid = Paragraph::create([
        'type' => 'industry_card_grid',
        'field_title' => 'Sit tortor ac justo, pharetra mollis',
        'field_subtitle' => $grid_subtitle,
        'field_section_style' => $style,
        'field_use_slider' => $slider,
        'field_cards' => $cards,
      ]);
      $grid->save();
      $grids[] = ['target_id' => $grid->id(), 'target_revision_id' => $grid->getRevisionId()];
    }

    $node->set('field_solutions', $grids);
  }

  $node->save();
  echo "OK: $title (nid $nid)\n";
}

echo "\nDone!\n";
