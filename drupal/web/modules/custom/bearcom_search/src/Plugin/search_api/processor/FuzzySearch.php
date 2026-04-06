<?php

namespace Drupal\bearcom_search\Plugin\search_api\processor;

use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Query\QueryInterface;

/**
 * Adds fuzzy matching by generating truncated variants of search keywords.
 *
 * For each keyword longer than 3 chars, adds progressively shorter prefixes
 * as OR alternatives. E.g. "bate" → "bate" OR "bat", so "bat" matches
 * "battery" via LIKE '%bat%'.
 *
 * @SearchApiProcessor(
 *   id = "fuzzy_search",
 *   label = @Translation("Fuzzy search (truncation)"),
 *   description = @Translation("Adds truncated keyword variants for typo-tolerant matching."),
 *   stages = {
 *     "preprocess_query" = -1,
 *   },
 * )
 */
class FuzzySearch extends ProcessorPluginBase {

  /**
   * Minimum truncated length.
   */
  const MIN_LENGTH = 3;

  /**
   * {@inheritdoc}
   */
  public function preprocessSearchQuery(QueryInterface $query) {
    $keys = &$query->getKeys();
    if (!is_array($keys)) {
      return;
    }
    $this->expandKeys($keys);
  }

  /**
   * Recursively expand keys with truncated variants.
   */
  protected function expandKeys(array &$keys) {
    foreach ($keys as $i => &$key) {
      if ($i === '#conjunction') {
        continue;
      }
      if (is_array($key)) {
        $this->expandKeys($key);
        continue;
      }
      if (!is_string($key)) {
        continue;
      }
      $len = mb_strlen($key);

      // Only expand words longer than minimum length.
      if ($len <= self::MIN_LENGTH) {
        continue;
      }

      // Create OR group: original + truncated by 1 char.
      // "bate" → "bate" OR "bat"
      // "bater" → "bater" OR "bate" OR "bat"  (but cap at 1-2 truncations)
      $or_group = [
        '#conjunction' => 'OR',
        $key,
      ];

      // Add 1-2 truncated variants (remove last chars down to MIN_LENGTH).
      $max_truncations = min(2, $len - self::MIN_LENGTH);
      for ($t = 1; $t <= $max_truncations; $t++) {
        $or_group[] = mb_substr($key, 0, $len - $t);
      }

      $keys[$i] = $or_group;
    }
  }

}
