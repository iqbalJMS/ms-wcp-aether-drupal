<?php

namespace Drupal\brimw\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class PersonalizedShortcutNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'personalized_shortcut';

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
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    $personalizedMenus = $this->em->getStorage('menu_link_content')
                                  ->loadByProperties(['menu_name' => 'personalized-menu']);

    usort($personalizedMenus, function ($_a, $_b) {
      $a = $_a->weight->value;
      $b = $_b->weight->value;

      if ($a == $b) {
        return 0;
      }
      return ($a < $b) ? -1 : 1;
    });

    if ($personalizedMenus) {
      $normalized['personalized_menu'] = $this->serializer->normalize($personalizedMenus, 'json_recursive');
    }

    return $normalized;
  }

}
