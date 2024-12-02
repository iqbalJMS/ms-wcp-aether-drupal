<?php

namespace Drupal\brimw\Normalizer;

class KursNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'kurs';

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

    $allowed_currencies = array_column($entity->field_currency->getValue(), 'value');

    if (!$allowed_currencies) {

      $data = array_filter(\Drupal::service('brimw.kurs_remote_data')->getKurs(), function ($data) use ($allowed_currencies) {
        return $data['isShow'];
      });
      
      $normalized['data'] = array_values($data);

    } else {

      $data = array_filter(\Drupal::service('brimw.kurs_remote_data')->getKurs()['data'], function ($data) use ($allowed_currencies) {
        return in_array($data['currency'], $allowed_currencies);
      });
      
      $normalized['data'] = array_values($data);

    }
      
    $normalized['note'] = \Drupal::service('brimw.kurs_remote_data')->getKurs()['note'];

    $normalized['available_currency'] = array_merge(
      ['IDR'],
      array_column(\Drupal::service('brimw.kurs_remote_data')->getKurs()['data'], 'currency')
    );

    return $normalized;
  }
}
