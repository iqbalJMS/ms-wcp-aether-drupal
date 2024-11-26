<?php

declare(strict_types=1);

namespace Drupal\brimw\Form;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a brimw form.
 */
final class LocationCategoryForm extends FormBase {

  /**
   * Location ID (null for adding new data, set for editing existing data).
   *
   * @var string|null
   */
  protected $categoryId;

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
    return 'brimw_location_category';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Get the 'id' parameter from the current route.
    $this->categoryId = \Drupal::routeMatch()->getParameter('id');

    if ($this->categoryId) {
      $data = $this->locationRemoteData->getCategory($this->categoryId);

      // Edit
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
      // Add
      $data = [
        'id' => '',
        'name' => '',
        'type' => [
          'id' => ''
        ],
      ];
    }

    $location_types_data = $this->locationRemoteData->getAllLocationType([
      'limit' => 999
    ]);

    // Iterate over the terms and retrieve their IDs and names.
    $location_types = [];
    foreach ($location_types_data['data'] as $type) {
      $location_types[$type['id']] = $type['name'];
    }

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#required' => TRUE,
      '#options' => $location_types,
      '#default_value' => $data['type']['id'],
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
        '#value' => $this->t('Send'),
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
    $form_state->setRedirect('view.location.category');

    // Get the form values.
    $values = $form_state->getValues();

    if ($this->categoryId) {

      try {
        $update_status = $this->locationRemoteData->updateCategory($this->categoryId, $values['name'], $values['type']);
        if ($update_status) {
          \Drupal::messenger()->addMessage($this->t('Category updated successfully'));
        }
        else {
          \Drupal::messenger()->addWarning($this->t('Unable to update Location category'));
        }
      }
      catch (\Exception $e) {
        \Drupal::messenger()->addWarning($this->t('Unable to update Location category. Error: @error', ['@error' => $e->getMessage()]));
      }

    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createCategory($values['type'], $values['name']);
      \Drupal::messenger()->addMessage($this->t('Category @name added successfully with ID: @id', ['@name' => $values['name'], '@id' => $new_id]));
    }
  }

}
