<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldWidget;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'brimw_bri_location_widget' field widget.
 *
 * @FieldWidget(
 *   id = "brimw_bri_location_widget",
 *   label = @Translation("BRI Location Widget"),
 *   field_types = {"brimw_bri_location"},
 * )
 */
class BriLocationWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly LocationRemoteData $locationRemoteData
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('brimw.location_remote_data'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $default_value = '';
    if (!empty($items[$delta]->location_id)) {
      $default_value = sprintf('%s (%s)', $items[$delta]->location_name, $items[$delta]->location_id);
    }

    $element['data'] = [
      '#title' => t('BRI Location'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'brimw.location.autocomplete',
      '#autocomplete_route_parameters' => [],
      '#placeholder' => t('Select location ID'),
      '#default_value' => $default_value,
      '#element_validate' => [
        [BriLocationWidget::class, 'validate'],
      ],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $item = NULL;

    foreach ($values as $delta => &$item) {
      $item['delta'] = $delta;

      // Take "label (entity id)", match the ID from inside the parentheses.
      // @see \Drupal\Core\Entity\Element\EntityAutocomplete::extractEntityIdFromAutocompleteInput
      if (preg_match('/(.+\\s)\\(([^\\)]+)\\)/', $item['data'], $matches)) {
        $item['location_name'] = trim($matches[1]);
        $item['location_id'] = trim($matches[2]);
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

    $isLocationIdValid = \Drupal::service('brimw.location_remote_data')->validateLocationId($id);

    if ($isLocationIdValid === FALSE) {
      $form_state->setError($element, t('Location ID is not valid.'));
    }
  }

}
