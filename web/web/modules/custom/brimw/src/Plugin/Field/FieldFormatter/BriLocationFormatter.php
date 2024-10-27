<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'BRI Location Formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "brimw_bri_location_formatter",
 *   label = @Translation("BRI Location Formatter"),
 *   field_types = {"brimw_bri_location"},
 * )
 */
final class BriLocationFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->location_name,
      ];
    }
    return $element;
  }

}
