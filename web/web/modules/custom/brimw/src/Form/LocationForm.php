<?php

declare(strict_types=1);

namespace Drupal\brimw\Form;

use Drupal\bricc\Location;
use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a brimw form.
 */
final class LocationForm extends FormBase {

  /**
   * Location ID (null for adding new data, set for editing existing data).
   *
   * @var string|null
   */
  protected $locationId;

  /**
   * @var \Drupal\brimw\External\LocationRemoteData
   */
  private LocationRemoteData $locationRemoteData;

  private Location $location;

  /**
   * Constructs a new LocationForm object.
   *
   * @param LocationRemoteData $locationRemoteData
   *   The remote data service.
   */
  public function __construct(LocationRemoteData $locationRemoteData, Location $location) {
    $this->locationRemoteData = $locationRemoteData;
    $this->location = $location;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('brimw.location_remote_data'),
      $container->get('bricc.location'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'brimw_location';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Get the 'id' parameter from the current route.
    $this->locationId = \Drupal::routeMatch()->getParameter('id');

    if ($this->locationId) {
      // Edit mode

      $data = $this->locationRemoteData->getLocation($this->locationId);

      // Area
      if (isset($data['area']) && is_array($data['area'])) {
        foreach ($data['area'] as $area) {
          if ($area['key'] === 'city') {
            $data['zip'] = $area['zip'];
          }

          $data[$area['key']] = [
            'id' => $area['_id'],
            'name' => $area['value'],
          ];
        }
      }

      $form['id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('ID'),
        '#title' => $this->t('ID is read-only'),
        '#default_value' => $data['id'],
        '#required' => TRUE,
        '#attributes' => ['readonly' => 'readonly'], // Make the ID field read-only.
      ];
    }
    else {
      // Add mode
      $data = [
        'id' => '',
        'name' => '',
        'address' => '',
        'lat' => '',
        'long' => '',
        'zip' => '',
        'data' => [
          'category' => '',
          'mid' => '',
          'tid' => '',
          'service' => '',
          'phone' => '',
          'tipe' => '',
        ],
        'province' => [
          'id' => '',
          'name' => '',
        ],
        'city' => [
          'id' => '',
          'name' => '',
        ],
        'area' => [
          [
            '_id' => '',
            'key' => '',
            'value' => '',
            'zip' => '',
          ],
          [
            '_id' => '',
            'key' => '',
            'value' => '',
            'zip' => '',
          ],
        ],
      ];
    }

    $form['mid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MID'),
      '#default_value' => $data['data']['mid'],
      '#required' => FALSE,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $data['name'],
      '#required' => TRUE,
    ];

//    $all_provinces = $this->locationRemoteData->getAllProvinces([
//      'skip' => 0,
//      'limit' => 99,
//    ]);
//    $province_options = array_column($all_provinces['data'], 'name', 'id');
    $province_options = $this->location->getAllProvinces();

    $form['id_province'] = [
      '#type' => 'select',
      '#title' => $this->t('Province'),
      '#default_value' => $data['province']['id'],
      '#options' => ['' => '-None -'] + $province_options,
      '#required' => TRUE,
    ];

//    $all_cities = $this->locationRemoteData->getAllCities([
//      'skip' => 0,
//      'limit' => 99,
//    ]);
//    $city_options = array_column($all_cities['data'], 'name', 'id');
    $city_options = $this->location->getAllCities();

    $form['id_city'] = [
      '#type' => 'select',
      '#title' => $this->t('City'),
      '#default_value' => $data['city'],
      '#options' => ['' => '-None -'] + $city_options,
      '#required' => FALSE,
    ];

    $form['zip'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ZIP code'),
      '#default_value' => $data['zip'],
      '#required' => FALSE,
    ];

    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $data['address'],
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#default_value' => $data['data']['phone'],
      '#required' => FALSE,
    ];

    $form['service'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Service'),
      '#default_value' => $data['data']['service'],
      '#required' => FALSE,
    ];

    $type_options = $this->locationRemoteData->getTypeOptions();
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#default_value' => $data['data']['tipe'],
      '#options' => ['' => '-None -'] + $type_options,
      '#required' => TRUE,
    ];

    $category_options = $this->locationRemoteData->getCategoryOptions();
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#default_value' => $data['data']['category'],
      '#options' => ['' => '-None -'] + $category_options,
      '#required' => FALSE,
    ];

    $form['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $data['lat'],
      '#required' => FALSE,
    ];

    $form['long'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $data['long'],
      '#required' => FALSE,
    ];

    // Add submit button.
    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t($this->locationId ? 'Update' : 'Add'),
      ],
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('The location has been saved.'));
    $form_state->setRedirect('view.location.page_1');

    // Get the form values.
    $values = $form_state->getValues();
    if (!isset($values['tid'])) {
      $values['tid'] = '';
    }
    $empty_string_value = [
      'mid',
      'tid',
      'category',
      'service',
      'phone',
      'zip',
      'lat',
      'long',
      'id_city',
    ];
    foreach ($empty_string_value as $formkey) {
      if (!isset($values[$formkey])) {
        $values[$formkey] = "";
      }
    }

    $values['url_maps'] = '';
    if (isset($values['lat']) && isset($values['long'])) {
      // Format: https://www.google.com/maps/search/?api=1&query=<lat>,<lng>
      $values['url_maps'] = 'https://www.google.com/maps/search/?api=1&query=' . $values['lat'] . '&' . $values['long'];
    }

    if ($this->locationId) {
      // Editing: Update existing location data.
      $location_data = [];
      $update_status = $this->locationRemoteData->updateLocation($this->locationId, $values);
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createLocation($values);
      \Drupal::messenger()->addMessage($this->t('Location added successfully with ID: @id', ['@id' => $new_id]));
    }
  }

}
