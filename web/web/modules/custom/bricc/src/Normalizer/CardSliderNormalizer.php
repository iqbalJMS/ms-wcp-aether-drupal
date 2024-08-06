<?php

namespace Drupal\bricc\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

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
  protected $supportedParagraphTypes = ['card_slider', 'card_group_nav'];

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

    return $normalized;
  }

}
