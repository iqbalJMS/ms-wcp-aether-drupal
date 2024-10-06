<?php

declare(strict_types=1);

namespace Drupal\brimw\Form;

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

  /**
   * Constructs a new LocationForm object.
   *
   * @param LocationRemoteData $locationRemoteData
   *   The remote data service.
   */
  public function __construct(LocationRemoteData $locationRemoteData) {
    $this->locationRemoteData = $locationRemoteData;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('brimw.location_remote_data')
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
      $data = [];
      $form['message'] = [
        '#markup' => 'Pending implementation'
      ];

      return $form;

      $form['id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('ID'),
        '#default_value' => $data['id'],
        '#required' => TRUE,
        '#attributes' => ['readonly' => 'readonly'], // Make the ID field read-only.
      ];
    }
    else {
      // Add mode
      $data = [
        'id' => '',
        'mid' => '',
        'name' => '',
        'address' => '',
        'phone' => '',
        'service' => '',
        'category' => '',
        'tipe' => '',
        'lat' => '',
        'long' => '',
      ];
    }

    $form['mid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MID'),
      '#default_value' => $data['mid'],
      '#required' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $data['name'],
      '#required' => TRUE,
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
      '#required' => TRUE,
    ];

    $form['service'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Service'),
      '#default_value' => $data['service'],
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#default_value' => $data['category'],
      '#required' => TRUE,
    ];

    $form['tipe'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type'),
      '#default_value' => $data['tipe'],
      '#required' => TRUE,
    ];

    $form['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $data['lat'],
      '#required' => TRUE,
    ];

    $form['long'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $data['long'],
      '#required' => TRUE,
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

    if ($this->locationId) {
      // Editing: Update existing location data.
      // TODO Edit location
      \Drupal::messenger()->addMessage($this->t('Location edit not implemented.'));
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->addLocation($values);
      \Drupal::messenger()->addMessage($this->t('Location added successfully with ID: @id', ['@id' => $new_id]));
    }
  }

}
