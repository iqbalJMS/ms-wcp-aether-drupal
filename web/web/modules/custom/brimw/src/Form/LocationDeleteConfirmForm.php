<?php

declare(strict_types=1);

namespace Drupal\brimw\Form;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to confirm delete location
 */
final class LocationDeleteConfirmForm extends ConfirmFormBase {


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
    return 'brimw_location_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    return $this->t('Are you sure you want to DELETE this location?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('view.location.page_1');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $location_id = \Drupal::routeMatch()->getParameter('id');
    $form_state->setRedirectUrl(new Url('view.location.page_1'));

    try {
      $delete_status = $this->locationRemoteData->deleteLocation($location_id);
      $this->messenger()->addStatus($this->t('Location successfully deleted.'));
    }
    catch (\Exception $e) {
      $this->messenger()->addStatus($this->t('Unable to delete location.'));
    }
  }

}
