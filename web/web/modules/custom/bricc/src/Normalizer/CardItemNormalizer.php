<?php

namespace Drupal\bricc\Normalizer;

use Drupal\bricc\Entity\BriccCardItem;
use Drupal\config_pages\Entity\ConfigPages;
use Drupal\config_pages\Entity\ConfigPagesType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

class CardItemNormalizer extends ContentEntityNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = BriccCardItem::class;

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
      return TRUE;
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

    $config_bricc = ConfigPages::config('bri_cc');

    if ($config_bricc instanceof ConfigPages) {
      if (!$config_bricc->get('field_url')->isEmpty()) {
        $field_url = $config_bricc->get('field_url')->getValue()[0];

        $card_id = NULL;
        if (!$entity->get('field_idcardtype')->isEmpty()) {
          $card_id = $entity->get('field_idcardtype')->value;
        }

        $current_language_code = \Drupal::languageManager()->getCurrentLanguage()->getId();

        // Add query string credit card and language
        $apply_url = Url::fromUri($field_url['uri'], [
          'query' => [
            'card_id' => $card_id,
            'lang' => $current_language_code,
          ]
        ]);
        $normalized['apply_link'] = [
          'path' => $apply_url->toString(),
          'title' => $field_url['title'],
        ];

        // Selengkapnya
        $nodes = $this->em->getStorage('node')->loadByProperties([
          'field_card_item' => $entity->id()
        ]);
        $label_detail = 'View detail';
        if (!$config_bricc->get('field_view_detail_text')->isEmpty()) {
          $label_detail = $config_bricc->get('field_view_detail_text')->value;
        }

        $node = reset($nodes);
        if ($node instanceof NodeInterface) {
          $normalized['detail_link'] = [
            'path' => $node->toUrl()->toString(),
            'title' => $label_detail,
          ];
        }
      }
    }

    return $normalized;
  }

}
