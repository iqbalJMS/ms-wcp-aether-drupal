<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the province entity type.
 */
final class BriccProvinceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Province name');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\bricc\BriccProvinceInterface $entity */
    $row['label'] = $entity->label();

    $created_timestamp = $entity->get('created')->value;
    $formatted_date = \Drupal::service('date.formatter')->format($created_timestamp, 'custom', 'd M Y');

    // Set the formatted date in the row
    $row['created'] = $formatted_date;

    return $row + parent::buildRow($entity);
  }

  public function render(): array {
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\bricc\Form\BriccProvinceFilterForm');
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
