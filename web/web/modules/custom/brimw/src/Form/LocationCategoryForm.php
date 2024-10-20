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
        'type' => '',
      ];
    }

    // Define the vocabulary ID.
    $vocabulary_id = 'location_type';

    // Load all terms from the specified vocabulary.
    $terms = \Drupal::entityQuery('taxonomy_term')
      ->accessCheck(FALSE)
      ->condition('vid', $vocabulary_id)
      ->execute();

    // Load term entities based on the query results.
    $terms_loaded = Term::loadMultiple($terms);

    // Iterate over the terms and retrieve their IDs and names.
    $location_types = [];
    foreach ($terms_loaded as $term) {
      $location_types[$term->id()] = $term->getName();
    }

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#required' => TRUE,
      '#options' => $location_types,
      '#default_value' => $data['type'],
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
      // Editing: Update existing location data.
      // TODO Edit category
      \Drupal::messenger()->addMessage($this->t('Category edit not implemented.'));
    } else {
      // Adding: Insert new location data.
      $new_id = $this->locationRemoteData->createCategory($values['type'], $values['name']);
      \Drupal::messenger()->addMessage($this->t('Category @name added successfully with ID: @id', ['@name' => $values['name'], '@id' => $new_id]));
    }
  }

}
