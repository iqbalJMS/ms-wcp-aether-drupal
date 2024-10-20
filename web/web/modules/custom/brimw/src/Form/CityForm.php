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
final class CityForm extends FormBase {
  /**
   * Location ID (null for adding new data, set for editing existing data).
   *
   * @var string|null
   */
  protected $cityId;

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
    return 'brimw_city';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Get the 'id' parameter from the current route.
    $this->cityId = \Drupal::routeMatch()->getParameter('id');

    if ($this->cityId) {
      $data = $this->locationRemoteData->getCity($this->cityId);

      // Edit
      $form['id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('ID'),
        '#default_value' => $data['id'],
        '#required' => TRUE,
        '#attributes' => ['readonly' => 'readonly'], // Make the ID field read-only.
      ];
    }
    else {
      // Add
      $data = [
        'id' => '',
        'name' => '',
        'province' => [
          'id' => '',
          'name' => '',
        ],
      ];
    }

    $all_provinces = $this->locationRemoteData->getAllProvinces([
      'skip' => 0,
      'limit' => 99,
    ]);
    $provinces = array_column($all_provinces['data'], 'name', 'id');

    $form['id_province'] = [
      '#type' => 'select',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#options' => $provinces,
      '#default_value' => $data['province']['id'],
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#default_value' => $data['name'],
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
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
    // Save city
    $form_state->setRedirect('view.location.city');

    // Get the form values.
    $values = $form_state->getValues();

    if ($this->cityId) {
      // Editing: Update existing location data.
      // TODO Edit city
      \Drupal::messenger()->addMessage($this->t('City edit not implemented.'));
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createCity($values['id_province'], $values['name']);
      \Drupal::messenger()->addMessage($this->t('City @name added successfully with ID: @id', ['@name' => $values['name'], '@id' => $new_id]));
    }
  }

}
