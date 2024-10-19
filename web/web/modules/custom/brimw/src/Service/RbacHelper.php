<?php

declare(strict_types=1);

namespace Drupal\brimw\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Session\AccountProxy;

/**
 * Helper service related to RBAC.
 */
final class RbacHelper {

  /**
   * Constructs a RbacHelper object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CacheBackendInterface $cacheData,
    private readonly AccountProxy $currentUser,
    private readonly LoggerChannelInterface $briccLoggerChannel,
  ) {}

  /**
   * Get all site id and roles
   */
  public function getAllSiteIdRoles(): array {
    $cache_key = 'bri:rbac_helper';

    if ($cache = $this->cacheData->get($cache_key)) {
      return $cache->data;
    }
    else {
      // Get all term of Site ID
      $vocabulary_id = 'site_id';

      // Load terms from the vocabulary
      $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary_id, 0, NULL, TRUE);
      $data = [];

      foreach ($terms as $term) {
        // Access term properties, like term name and ID
        $term_name = $term->label();
        $term_id = $term->id();

        $role = [];
        if ($term->hasField('field_role')) {
          if (!$term->get('field_role')->isEmpty()) {
            $role = $term->get('field_role')->target_id;
          }
        }

        $data[] = [
          'id' => $term_id,
          'name' => $term_name,
          'role' => $role,
        ];

      }

      $this->cacheData->set($cache_key, $data);
      return $data;
    }
  }

  public function getSiteIdByRole() {
    $siteIdRoles = $this->getAllSiteIdRoles();
    foreach ($siteIdRoles as $siteIdRole) {
      if ($this->currentUser->hasRole($siteIdRole['role'] ?: '')) {
        return $siteIdRole['id'];
      }
    }

    return NULL;
  }

}
