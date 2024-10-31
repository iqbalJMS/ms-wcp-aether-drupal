<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Location Type Formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "brimw_location_type_formatter",
 *   label = @Translation("Location Type Formatter"),
 *   field_types = {"brimw_locationtype"},
 * )
 */
final class LocationTypeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->type_name,
      ];
    }
    return $element;
  }

}
