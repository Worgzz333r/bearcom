#!/bin/bash
# BearCom — Create main menu structure
# Run: docker compose exec php bash /var/www/scripts/create-menu.sh

set -e

echo "========================================="
echo "  BearCom — Creating Main Menu"
echo "========================================="

cd /var/www/web

../vendor/bin/drush php-eval '
use Drupal\menu_link_content\Entity\MenuLinkContent;

function m($title, $uri, $menu, $parent_id = null, $weight = 0) {
  $item = MenuLinkContent::create([
    "title" => $title,
    "link" => ["uri" => $uri],
    "menu_name" => $menu,
    "weight" => $weight,
    "expanded" => true,
  ]);
  if ($parent_id) {
    $item->set("parent", "menu_link_content:" . $parent_id);
  }
  $item->save();
  echo "  + $title\n";
  return $item->uuid();
}

echo "--- Solutions ---\n";
$sol = m("Solutions", "internal:/", "main", null, 0);

$voice = m("Voice", "internal:/", "main", $sol, 0);
m("Two-Way Radios", "internal:/products", "main", $voice, 0);
m("Portable Radios", "internal:/products", "main", $voice, 1);
m("Mobile Radios", "internal:/products", "main", $voice, 2);
m("Cellular Push to Talk", "internal:/products", "main", $voice, 3);
m("Repeaters", "internal:/products", "main", $voice, 4);
m("BDA/DAS", "internal:/products", "main", $voice, 5);
m("Emergency Mass Notification", "internal:/products", "main", $voice, 6);
m("Dispatch Consoles", "internal:/products", "main", $voice, 7);
m("Call Boxes", "internal:/products", "main", $voice, 8);
m("Accessories", "internal:/products", "main", $voice, 9);
m("Batteries and Chargers", "internal:/products", "main", $voice, 10);
m("Antennas", "internal:/products", "main", $voice, 11);
m("Microphones", "internal:/products", "main", $voice, 12);
m("Earpieces", "internal:/products", "main", $voice, 13);
m("Cases", "internal:/products", "main", $voice, 14);

$sec = m("Security", "internal:/", "main", $sol, 1);
m("Surveillance Cameras", "internal:/products", "main", $sec, 0);
m("Body Worn Cameras", "internal:/products", "main", $sec, 1);
m("Access Control & Intercom", "internal:/products", "main", $sec, 2);
m("License Plate Recognition", "internal:/products", "main", $sec, 3);
m("Concealed Weapons Detection", "internal:/products", "main", $sec, 4);

$data = m("Data", "internal:/", "main", $sol, 2);
m("Private LTE", "internal:/products", "main", $data, 0);
m("AlwaysOn Integrated Solutions", "internal:/products", "main", $data, 1);
m("Backhaul Solutions", "internal:/products", "main", $data, 2);

echo "--- Rentals ---\n";
$rent = m("Rentals", "internal:/", "main", null, 1);
m("Two-Way Radio Rentals", "internal:/", "main", $rent, 0);
m("Video Surveillance & CCTV", "internal:/", "main", $rent, 1);
m("Private LTE", "internal:/", "main", $rent, 2);
m("Event Management & Staffing", "internal:/", "main", $rent, 3);

echo "--- Industries ---\n";
$ind = m("Industries", "internal:/industries", "main", null, 2);
m("Education", "internal:/industries", "main", $ind, 0);
m("Utilities & Public Works", "internal:/industries", "main", $ind, 1);
m("Transportation Logistics", "internal:/industries", "main", $ind, 2);
m("Hospitality", "internal:/industries", "main", $ind, 3);
m("Public Safety", "internal:/industries", "main", $ind, 4);
m("Manufacturing", "internal:/industries", "main", $ind, 5);
m("Facilities", "internal:/industries", "main", $ind, 6);
m("Healthcare", "internal:/industries", "main", $ind, 7);
m("Retail / Distribution", "internal:/industries", "main", $ind, 8);
m("Petro/Chem Oil and Gas", "internal:/industries", "main", $ind, 9);
m("Construction", "internal:/industries", "main", $ind, 10);
m("Events", "internal:/industries", "main", $ind, 11);

echo "--- Resources ---\n";
$res = m("Resources", "internal:/", "main", null, 3);
m("Promotions", "internal:/", "main", $res, 0);
m("BearCom Blog", "internal:/", "main", $res, 1);
m("Innovation", "internal:/", "main", $res, 2);

echo "--- About ---\n";
$about = m("About", "internal:/", "main", null, 4);
m("Our Story", "internal:/", "main", $about, 0);
m("Purpose", "internal:/", "main", $about, 1);
m("BearCom Companies", "internal:/", "main", $about, 2);
m("Locations", "internal:/locations", "main", $about, 3);
m("Careers", "internal:/", "main", $about, 4);
m("BearCom in the News", "internal:/", "main", $about, 5);

echo "\nDone! Created " . count(\Drupal::entityTypeManager()->getStorage("menu_link_content")->loadMultiple()) . " menu items.\n";
'

../vendor/bin/drush cr

echo ""
echo "========================================="
echo "  Main menu created successfully!"
echo "========================================="
