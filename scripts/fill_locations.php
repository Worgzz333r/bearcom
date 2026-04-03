<?php

/**
 * Fill all location nodes with content (address, about, description, hours, FAQ).
 *
 * Run on server:
 *   docker compose exec -T php /var/www/vendor/bin/drush php:script /var/www/scripts/fill_locations.php
 *
 * Run locally:
 *   docker compose exec -T php ./vendor/bin/drush php:script scripts/fill_locations.php
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

// ── Location data ──────────────────────────────────────────────────────────
$locations = [
  'Albany, New York' => ['phone' => '800-555-0402', 'lat' => '42.6526', 'lon' => '-73.7562', 'address' => '<p>BearCom Albany</p><p>1 Commerce Plaza<br>Albany, NY 12260</p>'],
  'Atlanta, Georgia' => ['phone' => '800-555-1001', 'lat' => '33.749', 'lon' => '-84.388', 'address' => '<p>BearCom Atlanta</p><p>55 Trinity Ave SW<br>Atlanta, GA 30303</p>'],
  'Austin, Texas' => ['phone' => '800-555-0103', 'lat' => '30.2672', 'lon' => '-97.7431', 'address' => '<p>BearCom Austin</p><p>200 Congress Ave<br>Austin, TX 78701</p>'],
  'Boston, Massachusetts' => ['phone' => '800-555-1601', 'lat' => '42.3601', 'lon' => '-71.0589', 'address' => '<p>BearCom Boston</p><p>1 City Hall Sq<br>Boston, MA 02201</p>'],
  'Boulder, Colorado' => ['phone' => '800-555-0602', 'lat' => '40.015', 'lon' => '-105.271', 'address' => '<p>BearCom Boulder</p><p>1777 Broadway<br>Boulder, CO 80302</p>'],
  'Buffalo, New York' => ['phone' => '800-555-0401', 'lat' => '42.8864', 'lon' => '-78.8784', 'address' => '<p>BearCom Buffalo</p><p>65 Court St<br>Buffalo, NY 14202</p>'],
  'Charlotte, North Carolina' => ['phone' => '800-555-1101', 'lat' => '35.2271', 'lon' => '-80.8431', 'address' => '<p>BearCom Charlotte</p><p>600 E 4th St<br>Charlotte, NC 28202</p>'],
  'Chicago, Illinois' => ['phone' => '800-527-1672', 'lat' => '41.8781', 'lon' => '-87.6298', 'address' => '<p>BearCom Chicago</p><p>500 N Michigan Ave<br>Chicago, IL 60611</p>'],
  'Cincinnati, Ohio' => ['phone' => '800-555-1803', 'lat' => '39.1031', 'lon' => '-84.512', 'address' => '<p>BearCom Cincinnati</p><p>801 Plum St<br>Cincinnati, OH 45202</p>'],
  'Cleveland, Ohio' => ['phone' => '800-555-1802', 'lat' => '41.4993', 'lon' => '-81.6944', 'address' => '<p>BearCom Cleveland</p><p>601 Lakeside Ave<br>Cleveland, OH 44114</p>'],
  'Columbus, Ohio' => ['phone' => '800-555-1801', 'lat' => '39.9612', 'lon' => '-82.9988', 'address' => '<p>BearCom Columbus</p><p>90 W Broad St<br>Columbus, OH 43215</p>'],
  'Dallas, Texas' => ['phone' => '800-555-0101', 'lat' => '32.7767', 'lon' => '-96.797', 'address' => '<p>BearCom Dallas</p><p>1000 Main St<br>Dallas, TX 75201</p>'],
  'Denver, Colorado' => ['phone' => '800-555-0601', 'lat' => '39.7392', 'lon' => '-104.99', 'address' => '<p>BearCom Denver</p><p>1700 Broadway<br>Denver, CO 80290</p>'],
  'Detroit, Michigan' => ['phone' => '800-555-1501', 'lat' => '42.3314', 'lon' => '-83.0458', 'address' => '<p>BearCom Detroit</p><p>2 Woodward Ave<br>Detroit, MI 48226</p>'],
  'Garland, Texas' => ['phone' => '800-527-1670', 'lat' => '32.9157', 'lon' => '-96.637', 'address' => '<p>BearCom Headquarters</p><p>4009 Distribution Drive, #200<br>Garland, Texas 75041</p>'],
  'Houston, Texas' => ['phone' => '800-555-0102', 'lat' => '29.7604', 'lon' => '-95.3698', 'address' => '<p>BearCom Houston</p><p>500 Travis St<br>Houston, TX 77002</p>'],
  'Indianapolis, Indiana' => ['phone' => '800-555-2001', 'lat' => '39.7684', 'lon' => '-86.1581', 'address' => '<p>BearCom Indianapolis</p><p>200 E Washington St<br>Indianapolis, IN 46204</p>'],
  'Kansas City, Missouri' => ['phone' => '800-555-1901', 'lat' => '39.0997', 'lon' => '-94.5786', 'address' => '<p>BearCom Kansas City</p><p>414 E 12th St<br>Kansas City, MO 64106</p>'],
  'Las Vegas, Nevada' => ['phone' => '800-555-1301', 'lat' => '36.1699', 'lon' => '-115.14', 'address' => '<p>BearCom Las Vegas</p><p>495 S Main St<br>Las Vegas, NV 89101</p>'],
  'Los Angeles, California' => ['phone' => '800-527-1671', 'lat' => '34.0522', 'lon' => '-118.244', 'address' => '<p>BearCom Los Angeles</p><p>1234 Wilshire Blvd<br>Los Angeles, CA 90017</p>'],
  'Milwaukee, Wisconsin' => ['phone' => '800-555-2101', 'lat' => '43.0389', 'lon' => '-87.9065', 'address' => '<p>BearCom Milwaukee</p><p>200 E Wells St<br>Milwaukee, WI 53202</p>'],
  'Minneapolis, Minnesota' => ['phone' => '800-555-1401', 'lat' => '44.9778', 'lon' => '-93.265', 'address' => '<p>BearCom Minneapolis</p><p>350 S 5th St<br>Minneapolis, MN 55415</p>'],
  'Nashville, Tennessee' => ['phone' => '800-555-1201', 'lat' => '36.1627', 'lon' => '-86.7816', 'address' => '<p>BearCom Nashville</p><p>1 Public Sq<br>Nashville, TN 37201</p>'],
  'New Orleans, Louisiana' => ['phone' => '800-555-2201', 'lat' => '29.9511', 'lon' => '-90.0715', 'address' => '<p>BearCom New Orleans</p><p>1300 Perdido St<br>New Orleans, LA 70112</p>'],
  'New York, New York' => ['phone' => '800-527-1674', 'lat' => '40.7484', 'lon' => '-73.9857', 'address' => '<p>BearCom New York</p><p>350 Fifth Avenue<br>New York, NY 10118</p>'],
  'Oklahoma City, Oklahoma' => ['phone' => '800-555-2401', 'lat' => '35.4676', 'lon' => '-97.5164', 'address' => '<p>BearCom Oklahoma City</p><p>200 N Walker Ave<br>Oklahoma City, OK 73102</p>'],
  'Orlando, Florida' => ['phone' => '800-555-0301', 'lat' => '28.5383', 'lon' => '-81.3792', 'address' => '<p>BearCom Orlando</p><p>400 S Orange Ave<br>Orlando, FL 32801</p>'],
  'Philadelphia, Pennsylvania' => ['phone' => '800-555-1701', 'lat' => '39.9526', 'lon' => '-75.1652', 'address' => '<p>BearCom Philadelphia</p><p>1401 JFK Blvd<br>Philadelphia, PA 19102</p>'],
  'Phoenix, Arizona' => ['phone' => '800-555-0701', 'lat' => '33.4484', 'lon' => '-112.074', 'address' => '<p>BearCom Phoenix</p><p>200 W Washington St<br>Phoenix, AZ 85003</p>'],
  'Pittsburgh, Pennsylvania' => ['phone' => '800-555-1702', 'lat' => '40.4406', 'lon' => '-79.9959', 'address' => '<p>BearCom Pittsburgh</p><p>414 Grant St<br>Pittsburgh, PA 15219</p>'],
  'Portland, Oregon' => ['phone' => '800-555-0901', 'lat' => '45.5152', 'lon' => '-122.678', 'address' => '<p>BearCom Portland</p><p>1221 SW 4th Ave<br>Portland, OR 97204</p>'],
  'Sacramento, California' => ['phone' => '800-555-0203', 'lat' => '38.5816', 'lon' => '-121.494', 'address' => '<p>BearCom Sacramento</p><p>915 Capitol Mall<br>Sacramento, CA 95814</p>'],
  'Salt Lake City, Utah' => ['phone' => '800-555-2301', 'lat' => '40.7608', 'lon' => '-111.891', 'address' => '<p>BearCom Salt Lake City</p><p>451 S State St<br>Salt Lake City, UT 84111</p>'],
  'San Antonio, Texas' => ['phone' => '800-555-0104', 'lat' => '29.4241', 'lon' => '-98.4936', 'address' => '<p>BearCom San Antonio</p><p>300 Alamo Plaza<br>San Antonio, TX 78205</p>'],
  'San Diego, California' => ['phone' => '800-555-0202', 'lat' => '32.7157', 'lon' => '-117.161', 'address' => '<p>BearCom San Diego</p><p>600 B St<br>San Diego, CA 92101</p>'],
  'San Francisco, California' => ['phone' => '800-555-0201', 'lat' => '37.7749', 'lon' => '-122.419', 'address' => '<p>BearCom San Francisco</p><p>100 Market St<br>San Francisco, CA 94105</p>'],
  'Seattle, Washington' => ['phone' => '800-555-0801', 'lat' => '47.6062', 'lon' => '-122.332', 'address' => '<p>BearCom Seattle</p><p>600 4th Ave<br>Seattle, WA 98104</p>'],
  'Springfield, Illinois' => ['phone' => '800-555-0501', 'lat' => '39.7817', 'lon' => '-89.6501', 'address' => '<p>BearCom Springfield</p><p>300 S 2nd St<br>Springfield, IL 62701</p>'],
  'St. Louis, Missouri' => ['phone' => '800-555-1902', 'lat' => '38.627', 'lon' => '-90.1994', 'address' => '<p>BearCom St. Louis</p><p>1200 Market St<br>St. Louis, MO 63103</p>'],
  'Tampa, Florida' => ['phone' => '800-555-0302', 'lat' => '27.9506', 'lon' => '-82.4572', 'address' => '<p>BearCom Tampa</p><p>611 N Tampa St<br>Tampa, FL 33602</p>'],
  'Tucson, Arizona' => ['phone' => '800-555-0702', 'lat' => '32.2226', 'lon' => '-110.975', 'address' => '<p>BearCom Tucson</p><p>255 W Alameda St<br>Tucson, AZ 85701</p>'],
];

// ── Shared content (same as Garland template) ──────────────────────────────

$about_text = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>';

$description_text = '<p>Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis faucibus porta duis. Nam vel parturient maecenas imperdiet proin</p>';

$faq_questions = [
  ['question' => 'Tristique maecenas suspendisse purus eu nulla?', 'answer' => '<p>Arcu penatibus imperdiet, nisi pretium etiam vitae interdum? Ex risus ligula dolor fusce amet. Erat tellus dui vestibulum platea sapien.</p>'],
  ['question' => 'Tristique maecenas suspendisse purus eu nulla?', 'answer' => '<p>Arcu penatibus imperdiet, nisi pretium etiam vitae interdum? Ex risus ligula dolor fusce amet.</p>'],
  ['question' => 'Tristique maecenas suspendisse purus eu nulla?', 'answer' => '<p>Arcu penatibus imperdiet, nisi pretium etiam vitae interdum?</p>'],
  ['question' => 'Tristique maecenas suspendisse purus eu nulla?', 'answer' => '<p>Arcu penatibus imperdiet, nisi pretium etiam vitae interdum? Ex risus ligula dolor fusce amet.</p>'],
];

$open_hours = [
  ['day' => 'Monday',    'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Tuesday',   'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Wednesday', 'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Thursday',  'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Friday',    'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Saturday',  'hours' => '9:00AM - 6:00PM'],
  ['day' => 'Sunday',    'hours' => 'Closed'],
];

// ── Find or create placeholder media for field_photo ───────────────────────

function get_or_create_placeholder_media() {
  // Try to find existing media.
  $mids = \Drupal::entityTypeManager()->getStorage('media')->getQuery()
    ->condition('bundle', 'image')
    ->accessCheck(FALSE)
    ->range(0, 1)
    ->execute();

  if (!empty($mids)) {
    return reset($mids);
  }

  // Create a simple placeholder image.
  $dir = 'public://';
  $filepath = $dir . 'placeholder-location.png';

  $img = imagecreatetruecolor(800, 600);
  $bg = imagecolorallocate($img, 0xFC, 0x50, 0x00);
  imagefill($img, 0, 0, $bg);
  $white = imagecolorallocate($img, 255, 255, 255);
  imagestring($img, 5, 320, 290, 'BearCom', $white);
  ob_start();
  imagepng($img);
  $data = ob_get_clean();
  imagedestroy($img);

  $real_path = \Drupal::service('file_system')->realpath($dir);
  file_put_contents($real_path . '/placeholder-location.png', $data);

  $file = \Drupal\file\Entity\File::create([
    'filename' => 'placeholder-location.png',
    'uri' => $filepath,
    'status' => 1,
  ]);
  $file->save();

  $media = \Drupal\media\Entity\Media::create([
    'bundle' => 'image',
    'name' => 'Location placeholder',
    'field_media_image' => [
      'target_id' => $file->id(),
      'alt' => 'BearCom location',
    ],
  ]);
  $media->save();

  return $media->id();
}

// ── Main ───────────────────────────────────────────────────────────────────

$photo_mid = get_or_create_placeholder_media();
echo "Using media ID $photo_mid for field_photo.\n\n";

$updated = 0;
$skipped = 0;

foreach ($locations as $title => $data) {
  // Find node by title.
  $nids = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
    ->condition('type', 'location')
    ->condition('title', $title)
    ->accessCheck(FALSE)
    ->execute();

  if (empty($nids)) {
    echo "SKIP (not found): $title\n";
    $skipped++;
    continue;
  }

  $nid = reset($nids);
  $node = Node::load($nid);

  // Update address (always overwrite to ensure correct HTML format).
  $node->set('field_address', ['value' => $data['address'], 'format' => 'full_html']);

  // Update phone, lat, lon.
  $node->set('field_phone', $data['phone']);
  $node->set('field_latitude', $data['lat']);
  $node->set('field_longitude', $data['lon']);

  // field_about.
  if ($node->get('field_about')->isEmpty()) {
    $node->set('field_about', ['value' => $about_text, 'format' => 'full_html']);
  }

  // field_description.
  if ($node->get('field_description')->isEmpty()) {
    $node->set('field_description', ['value' => $description_text, 'format' => 'full_html']);
  }

  // field_photo.
  if ($node->get('field_photo')->isEmpty()) {
    $node->set('field_photo', ['target_id' => $photo_mid]);
  }

  // field_hero_link.
  if ($node->get('field_hero_link')->isEmpty()) {
    $node->set('field_hero_link', [
      'uri' => 'internal:/node/' . $nid,
      'title' => 'CTA BUTTON',
    ]);
  }

  // field_open_hours.
  if ($node->get('field_open_hours')->isEmpty()) {
    $hours_paragraphs = [];
    foreach ($open_hours as $row) {
      $p = Paragraph::create([
        'type' => 'open_hours_row',
        'field_day' => $row['day'],
        'field_hours' => $row['hours'],
      ]);
      $p->save();
      $hours_paragraphs[] = [
        'target_id' => $p->id(),
        'target_revision_id' => $p->getRevisionId(),
      ];
    }
    $node->set('field_open_hours', $hours_paragraphs);
  }

  // field_faq.
  if ($node->get('field_faq')->isEmpty()) {
    $faq_items = [];
    foreach ($faq_questions as $q) {
      $item = Paragraph::create([
        'type' => 'faq_item',
        'field_question' => $q['question'],
        'field_answer' => ['value' => $q['answer'], 'format' => 'full_html'],
      ]);
      $item->save();
      $faq_items[] = [
        'target_id' => $item->id(),
        'target_revision_id' => $item->getRevisionId(),
      ];
    }

    $faq_wrapper = Paragraph::create([
      'type' => 'faq_two_column',
      'field_title' => 'H2 - General Questions',
      'field_faq' => $faq_items,
      'field_latitude' => $data['lat'],
      'field_longitude' => $data['lon'],
    ]);
    $faq_wrapper->save();

    $node->set('field_faq', [
      'target_id' => $faq_wrapper->id(),
      'target_revision_id' => $faq_wrapper->getRevisionId(),
    ]);
  }

  $node->save();
  echo "OK: $title (nid $nid)\n";
  $updated++;
}

echo "\nDone! Updated: $updated, Skipped: $skipped\n";
