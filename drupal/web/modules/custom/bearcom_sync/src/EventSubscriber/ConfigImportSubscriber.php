<?php

namespace Drupal\bearcom_sync\EventSubscriber;

use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\structure_sync\StructureSyncHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Triggers structure_sync import after config import completes.
 */
class ConfigImportSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      ConfigEvents::IMPORT => ['onConfigImport', -100],
    ];
  }

  /**
   * Imports structure_sync data after config import.
   */
  public function onConfigImport(ConfigImporterEvent $event): void {
    $changelist = $event->getConfigImporter()->getStorageComparer()->getChangelist();

    // Check if structure_sync.data was created or updated.
    $dominated = array_merge(
      $changelist['create'] ?? [],
      $changelist['update'] ?? [],
    );

    if (!in_array('structure_sync.data', $dominated)) {
      return;
    }

    StructureSyncHelper::importTaxonomies(['style' => 'full']);
    StructureSyncHelper::importMenuLinks(['style' => 'full']);
    StructureSyncHelper::importCustomBlocks(['style' => 'full']);
  }

}
