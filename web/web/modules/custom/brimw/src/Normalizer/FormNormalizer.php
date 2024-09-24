<?php

namespace Drupal\brimw\Normalizer;

use Drupal\webform\Entity\Webform;

class FormNormalizer extends BaseParagraphNormalizer {

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'form';

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

    if ($entity->bundle() === 'form') {
      if ($entity->hasField('field_form')) {
        $field_form = $entity->get('field_form')->first()->getValue();
        if (isset($field_form['target_id'])) {
          $webform_id = $field_form['target_id'];
          $webform = Webform::load($webform_id);
          if ($webform) {

            // Grab the form in its entirety.
            $form = $webform->getSubmissionForm();
            $normalized['webform_elements'] = $form['elements'];
          }
        }
      }
    }

    return $normalized;
  }
}
