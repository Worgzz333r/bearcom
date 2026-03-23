<?php

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

$desc = 'Iuismod vel nisl dapibus, sem dictum mi cras. Suscipit blandit ipsum sollicitudin habitant vedfecfli lacinia habitant maecenas.';
$section_title = 'Sit tortor ac justo, pharetra mollis';
$section_subtitle = 'Lfaucibus porta duis. Nam vel parturient maecenas imperdiet proin. Et elit natoque duis malesuada egestas nostra lobortis. Posuere ex mattis phasellus per ultricies ipsum rhoncus.';

$sections = [];

// Create 3 card_grid sections
for ($s = 0; $s < 3; $s++) {
  $cards = [];

  // Each section has 3 card_items
  for ($c = 0; $c < 3; $c++) {
    $card = Paragraph::create([
      'type' => 'card_item',
      'field_title' => 'Nulla aenean',
      'field_description' => $desc,
    ]);
    $card->save();
    $cards[] = ['target_id' => $card->id(), 'target_revision_id' => $card->getRevisionId()];
  }

  $grid = Paragraph::create([
    'type' => 'card_grid',
    'field_title' => $section_title,
    'field_subtitle' => $section_subtitle,
    'field_cards' => $cards,
  ]);
  $grid->save();
  $sections[] = ['target_id' => $grid->id(), 'target_revision_id' => $grid->getRevisionId()];
  echo "Created card_grid section " . ($s + 1) . "\n";
}

// Update first industry node (nid 1)
$node = Node::load(1);
$node->set('field_solutions', $sections);
$node->save();
echo "Updated node 1 with 3 card_grid sections\n";
echo "Done!\n";
