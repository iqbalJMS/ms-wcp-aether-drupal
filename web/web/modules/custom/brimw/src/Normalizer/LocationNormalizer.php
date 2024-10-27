<?php

namespace Drupal\brimw\Normalizer;

use Drupal\media\Entity\Media;

class LocationNormalizer extends BaseParagraphNormalizer {

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = [
    'location', 'map'
  ];

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
      // Location Type detail from location service
      if ($entity->hasField('field_bri_location_type')) {
        if (!$entity->get('field_bri_location_type')->isEmpty()) {
          $values = $entity->get('field_bri_location_type')->getValue();
          $location_type_details = [];
          $type_ids = [];
          foreach ($values as $value) {
            $type_ids = $value['type_id'];
            $location_type_details[] = \Drupal::service('brimw.location_remote_data')->getType($value['type_id']);
          }
          $normalized['location_type_details'] = $location_type_details;

          // Fetch icon
          $query = \Drupal::entityQuery('taxonomy_term')
            ->accessCheck(FALSE)
            ->condition('vid', 'location_type', '=')
            ->condition('field_bri_location_type.type_id', $type_ids, 'IN');
          $term_ids = $tids = $query->execute();
          $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($term_ids);
          $terms_normalized = $this->serializer->normalize($terms, 'json_recursive');
          $term_images = [];
          foreach ($terms_normalized as $term_item_normalized) {
            if (isset($term_item_normalized['field_bri_location_type'][0]['type_id'])) {
              if (isset($term_item_normalized['field_image'][0]['field_media_image'][0]['uri'][0]['url'])) {
                $term_images[$term_item_normalized['field_bri_location_type'][0]['type_id']] =
                  $term_item_normalized['field_image'][0]['field_media_image'][0]['uri'][0]['url'];
              }
            }
          }

          foreach ($normalized['location_type_details'] as &$type_item) {
            if (isset($term_images[$type_item['id']])) {
              $type_item['image_url'] = $term_images[$type_item['id']];
            }
          }



        }
      }
    }
    elseif ($entity->bundle() === 'map') {
      if ($entity->hasField('field_bri_location')) {
        if (!$entity->get('field_bri_location')->isEmpty()) {
          $values = $entity->get('field_bri_location')->first()->getValue();
          $normalized['location_detail'] = \Drupal::service('brimw.location_remote_data')->getLocation($values['location_id']);
        }
      }
    }

    return $normalized;
  }
}
