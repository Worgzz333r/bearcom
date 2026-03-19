<?php

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

// 1. Delete View page display
$view = \Drupal\views\Entity\View::load('industries_listing');
$displays = $view->get('display');
unset($displays['page_1']);
$view->set('display', $displays);
$view->save();
echo "View page display removed\n";

// 2. Create hero_banner paragraph
$hero = Paragraph::create([
  'type' => 'hero_banner',
  'field_style' => 'solid',
  'field_title' => 'Industry Solutions',
  'field_subtitle' => 'Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis faucibus porta duis. Nam vel parturient maecenas imperdiet proin',
  'field_cta_text' => 'CTA BUTTON',
  'field_cta_url' => ['uri' => 'internal:/contact-us'],
]);
$hero->save();
echo "Hero paragraph created: " . $hero->id() . "\n";

// 3. Create product_grid paragraph (embeds industries_listing view)
$grid = Paragraph::create([
  'type' => 'product_grid',
  'field_title' => 'Posuere ex mattis phasellus',
  'field_view_id' => 'industries_listing',
]);
$grid->save();
echo "Product grid paragraph created: " . $grid->id() . "\n";

// 4. Create cta_block paragraph
$cta = Paragraph::create([
  'type' => 'cta_block',
  'field_style' => 'default',
  'field_title' => 'Posuere ex mattis phasellus',
  'field_description' => ['value' => 'Habitasse egestas felis natoque, cursus nulla amet posuere ipsum', 'format' => 'plain_text'],
  'field_button_text' => 'CTA BUTTON',
  'field_button_url' => ['uri' => 'internal:/contact-us'],
]);

// Add CTA image
$source = '/var/www/web/themes/custom/bearcom/images/cta-industry.jpg';
$dest = 'public://cta-industry.jpg';
\Drupal::service('file_system')->copy($source, $dest, \Drupal\Core\File\FileExists::Replace);
$file = \Drupal\file\Entity\File::create(['uri' => $dest, 'status' => 1]);
$file->save();
$media = \Drupal\media\Entity\Media::create([
  'bundle' => 'image',
  'name' => 'CTA Industry',
  'field_media_image' => ['target_id' => $file->id(), 'alt' => 'BearCom Solutions'],
  'status' => 1,
]);
$media->save();
$cta->set('field_image', ['target_id' => $media->id()]);
$cta->save();
echo "CTA paragraph created: " . $cta->id() . "\n";

// 5. Create flexible_page node
$node = Node::create([
  'type' => 'flexible_page',
  'title' => 'Industry Solutions',
  'status' => 1,
  'path' => ['alias' => '/industries'],
  'field_paragraphs' => [
    ['target_id' => $hero->id(), 'target_revision_id' => $hero->getRevisionId()],
    ['target_id' => $grid->id(), 'target_revision_id' => $grid->getRevisionId()],
    ['target_id' => $cta->id(), 'target_revision_id' => $cta->getRevisionId()],
  ],
]);
$node->save();
echo "Node created: nid " . $node->id() . " at /industries\n";
echo "Done!\n";
