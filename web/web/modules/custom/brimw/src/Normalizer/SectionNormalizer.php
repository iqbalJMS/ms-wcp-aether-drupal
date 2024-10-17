<?php

namespace Drupal\brimw\Normalizer;

use Drupal\media\Entity\Media;

class SectionNormalizer extends BaseParagraphNormalizer {

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'section';

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

    if ($entity->bundle() === 'section') {
      $column_count = 1;
      if ($entity->hasField('field_column_per_row')) {
        if (!$entity->get('field_column_per_row')->isEmpty()) {
          $field_column_per_row = $entity->get('field_column_per_row')->value;
          $col_per_row = str_replace('-', '_', $field_column_per_row);
          $col_per_row_int = (int) filter_var($col_per_row, FILTER_SANITIZE_NUMBER_INT);
          $column_count = 12 / $col_per_row_int;
        }
      }

      $normalized['column_count'] = $column_count;
    }

    return $normalized;
  }
}
