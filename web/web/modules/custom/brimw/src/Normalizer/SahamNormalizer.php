<?php

namespace Drupal\brimw\Normalizer;

class SahamNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'bbri_stock_market';

  /**
   * @inheritDoc
   */
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    $normalized['data'] = \Drupal::service('brimw.kurs_remote_data')->getStock();

    return $normalized;
  }
}
