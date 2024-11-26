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
      $provinces[$province->id()] = $province->label();
    }

    return $provinces;
  }

  public function getAllCities(): array {
    $cities = [];

    $city_entities = $this->entityTypeManager
      ->getStorage('bricc_city')
      ->loadMultiple();

    foreach ($city_entities as $city) {
      $cities[$city->id()] = $city->label();
    }

    return $cities;
  }

}
