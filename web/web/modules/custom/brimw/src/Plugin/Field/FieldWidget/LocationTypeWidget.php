<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'brimw_location_type_widget' field widget.
 *
 * @FieldWidget(
 *   id = "brimw_location_type_widget",
 *   label = @Translation("Location Type Widget"),
 *   field_types = {"brimw_locationtype"},
 * )
 */
final class LocationTypeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $default_value = '';
    if (!empty($items[$delta]->type_id)) {
      $default_value = sprintf('%s (%s)', $items[$delta]->type_name, $items[$delta]->type_id);
    }

    $element['data'] = [
      '#title' => t('BRI Location Type'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'brimw.location.type.autocomplete',
      '#autocomplete_route_parameters' => [],
      '#placeholder' => t('Select location type ID'),
      '#default_value' => $default_value,
      '#element_validate' => [
        [LocationTypeWidget::class, 'validate'],
      ],
    ];
    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $item = NULL;

    foreach ($values as $delta => $item) {
      $item['delta'] = $delta;

      // Take "label (entity id)", match the ID from inside the parentheses.
      // @see \Drupal\Core\Entity\Element\EntityAutocomplete::extractEntityIdFromAutocompleteInput
      if (preg_match('/(.+\\s)\\(([^\\)]+)\\)/', $item['data'], $matches)) {
        $item['type_name'] = trim($matches[1]);
        $item['type_id'] = trim($matches[2]);
      }
    }

    return $values;
  }

  public static function validate($element, FormStateInterface $form_state): void {
    $value = $element['#value'];
    $id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($value);

    if (empty($id)) {
      $form_state->setValueForElement($element, '');

      return;
    }

    $isLocationIdValid = \Drupal::service('brimw.location_remote_data')->validateLocationTypeId($id);

    if ($isLocationIdValid === FALSE) {
      $form_state->setError($element, t('Location Type ID is not valid.'));
    }
  }

}
