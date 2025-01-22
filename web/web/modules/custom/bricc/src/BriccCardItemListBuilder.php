<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the card item entity type.
 */
final class BriccCardItemListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
//    $header['status'] = $this->t('Status');
    $header['uid'] = $this->t('Author');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bricc\BriccCardItemInterface $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->toLink();
    $row['status'] = $entity->get('status')->value ? $this->t('Enabled') : $this->t('Disabled');

    // Check if the 'uid' field has a value and the referenced entity exists
    if ($entity->hasField('uid') && !$entity->get('uid')->isEmpty() && $entity->get('uid')->entity) {
      $user_entity = $entity->get('uid')->entity;

      // Safely determine if the user is authenticated
      $is_authenticated = $user_entity instanceof \Drupal\user\UserInterface && $user_entity->isAuthenticated();

      $username_options = [
        'label' => 'hidden',
        'settings' => ['link' => $is_authenticated],
      ];

      // Render the 'uid' field with the options
      $row['uid']['data'] = $entity->get('uid')->view($username_options);
    } else {
      // Handle cases where 'uid' or the referenced entity is missing
      $row['uid']['data'] = NULL;
    }

    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

  public function render(): array {
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\bricc\Form\BriccCardItemFilterForm');
    $build += parent::render();
    return $build;
  }

  protected function getEntityIds() {
    $query = \Drupal::entityQuery($this->entityTypeId);
    $request = \Drupal::request();

    $label = $request->get('label') ?? '';
    if (!empty($label)) {
      $query->condition('label', $label, 'CONTAINS');
    }

    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->accessCheck()->execute();
  }

}
