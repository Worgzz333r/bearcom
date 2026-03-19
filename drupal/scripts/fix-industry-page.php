<?php

// Update hero paragraph to solid
$p = \Drupal\paragraphs\Entity\Paragraph::load(2);
if ($p) {
  $p->set('field_style', 'solid');
  $p->set('field_subtitle', 'Taciti nisi lectus montes nunc donec nam iaculis lobortis. Cursus metus diam felis platea imperdiet egestas dui. Sit tortor ac justo, pharetra mollis faucibus porta duis. Nam vel parturient maecenas imperdiet proin');
  $p->save();
  echo "Hero updated to solid style\n";
}

// Update CTA block - add image
$cta = \Drupal\paragraphs\Entity\Paragraph::load(3);
if ($cta) {
  $cta->set('field_title', 'Posuere ex mattis phasellus');
  $cta->set('field_description', ['value' => 'Habitasse egestas felis natoque, cursus nulla amet posuere ipsum', 'format' => 'plain_text']);

  // Create media for CTA image
  $source = '/var/www/web/themes/custom/bearcom/images/cta-industry.jpg';
  $dest = 'public://cta-industry.jpg';
  \Drupal::service('file_system')->copy($source, $dest, \Drupal\Core\File\FileExists::Replace);

  $file = \Drupal\file\Entity\File::create([
    'uri' => $dest,
    'status' => 1,
  ]);
  $file->save();

  $media = \Drupal\media\Entity\Media::create([
    'bundle' => 'image',
    'name' => 'CTA Industry Image',
    'field_media_image' => [
      'target_id' => $file->id(),
      'alt' => 'BearCom Communication Solutions',
    ],
    'status' => 1,
  ]);
  $media->save();

  $cta->set('field_image', ['target_id' => $media->id()]);
  $cta->set('field_button_text', 'CTA BUTTON');
  $cta->save();
  echo "CTA updated with image\n";
}

echo "Done!\n";
