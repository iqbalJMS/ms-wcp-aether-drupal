<?php

declare(strict_types=1);

namespace Drupal\bricc\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Plugin implementation of the 'Media image URL' formatter.
 *
 * @FieldFormatter(
 *   id = "bricc_media_image_url",
 *   label = @Translation("Media image URL"),
 *   field_types = {"entity_reference"}
 * )
 */
final class MediaImageUrlFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable($field_definition) {
    // Check if the field is an entity reference and if it references the media entity type.
    $target_type = $field_definition->getSetting('target_type');
    return $target_type === 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      $media = Media::load($item->target_id);
      if ($media instanceof Media) {
        if (!$media->get('field_media_image')->isEmpty()) {
          $file_id = $media->get('field_media_image')->target_id;
          $file = File::load($file_id);
          $file_uri = $file->getFileUri();
          $url = \Drupal::service('file_url_generator')->generateString($file_uri);
          $elements[$delta] = [
            '#markup' => $url,
          ];
        }
      }
    }

    return $elements;
  }

}
