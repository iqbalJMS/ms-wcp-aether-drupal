<?php

namespace Drupal\bricc\Normalizer;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\config_pages\Entity\ConfigPagesType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

/**
 * NOTE This class is called CardSliderNormalizer, but it handles a lot more than
 * normalizing CardSlider.
 */
class CardSliderNormalizer extends ContentEntityNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\paragraphs\ParagraphInterface';

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphTypes = ['card_slider', 'card_group_nav', 'card_detail', 'card_popular'];

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $em;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->em = $entityTypeManager;
  }

  /**
   * @inheritDoc
   */
  public function supportsNormalization(
    $data,
    ?string $format = NULL,
    array $context = []
  ): bool {
    if (parent::supportsNormalization($data, $format)) {
      if (empty($this->supportedParagraphTypes)) {
        return TRUE;
      }
      if (in_array($data->getType(), $this->supportedParagraphTypes)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * @inheritDoc
   */
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array|string|int|float|bool|\ArrayObject|null {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    if ($entity->bundle() == 'card_slider') {
      if ($entity->hasField('field_category')) {
        if (!$entity->get('field_category')->isEmpty()) {
          $field_category = $entity->get('field_category')->target_id;
          // Get card item data based on category field.
          $card_items = $this->em->getStorage('bricc_card_item')->loadByProperties([
            'field_category' => $field_category,
          ]);
          $normalized['card_items'] = $this->serializer->normalize($card_items, 'json_recursive');
        }
      }
    }
    elseif ($entity->bundle() == 'card_group_nav') {
      // Get list of categories
      $categories = $this->em->getStorage('bricc_category')->loadByProperties([
        'status' => TRUE,
      ]);
      $category_items = [];
      /** @var \Drupal\bricc\Entity\BriccCategory $category */
      foreach ($categories as $category) {
        $category_items[$category->id()] = $category->label();
      }
      $normalized['items'] = $category_items;
    }
    elseif ($entity->bundle() == 'card_detail') {
      if ($entity->hasField('field_card_item')) {
        if (!$entity->get('field_card_item')->isEmpty()) {
          $card_item_id = $entity->get('field_card')->target_id;
          $card_item = $this->em->getStorage('bricc_card_item')->load($card_item_id);
          $normalized['card_detail'] = $this->serializer->normalize($card_item, 'json_recursive');
        }
      }
    }

    return $normalized;
  }

}
