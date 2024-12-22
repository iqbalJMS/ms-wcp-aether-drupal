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
        '#type' => 'hidden',
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
        'province' => 'none',
        'city' => 'none',
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

    $province_options = ['none' => '-None -'] + $this->location->getAllProvinces();
    if (empty($form_state->getValue('id_province'))) {
      if (isset($data['province']) && $data['province'] !== 'none') {
        $selected_province = $data['province'];
      }
      else {
        $selected_province = key($province_options);
      }
    }
    else {
      // Get the value if it already exists.
      $selected_province = $form_state->getValue('id_province');
    }

    $form['id_province'] = [
      '#type' => 'select',
      '#title' => $this->t('Province'),
      '#default_value' => $selected_province,
      '#options' => $province_options,
      '#required' => FALSE,
      '#ajax' => [
        'callback' => [$this, 'updateCityField'],
        'event' => 'change',
        'wrapper' => 'city-wrapper',
      ],
    ];

    $city_options = [];
    $category_options = [];

    $city_options = ['none' => '-None -'] + $this->location->getAllCities($selected_province);

    if (empty($form_state->getValue('id_city'))) {
      if (isset($data['city']) && $data['city'] !== 'none') {
        $selected_city = $data['city'];
      }
      else {
        $selected_city = key($city_options);
      }
    }
    else {
      $selected_city =  $form_state->getValue('id_city');
    }

    $form['id_city'] = [
      '#type' => 'select',
      '#title' => $this->t('City'),
      '#options' => $city_options,
      '#required' => FALSE,
      '#prefix' => '<div id="city-wrapper">',
      '#suffix' => '</div>',
      '#default_value' => $selected_city,
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

    $type_options = ['none' => '-None -'] + $this->locationRemoteData->getTypeOptions();
    if (empty($form_state->getValue('type'))) {
      if (isset($data['tipe']) && $data['tipe'] !== 'none') {
        $selected_type = $data['tipe'];
      }
      else {
        $selected_type = key($type_options);
      }
    }
    else {
      // Get the value if it already exists.
      $selected_type = $form_state->getValue('type');
    }

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#default_value' => $selected_type,
      '#options' => $type_options,
      '#required' => FALSE,
      '#ajax' => [
        'callback' => [$this, 'updateCategoryField'],
        'event' => 'change',
        'wrapper' => 'category-wrapper',
      ],
    ];

    $category_options = ['none' => '-None -'] + $this->locationRemoteData->getCategoryByTypeOptions($selected_type);
    if (empty($form_state->getValue('category'))) {
      if (isset($data['category']) && $data['category'] !== 'none') {
        $selected_category = $data['category'];
      }
      else {
        $selected_category = key($category_options);
      }
    }
    else {
      // Get the value if it already exists.
      $selected_category = $form_state->getValue('category');
    }
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#default_value' => $selected_category,
      '#options' => $category_options,
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
        'HOME' => $this->t('BRI Home'),
        'PRIORITAS' => $this->t('BRI Prioritas'),
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
      $sanitized_name = sprintf('%s', $values['name']);
      \Drupal::messenger()->addMessage($this->t('Location @name added successfully', ['@name' => $sanitized_name]));
    }
  }

}
