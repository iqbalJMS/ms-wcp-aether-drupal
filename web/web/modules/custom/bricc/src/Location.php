<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;

/**
 * Location service for data in Drupal
 */
final class Location {

  /**
   * Constructs a Location object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LoggerChannelInterface $briccLoggerChannel,
  ) {}

  /**
   * Get all province
   */
  public function getAllProvinces(): array {
    $provinces = [];

    // Load all province entities.
    $province_entities = $this->entityTypeManager
      ->getStorage('bricc_province')
      ->loadMultiple();

    // Format the array.
    foreach ($province_entities as $province) {
      $provinces[$province->uuid()] = $province->label();
    }

    return $provinces;
  }

  public function getAllCities($province_uuid = NULL): array {
    $cities = [];

    if ($province_uuid === 'none') {
      $province_uuid = NULL;
    }

    if (!is_null($province_uuid)) {

      $province_entities = $this->entityTypeManager->getStorage('bricc_province')->loadByProperties(
        ['uuid' => $province_uuid]
      );
      $province_entity = reset($province_entities);
      if ($province_entity) {
        $province = $province_entity->id();

        $city_entity_ids = $this->entityTypeManager
          ->getStorage('bricc_city')
          ->getQuery()
          ->accessCheck(FALSE)
          ->condition('field_province.target_id', $province)
          ->execute();

        $city_entities = $this->entityTypeManager
          ->getStorage('bricc_city')
          ->loadMultiple($city_entity_ids);

        foreach ($city_entities as $city) {
          $cities[$city->uuid()] = $city->label();
        }
      }
    }

    return $cities;
  }

}
