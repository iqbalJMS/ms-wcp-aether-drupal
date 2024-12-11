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
  private ?LocationRemoteData $locationRemoteData = NULL;

  private ?Location $location = NULL;

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

  public function updateCityField(array &$form, FormStateInterface $form_state) {
    return $form['id_city'];
  }

  public function updateCategoryField(array &$form, FormStateInterface $form_state) {
    return $form['category'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Get the 'id' parameter from the current route.
    $this->locationId = \Drupal::routeMatch()->getParameter('id');

    if (!$this->location instanceof Location) {
      $this->location = \Drupal::service('bricc.location');
    }

    if (!$this->locationRemoteData instanceof LocationRemoteData) {
      $this->locationRemoteData = \Drupal::service('brimw.location_remote_data');
    }

    if ($this->locationId) {
      // Edit mode

      $data = $this->locationRemoteData->getLocation($this->locationId);

      $form['id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('ID'),
        '#description' => $this->t('ID is read-only'),
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
        'category' => '',
        'phone' => '',
        'tipe' => '',
        'province' => '',
        'city' => '',
        'urlMaps' => '',
        'site' => '',
      ];
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $data['name'],
      '#required' => TRUE,
    ];

    $province_options = $this->location->getAllProvinces();

    $form['id_province'] = [
      '#type' => 'select',
      '#title' => $this->t('Province'),
      '#default_value' => $data['province'],
      '#options' => ['' => '-None -'] + $province_options,
      '#required' => FALSE,
      '#ajax' => [
        'callback' => [$this, 'updateCityField'],
        'event' => 'change',
        'wrapper' => 'city-wrapper',
      ],
    ];

    $city_options = [];
    $category_options = [];

    $triggering_element = $form_state->getTriggeringElement();
    if ($triggering_element) {
      if ($triggering_element['#name'] == 'id_province') {
        $uuid_province = empty($triggering_element) ? NULL : $triggering_element['#value'];
        $city_options = $this->location->getAllCities($uuid_province);
      }
      if ($triggering_element['#name'] == 'type') {
        $type_id = empty($triggering_element) ? NULL : $triggering_element['#value'];
        $category_options = $this->locationRemoteData->getCategoryByTypeOptions($type_id);
      }
    }
    else {
      if ($data['province']) {
        $city_options = $this->location->getAllCities($data['province']);
      }
      if ($data['tipe']) {
        $category_options = $this->locationRemoteData->getCategoryByTypeOptions($data['tipe']);
      }
    }

    $form['id_city'] = [
      '#type' => 'select',
      '#title' => $this->t('City'),
      '#default_value' => $data['city'],
      '#options' => ['' => '-None -'] + $city_options,
      '#required' => FALSE,
      '#prefix' => '<div id="city-wrapper">',
      '#suffix' => '</div>',
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
      '#default_value' => $data['phone'],
      '#required' => FALSE,
    ];

    $type_options = $this->locationRemoteData->getTypeOptions();
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#default_value' => $data['tipe'],
      '#options' => ['' => '-None -'] + $type_options,
      '#required' => FALSE,
      '#ajax' => [
        'callback' => [$this, 'updateCategoryField'],
        'event' => 'change',
        'wrapper' => 'category-wrapper',
      ],
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#default_value' => $data['category'],
      '#options' => ['' => '-None -'] + $category_options,
      '#required' => FALSE,
      '#prefix' => '<div id="category-wrapper">',
      '#suffix' => '</div>',
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

    $form['url_maps'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL Maps'),
      '#default_value' => $data['urlMaps'],
      '#required' => FALSE,
    ];

    $form['site'] = [
      '#type' => 'select',
      '#title' => $this->t('Site'),
      '#options' => [
        '' => $this->t('- Select -'),
        'bri_main' => $this->t('BRI Main'),
        'bri_promo' => $this->t('BRI Promo'),
      ],
      '#default_value' => $data['site'],
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

    if (!$this->locationRemoteData instanceof LocationRemoteData) {
      $this->locationRemoteData = \Drupal::service('brimw.location_remote_data');
    }

    $this->messenger()->addStatus($this->t('The location has been saved.'));
    $form_state->setRedirect('view.location.page_1');

    // Get the form values.
    $values = $form_state->getValues();
    $empty_string_value = [
      'category',
      'phone',
      'zip',
      'lat',
      'long',
      'city',
    ];
    foreach ($empty_string_value as $formkey) {
      if (!isset($values[$formkey])) {
        $values[$formkey] = "";
      }
    }

    if (!isset($values['lat'])) {
      $values['lat'] = 0;
    }
    if (!isset($values['long'])) {
      $values['long'] = 0;
    }

    if (!isset($values['url_maps'])) {
      if (!empty($values['lat']) && !empty($values['long'])) {
        // Format: https://www.google.com/maps/search/?api=1&query=<lat>,<lng>
        $values['url_maps'] = 'https://www.google.com/maps/search/?api=1&query=' . $values['lat'] . '&' . $values['long'];
      }
    }

    if ($this->locationId) {
      // Editing: Update existing location data.
      $update_status = $this->locationRemoteData->updateLocation($this->locationId, $values);
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createLocation($values);
      \Drupal::messenger()->addMessage($this->t('Location added successfully with ID: @id', ['@id' => $new_id]));
    }
  }

}
