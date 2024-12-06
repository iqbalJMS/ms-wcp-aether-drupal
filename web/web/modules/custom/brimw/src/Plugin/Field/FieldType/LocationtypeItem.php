<?php

declare(strict_types=1);

namespace Drupal\brimw\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'brimw_locationtype' field type.
 *
 * @FieldType(
 *   id = "brimw_locationtype",
 *   label = @Translation("BRI Location Type"),
 *   description = @Translation("BRI Location Type field type."),
 *   default_widget = "brimw_location_type_widget",
 *   default_formatter = "brimw_location_type_formatter",
 * )
 */
final class LocationtypeItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return match ($this->get('type_id')->getValue()) {
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
    $properties['type_id'] = DataDefinition::create('string')
      ->setLabel(t('Location Type ID'))
      ->setRequired(TRUE);
    $properties['type_name'] = DataDefinition::create('string')
      ->setLabel(t('Location Type Name'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'type_id' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'Location Type ID.',
        'length' => 255,
      ],
      'type_name' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'Location Type Name.',
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
    $values['type_id'] = $random->word(random_int(10, 15));
    $values['type_name'] = $random->word(random_int(10, 15));
    return $values;
  }

}
