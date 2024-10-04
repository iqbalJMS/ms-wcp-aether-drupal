<?php

namespace Drupal\brimw\Normalizer;

use Drupal\media\Entity\Media;

class LocationNormalizer extends BaseParagraphNormalizer {

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'location';

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

    if ($entity->bundle() === 'location') {
      // Get location type
      $vocabulary_id = 'location_type';
      $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'vid' => $vocabulary_id,
      ]);

      // Get image and label
      $location_types = [];
      foreach ($terms as $term) {
        // Example: Get term name and ID.
        $type_item['name'] = $term->getName();
        $type_item['term_id'] = $term->id();

        if (!$term->get('field_location_type')->isEmpty()) {
          $type_item['type_id'] = $term->get('field_location_type')->value;
        }

        if ($term->hasField('field_image')) {
          if (!$term->get('field_image')->isEmpty()) {
            $media_image = $term->get('field_image')->referencedEntities()[0];
            if ($media_image instanceof Media) {
              if (!$media_image->get('field_media_image')->isEmpty()) {
                /** @var \Drupal\file\Entity\File $file_image */
                $file_image = $media_image->get('field_media_image')->referencedEntities()[0];
                $type_item['icon'] = $file_image->createFileUrl();
              }
            }
          }
        }

        $location_types[] = $type_item;
      }

      $normalized['location_types'] = $location_types;
    }

    return $normalized;
  }
}
