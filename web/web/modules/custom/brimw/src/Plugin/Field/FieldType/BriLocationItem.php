<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'brimw_bri_location' field type.
 *
 * @FieldType(
 *   id = "brimw_bri_location",
 *   label = @Translation("BRI Location"),
 *   description = @Translation("Store BRI location data."),
 *   default_widget = "brimw_bri_location_widget",
 *   default_formatter = "brimw_bri_location_formatter",
 * )
 */
final class BriLocationItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return match ($this->get('location_id')->getValue()) {
      NULL, '' => TRUE,
      default => FALSE,
    };
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    // @DCG
    // See /core/lib/Drupal/Core/TypedData/Plugin/DataType directory for
    // available data types.
    $properties['location_id'] = DataDefinition::create('string')
      ->setLabel(t('BRI Location ID'))
      ->setRequired(TRUE);

    $properties['location_name'] = DataDefinition::create('string')
      ->setLabel(t('BRI Location Name'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'location_id' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'BRI Location ID.',
        'length' => 255,
      ],
      'location_name' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'BRI Location Name.',
        'length' => 255,
      ],
    ];

    $schema = [
      'columns' => $columns,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();
    $values['location_id'] = '6yj3jh5u9iojx2ub' . \Drupal\Component\Datetime\time();
    $values['location_name'] = $random->word(mt_rand(10, 20));
    return $values;
  }

}
