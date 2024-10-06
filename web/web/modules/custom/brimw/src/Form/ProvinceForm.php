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
final class ProvinceForm extends FormBase {

  private $provinceId;

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
    return 'brimw_province';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Get the 'id' parameter from the current route.
    $this->provinceId = \Drupal::routeMatch()->getParameter('id');

    if ($this->provinceId) {
      $data = $this->locationRemoteData->getProvince($this->provinceId);

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
      ];
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $data['name'],
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t($this->provinceId ? 'Update' : 'Add'),
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
    $this->messenger()->addStatus($this->t('The province has been saved.'));
    $form_state->setRedirect('view.location.province');

    // Get the form values.
    $values = $form_state->getValues();

    if ($this->provinceId) {
      // Editing: Update existing location data.
      // Edit location
      $updated = $this->locationRemoteData->updateProvince($this->provinceId, $values['name']);
      if ($updated === TRUE) {
        \Drupal::messenger()->addMessage($this->t('Province has been updated.'));
      }
      else {
        \Drupal::messenger()->addError($this->t('Province has NOT been updated.'));
      }
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createProvince($values['name']);
      \Drupal::messenger()->addMessage($this->t('Province @name added successfully with ID: @id', ['@name' => $values['name'], '@id' => $new_id]));
    }
  }

}
