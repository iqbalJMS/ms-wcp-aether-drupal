<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the category entity type.
 */
final class BriccCategoryListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Title');
    $header['description'] = $this->t('Description');
    $header['card_count'] = $this->t('Card Count');
    $header['status'] = $this->t('Active');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bricc\BriccCategoryInterface $entity */
    $row['label'] = $entity->toLink();
    $row['description'] = $entity->get('description')->value;
    $row['card_count'] = 0;
    $row['status'] = $entity->get('status')->value ? $this->t('Active') : $this->t('Disabled');
    $username_options = [
      'label' => 'hidden',
      'settings' => ['link' => $entity->get('uid')->entity->isAuthenticated()],
    ];
    $row['uid']['data'] = $entity->get('uid')->view($username_options);
    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
//    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

  public function render(): array {
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\bricc\Form\BriccCategoryFilterForm');
    $build += parent::render();
    return $build;
  }

}
