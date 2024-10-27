<?php

namespace Drupal\brimw\Normalizer;

use Drupal\media\Entity\Media;

class ExternalMagazineNormalizer extends BaseParagraphNormalizer {

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'external_magazine';

  /**
   * @inheritDoc
   */
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array|string|int|float|bool|\ArrayObject|null {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    if ($entity->bundle() === 'external_magazine') {
      if ($entity->hasField('field_display')) {
        if (!$entity->get('field_display')->isEmpty()) {
          $field_display = $entity->get('field_display')->value;
          if ($field_display === 'last_4_magazines') {
            // Fetch last four
            $normalized['endpoint_path'] = '/api/bri/external-magazines/last_4_magazines';
          }
          elseif ($field_display === 'all') {
            // Fetch all
            $normalized['endpoint_path'] = '/api/bri/external-magazines/all';
          }
        }
      }
    }

    return $normalized;
  }
}
