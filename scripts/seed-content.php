<?php

/**
 * @file
 * Seed content script — replicates server content structure on local.
 * Run: docker compose exec php bash -c "cd /var/www/web && php ../scripts/seed-content.php"
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\path_alias\Entity\PathAlias;

// Bootstrap Drupal.
$autoloader = require '/var/www/vendor/autoload.php';
$kernel = \Drupal\Core\DrupalKernel::createFromRequest(
  \Symfony\Component\HttpFoundation\Request::createFromGlobals(),
  $autoloader,
  'prod'
);
$kernel->boot();
$kernel->preHandle(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
\Drupal::setContainer($kernel->getContainer());

echo "=== BearCom Content Seeder ===\n\n";

// Helper: create a placeholder image file + media entity.
function create_placeholder_image($filename = 'placeholder.png', $width = 800, $height = 600) {
  $dir = 'public://seed-images';
  \Drupal::service('file_system')->prepareDirectory($dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);

  $filepath = $dir . '/' . $filename;

  // Create a simple colored rectangle PNG.
  $img = imagecreatetruecolor($width, $height);
  $bg = imagecolorallocate($img, 0xFC, 0x50, 0x00); // BearCom orange
  imagefill($img, 0, 0, $bg);
  $white = imagecolorallocate($img, 255, 255, 255);
  imagestring($img, 5, $width / 2 - 40, $height / 2 - 8, $filename, $white);
  ob_start();
  imagepng($img);
  $data = ob_get_clean();
  imagedestroy($img);

  file_put_contents(\Drupal::service('file_system')->realpath($dir) . '/' . $filename, $data);

  $file = File::create([
    'filename' => $filename,
    'uri' => $filepath,
    'status' => 1,
  ]);
  $file->save();

  $media = Media::create([
    'bundle' => 'image',
    'name' => pathinfo($filename, PATHINFO_FILENAME),
    'field_media_image' => [
      'target_id' => $file->id(),
      'alt' => pathinfo($filename, PATHINFO_FILENAME),
    ],
    'status' => 1,
  ]);
  $media->save();

  return $media;
}

// Create placeholder images.
echo "[1] Creating placeholder images...\n";
$hero_img = create_placeholder_image('hero-wave-image.png', 1920, 600);
$category_hero = create_placeholder_image('category-hero.png', 1920, 600);
$industry_icon = create_placeholder_image('industry-icon.png', 80, 80);
$industry_card_img = create_placeholder_image('industry-card-image.jpg', 400, 300);
$radios_img = create_placeholder_image('radios-image.jpg', 800, 600);
$cta_img = create_placeholder_image('cta-credibility.jpg', 500, 350);
$rentals_truck = create_placeholder_image('rentals-truck.png', 600, 400);
$connected_services = create_placeholder_image('connected-services.png', 600, 400);
$camera_white = create_placeholder_image('camera-white.jpg', 400, 300);
$camera_building = create_placeholder_image('camera-building.jpg', 400, 300);
$camera_sky = create_placeholder_image('camera-sky.jpg', 400, 300);
$camera_eye = create_placeholder_image('camera-eye.jpg', 400, 300);
$camera_outside = create_placeholder_image('camera-outside.jpg', 400, 300);
$camera_lamp = create_placeholder_image('camera-lamp.jpg', 400, 300);
$keyboard_img = create_placeholder_image('keyboard.jpg', 400, 300);
$office_photo = create_placeholder_image('office-photo.jpg', 600, 400);
$product_img = create_placeholder_image('product-radio.png', 400, 400);
$contact_img = create_placeholder_image('contact-image.jpg', 600, 400);

echo "  Created " . 18 . " placeholder images.\n";

// =========================================================
// Helper: CTA Block paragraph (credibility style, reused many times)
// =========================================================
function create_cta_credibility($cta_img) {
  $cta = Paragraph::create([
    'type' => 'cta_block',
    'field_style' => 'credibility',
    'field_title' => 'Posuere ex mattis phasellus',
    'field_description' => [
      'value' => '<p>Habitasse egestas felis natoque, cursus nulla amet posuere ipsum</p>',
      'format' => 'full_html',
    ],
    'field_button_text' => 'CTA BUTTON',
    'field_button_url' => 'internal:/contact-us',
    'field_image' => ['target_id' => $cta_img->id()],
  ]);
  $cta->save();
  return $cta;
}

// =========================================================
// Helper: FAQ Section paragraph
// =========================================================
function create_faq_section() {
  $faqs = [];
  for ($i = 0; $i < 4; $i++) {
    $faq = Paragraph::create([
      'type' => 'faq_item',
      'field_question' => 'Tristique maecenas suspendisse purus eu nulla?',
      'field_answer' => [
        'value' => '<p>Arcu penatibus imperdiet, nisi pretium etiam vitae interdum? Ex risus ligula dolor fusce amet. Erat tellus dui vestibulum platea sapien.</p>',
        'format' => 'full_html',
      ],
    ]);
    $faq->save();
    $faqs[] = [
      'target_id' => $faq->id(),
      'target_revision_id' => $faq->getRevisionId(),
    ];
  }

  $section = Paragraph::create([
    'type' => 'faq_section',
    'field_title' => 'H2 - General Questions',
    'field_faq_items' => $faqs,
  ]);
  $section->save();
  return $section;
}

// =========================================================
// 1. HOMEPAGE (Flexible Page)
// =========================================================
echo "\n[2] Creating Homepage...\n";

// Hero Banner
$hero_banner = Paragraph::create([
  'type' => 'hero_banner',
  'field_style' => 'product',
  'field_title' => 'Page title',
  'field_subtitle' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
  'field_image' => ['target_id' => $hero_img->id()],
  'field_cta_text' => 'CTA button',
  'field_cta_secondary_text' => 'SECONDARY BUTTON',
]);
$hero_banner->save();

// Guided Journey
$gj_tabs = [];
for ($i = 1; $i <= 3; $i++) {
  $tab = Paragraph::create([
    'type' => 'gj_tab',
    'field_title' => "Tab title $i",
    'field_caption' => 'CAPTION LEAVE HERE',
    'field_description' => [
      'value' => '<p>Guided Journey block, Lorem ipsum dolor sit ametLorem ipsum dolor sit ametLorem ipsum dolor sit ametLorem ipsum dolor sit amet</p>',
      'format' => 'full_html',
    ],
    'field_image' => ['target_id' => $radios_img->id()],
  ]);
  $tab->save();
  $gj_tabs[] = [
    'target_id' => $tab->id(),
    'target_revision_id' => $tab->getRevisionId(),
  ];
}

$guided_journey = Paragraph::create([
  'type' => 'guided_journey',
  'field_title' => 'Guided Journey block',
  'field_description' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
  'field_tabs' => $gj_tabs,
]);
$guided_journey->save();

// Stats Counter
$stats = [];
$stat_data = [
  ['1,200+', 'employees'],
  ['350', 'Certified Service Technicians'],
  ['70', 'locations in 2 countries'],
];
foreach ($stat_data as $sd) {
  $stat = Paragraph::create([
    'type' => 'stat_item',
    'field_number' => $sd[0],
    'field_label' => $sd[1],
  ]);
  $stat->save();
  $stats[] = [
    'target_id' => $stat->id(),
    'target_revision_id' => $stat->getRevisionId(),
  ];
}

$stats_counter = Paragraph::create([
  'type' => 'stats_counter',
  'field_style' => 'default',
  'field_title' => 'Keeping You Connected Through Personalized & Customized Voice, Video, and Data Solutions',
  'field_items' => $stats,
]);
$stats_counter->save();

// Card Grid (Industries)
$cards = [];
for ($i = 0; $i < 4; $i++) {
  $card = Paragraph::create([
    'type' => 'card_item',
    'field_image' => ['target_id' => $industry_card_img->id()],
    'field_title' => 'Supplied',
    'field_subtitle' => 'over 12,000',
    'field_description' => [
      'value' => '<p>hotels across america</p>',
      'format' => 'full_html',
    ],
  ]);
  $card->save();
  $cards[] = [
    'target_id' => $card->id(),
    'target_revision_id' => $card->getRevisionId(),
  ];
}

$card_grid = Paragraph::create([
  'type' => 'card_grid',
  'field_title' => 'Industries',
  'field_description' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
  'field_items' => $cards,
]);
$card_grid->save();

// CTA Block (default)
$cta_default = Paragraph::create([
  'type' => 'cta_block',
  'field_style' => 'default',
  'field_title' => 'CTA block title',
  'field_description' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
  'field_button_text' => 'CTA Button',
  'field_button_url' => 'internal:/contact-us',
]);
$cta_default->save();

// Image Grid - Rentals (2 items)
$ig_rentals_items = [];
foreach ([[$rentals_truck, 'internal:#'], [$connected_services, 'internal:#']] as $data) {
  $item = Paragraph::create([
    'type' => 'image_grid_item',
    'field_image' => ['target_id' => $data[0]->id()],
    'field_url' => $data[1],
  ]);
  $item->save();
  $ig_rentals_items[] = ['target_id' => $item->id(), 'target_revision_id' => $item->getRevisionId()];
}

$ig_rentals = Paragraph::create([
  'type' => 'image_grid',
  'field_style' => 'rentals',
  'field_title' => 'Rentals/Connected Services',
  'field_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis</p>',
    'format' => 'full_html',
  ],
  'field_items' => $ig_rentals_items,
]);
$ig_rentals->save();

// Image Grid - 4 items (plain)
$ig4_items = [];
foreach ([$camera_white, $camera_building, $camera_sky, $camera_eye] as $img) {
  $item = Paragraph::create([
    'type' => 'image_grid_item',
    'field_image' => ['target_id' => $img->id()],
    'field_title' => 'Card Title leaves here',
    'field_url' => 'internal:#',
  ]);
  $item->save();
  $ig4_items[] = ['target_id' => $item->id(), 'target_revision_id' => $item->getRevisionId()];
}

$ig4 = Paragraph::create([
  'type' => 'image_grid',
  'field_style' => 'plain',
  'field_title' => 'Block title',
  'field_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis.</p>',
    'format' => 'full_html',
  ],
  'field_items' => $ig4_items,
]);
$ig4->save();

// Image Grid - 3 items (plain)
$ig3_items = [];
foreach ([$camera_white, $camera_building, $camera_sky] as $img) {
  $item = Paragraph::create([
    'type' => 'image_grid_item',
    'field_image' => ['target_id' => $img->id()],
    'field_title' => 'Card Title leaves here',
    'field_url' => 'internal:#',
  ]);
  $item->save();
  $ig3_items[] = ['target_id' => $item->id(), 'target_revision_id' => $item->getRevisionId()];
}

$ig3 = Paragraph::create([
  'type' => 'image_grid',
  'field_style' => 'plain',
  'field_title' => 'Block title',
  'field_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis.</p>',
    'format' => 'full_html',
  ],
  'field_items' => $ig3_items,
]);
$ig3->save();

// Image Grid - 2 items (plain)
$ig2_items = [];
foreach ([$camera_white, $camera_sky] as $img) {
  $item = Paragraph::create([
    'type' => 'image_grid_item',
    'field_image' => ['target_id' => $img->id()],
    'field_title' => 'Card Title leaves here',
    'field_url' => 'internal:#',
  ]);
  $item->save();
  $ig2_items[] = ['target_id' => $item->id(), 'target_revision_id' => $item->getRevisionId()];
}

$ig2 = Paragraph::create([
  'type' => 'image_grid',
  'field_style' => 'plain',
  'field_title' => 'Block title',
  'field_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis.</p>',
    'format' => 'full_html',
  ],
  'field_items' => $ig2_items,
]);
$ig2->save();

// Solutions Grid (6 children)
$sol_items = [];
$sol_images = [$camera_outside, $camera_lamp, $keyboard_img, $camera_outside, $camera_lamp, $keyboard_img];
foreach ($sol_images as $img) {
  $sol_card = Paragraph::create([
    'type' => 'solutions_card',
    'field_image' => ['target_id' => $img->id()],
    'field_title' => 'Nulla aenean',
    'field_description' => [
      'value' => '<p>Iuismod vel nisl dapibus, sem dictum mi cras. Suscipit blandit ipsum sollicitudin habitant vedfecfli lacinia habitant maecenas.</p>',
      'format' => 'full_html',
    ],
  ]);
  $sol_card->save();
  $sol_items[] = ['target_id' => $sol_card->id(), 'target_revision_id' => $sol_card->getRevisionId()];
}

$solutions_grid = Paragraph::create([
  'type' => 'solutions_grid',
  'field_title' => 'Sit tortor ac justo, pharetra mollis',
  'field_description' => [
    'value' => '<p>Lfaucibus porta duis. Nam vel parturient maecenas imperdiet proin. Et elit natoque duis malesuada egestas nostra lobortis. Posuere ex mattis phasellus</p>',
    'format' => 'full_html',
  ],
  'field_items' => $sol_items,
]);
$solutions_grid->save();

// Create Homepage node
$homepage = Node::create([
  'type' => 'flexible_page',
  'title' => 'Homepage',
  'status' => 1,
  'promote' => 1,
  'field_paragraphs' => [
    ['target_id' => $hero_banner->id(), 'target_revision_id' => $hero_banner->getRevisionId()],
    ['target_id' => $guided_journey->id(), 'target_revision_id' => $guided_journey->getRevisionId()],
    ['target_id' => $stats_counter->id(), 'target_revision_id' => $stats_counter->getRevisionId()],
    ['target_id' => $card_grid->id(), 'target_revision_id' => $card_grid->getRevisionId()],
    ['target_id' => $cta_default->id(), 'target_revision_id' => $cta_default->getRevisionId()],
    ['target_id' => $ig_rentals->id(), 'target_revision_id' => $ig_rentals->getRevisionId()],
    ['target_id' => $ig4->id(), 'target_revision_id' => $ig4->getRevisionId()],
    ['target_id' => $ig3->id(), 'target_revision_id' => $ig3->getRevisionId()],
    ['target_id' => $ig2->id(), 'target_revision_id' => $ig2->getRevisionId()],
    ['target_id' => $solutions_grid->id(), 'target_revision_id' => $solutions_grid->getRevisionId()],
  ],
]);
$homepage->save();

// Set as front page.
\Drupal::configFactory()->getEditable('system.site')->set('page.front', '/node/' . $homepage->id())->save();
echo "  Homepage created (node/{$homepage->id()}) — set as front page.\n";

// =========================================================
// 2. INDUSTRY SOLUTIONS (Flexible Page) — /industry
// =========================================================
echo "\n[3] Creating Industry Solutions page...\n";

$ind_hero = Paragraph::create([
  'type' => 'hero_banner',
  'field_style' => 'solid',
  'field_title' => 'Industry Solutions',
  'field_subtitle' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis</p>',
    'format' => 'full_html',
  ],
  'field_cta_text' => 'CTA BUTTON',
]);
$ind_hero->save();

$ind_product_grid = Paragraph::create([
  'type' => 'product_grid',
  'field_title' => 'Posuere ex mattis phasellus',
  'field_view_id' => 'industries_listing',
]);
$ind_product_grid->save();

$ind_cta = create_cta_credibility($cta_img);

$industry_solutions = Node::create([
  'type' => 'flexible_page',
  'title' => 'Industry Solutions',
  'status' => 1,
  'promote' => 1,
  'path' => ['alias' => '/industry'],
  'field_paragraphs' => [
    ['target_id' => $ind_hero->id(), 'target_revision_id' => $ind_hero->getRevisionId()],
    ['target_id' => $ind_product_grid->id(), 'target_revision_id' => $ind_product_grid->getRevisionId()],
    ['target_id' => $ind_cta->id(), 'target_revision_id' => $ind_cta->getRevisionId()],
  ],
]);
$industry_solutions->save();
echo "  Industry Solutions created (node/{$industry_solutions->id()}).\n";

// =========================================================
// 3. INDUSTRIES (9 Industry nodes) — "Nulla aenean"
// =========================================================
echo "\n[4] Creating 9 Industry nodes...\n";

for ($i = 0; $i < 9; $i++) {
  // Industry Card Grid paragraphs (solutions field)
  $icg_items = [];
  $num_grids = ($i % 3 == 0) ? 3 : 2;

  for ($g = 0; $g < $num_grids; $g++) {
    $num_cards = ($g == 0) ? 3 : 4;
    $style = ($g == 1) ? 'industry_dark' : 'industry';

    $ind_cards_refs = [];
    // Industry Card Grid references industry nodes — we'll create simple card items
    $icg = Paragraph::create([
      'type' => 'industry_card_grid',
      'field_style' => $style,
      'field_title' => 'Sit tortor ac justo, pharetra mollis',
      'field_description' => [
        'value' => '<p>Lfaucibus porta duis. Nam vel parturient maecenas imperdiet proin. Et elit natoque duis malesuada egestas nostra lobortis. Posuere ex mattis phasellus</p>',
        'format' => 'full_html',
      ],
    ]);
    $icg->save();
    $icg_items[] = ['target_id' => $icg->id(), 'target_revision_id' => $icg->getRevisionId()];
  }

  $ind_cta_n = create_cta_credibility($cta_img);

  $industry = Node::create([
    'type' => 'industry',
    'title' => 'Nulla aenean',
    'status' => 1,
    'promote' => 1,
    'field_icon' => ['target_id' => $industry_icon->id()],
    'field_hero_image' => ['target_id' => $industry_card_img->id()],
    'field_description' => [
      'value' => '<p>Iuismod vel nisl dapibus, sem dictum mi cras. Suscipit blandit ipsum sollicitudin habitant vedfecfli lacinia habitant maecenas.</p>',
      'format' => 'full_html',
    ],
    'field_solutions' => $icg_items,
    'field_cta' => [
      'target_id' => $ind_cta_n->id(),
      'target_revision_id' => $ind_cta_n->getRevisionId(),
    ],
  ]);
  $industry->save();
  echo "  Industry node {$industry->id()} created.\n";
}

// =========================================================
// 4. PRODUCTS (Flexible Page) — /products
// =========================================================
echo "\n[5] Creating Products page...\n";

$prod_hero = Paragraph::create([
  'type' => 'hero_banner',
  'field_style' => 'color',
  'field_title' => 'Category Page',
  'field_subtitle' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
  'field_image' => ['target_id' => $category_hero->id()],
]);
$prod_hero->save();

$prod_grid = Paragraph::create([
  'type' => 'product_grid',
  'field_view_id' => 'products_listing',
]);
$prod_grid->save();

$prod_faq = create_faq_section();

$products_page = Node::create([
  'type' => 'flexible_page',
  'title' => 'Products',
  'status' => 1,
  'promote' => 1,
  'field_paragraphs' => [
    ['target_id' => $prod_hero->id(), 'target_revision_id' => $prod_hero->getRevisionId()],
    ['target_id' => $prod_grid->id(), 'target_revision_id' => $prod_grid->getRevisionId()],
    ['target_id' => $prod_faq->id(), 'target_revision_id' => $prod_faq->getRevisionId()],
  ],
]);
$products_page->save();
echo "  Products page created (node/{$products_page->id()}).\n";

// =========================================================
// 5. MOTOTRBO R2 Radio (Product)
// =========================================================
echo "\n[6] Creating Product: MOTOTRBO R2 Radio...\n";

$product = Node::create([
  'type' => 'product',
  'title' => 'MOTOTRBO R2 Radio',
  'status' => 1,
  'field_images' => [['target_id' => $product_img->id()]],
  'field_short_description' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
    'format' => 'full_html',
  ],
]);
$product->save();
echo "  Product created (node/{$product->id()}).\n";

// =========================================================
// 6. LOCATION PARENT (Flexible Page) — /locations
// =========================================================
echo "\n[7] Creating Location Parent...\n";

$loc_hero = Paragraph::create([
  'type' => 'hero_banner',
  'field_style' => 'soundwave',
  'field_title' => 'BearCom Branch Locator & Directory',
  'field_subtitle' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis</p>',
    'format' => 'full_html',
  ],
  'field_cta_text' => 'CTA BUTTON',
  'field_cta_secondary_text' => 'CTA BUTTON',
  'field_phone' => '(800) 527-1670',
]);
$loc_hero->save();

$loc_finder = Paragraph::create([
  'type' => 'location_finder',
]);
$loc_finder->save();

$loc_cta = create_cta_credibility($cta_img);

$location_parent = Node::create([
  'type' => 'flexible_page',
  'title' => 'Location Parent',
  'status' => 1,
  'promote' => 1,
  'path' => ['alias' => '/locations'],
  'field_paragraphs' => [
    ['target_id' => $loc_hero->id(), 'target_revision_id' => $loc_hero->getRevisionId()],
    ['target_id' => $loc_finder->id(), 'target_revision_id' => $loc_finder->getRevisionId()],
    ['target_id' => $loc_cta->id(), 'target_revision_id' => $loc_cta->getRevisionId()],
  ],
]);
$location_parent->save();
echo "  Location Parent created (node/{$location_parent->id()}).\n";

// =========================================================
// 7. GARLAND, TEXAS (Location)
// =========================================================
echo "\n[8] Creating Location: Garland, Texas...\n";

// Open Hours
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$hours_refs = [];
foreach ($days as $day) {
  $hours_text = ($day === 'Sunday') ? 'Closed' : '9:00AM - 6:00PM';
  $oh = Paragraph::create([
    'type' => 'open_hours_row',
    'field_day' => $day,
    'field_hours' => $hours_text,
  ]);
  $oh->save();
  $hours_refs[] = ['target_id' => $oh->id(), 'target_revision_id' => $oh->getRevisionId()];
}

// FAQ Section for location
$loc_faq = create_faq_section();

// Get Texas term.
$texas_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
  'name' => 'Texas',
  'vid' => 'state_province',
]);
$texas_tid = !empty($texas_terms) ? reset($texas_terms)->id() : NULL;

$location = Node::create([
  'type' => 'location',
  'title' => 'Garland, Texas',
  'status' => 1,
  'field_state' => $texas_tid ? ['target_id' => $texas_tid] : [],
  'field_address' => [
    'value' => "<p>BearCom Headquarters</p>\n<p>4009 Distribution Drive, #200<br>Garland, Texas 75041</p>",
    'format' => 'full_html',
  ],
  'field_phone' => '800-527-1670',
  'field_latitude' => '32.9157',
  'field_longitude' => '-96.637',
  'field_photo' => ['target_id' => $office_photo->id()],
  'field_open_hours' => $hours_refs,
  'field_about' => [
    'value' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>',
    'format' => 'basic_html',
  ],
  'field_faq_section' => [
    'target_id' => $loc_faq->id(),
    'target_revision_id' => $loc_faq->getRevisionId(),
  ],
  'field_hero_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis faucibus porta duis. Nam vel parturient maecenas imperdiet proin</p>',
    'format' => 'full_html',
  ],
]);
$location->save();
echo "  Location created (node/{$location->id()}).\n";

// =========================================================
// 8. 403 - Access Denied (Flexible Page)
// =========================================================
echo "\n[9] Creating 403 page...\n";

$page_403 = Node::create([
  'type' => 'flexible_page',
  'title' => '403 - Access Denied',
  'status' => 1,
  'promote' => 1,
  'path' => ['alias' => '/403'],
]);
$page_403->save();

// Set as 403 page.
\Drupal::configFactory()->getEditable('system.site')->set('page.403', '/node/' . $page_403->id())->save();
echo "  403 page created (node/{$page_403->id()}).\n";

// =========================================================
// 9. 404 - Page Not Found (Flexible Page)
// =========================================================
echo "\n[10] Creating 404 page...\n";

$page_404 = Node::create([
  'type' => 'flexible_page',
  'title' => '404 - Page Not Found',
  'status' => 1,
  'promote' => 1,
  'path' => ['alias' => '/404'],
]);
$page_404->save();

// Set as 404 page.
\Drupal::configFactory()->getEditable('system.site')->set('page.404', '/node/' . $page_404->id())->save();
echo "  404 page created (node/{$page_404->id()}).\n";

// =========================================================
// 10. CONTACT US (Flexible Page) — /contact-us
// =========================================================
echo "\n[11] Creating Contact Us page...\n";

// Contact Info paragraph (2 children)
$contact_info = Paragraph::create([
  'type' => 'contact_info',
  'field_title' => 'Porta nulla felis tincidunt viverra convall?',
  'field_description' => [
    'value' => '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis</p>',
    'format' => 'full_html',
  ],
  'field_image' => ['target_id' => $contact_img->id()],
]);
$contact_info->save();

$contact_cta = create_cta_credibility($cta_img);

$contact_us = Node::create([
  'type' => 'flexible_page',
  'title' => 'Contact Us',
  'status' => 1,
  'promote' => 1,
  'path' => ['alias' => '/contact-us'],
  'field_paragraphs' => [
    ['target_id' => $contact_info->id(), 'target_revision_id' => $contact_info->getRevisionId()],
    ['target_id' => $contact_cta->id(), 'target_revision_id' => $contact_cta->getRevisionId()],
  ],
]);
$contact_us->save();
echo "  Contact Us created (node/{$contact_us->id()}).\n";

// =========================================================
// Clear cache
// =========================================================
echo "\n[12] Clearing cache...\n";
drupal_flush_all_caches();
echo "  Cache cleared.\n";

echo "\n=== Done! All content seeded. ===\n";
echo "Pages created:\n";
echo "  - Homepage (front page)\n";
echo "  - Industry Solutions (/industry)\n";
echo "  - 9x Industry (Nulla aenean)\n";
echo "  - Products (/products)\n";
echo "  - MOTOTRBO R2 Radio (Product)\n";
echo "  - Location Parent (/locations)\n";
echo "  - Garland, Texas (Location)\n";
echo "  - 403 (/403)\n";
echo "  - 404 (/404)\n";
echo "  - Contact Us (/contact-us)\n";
