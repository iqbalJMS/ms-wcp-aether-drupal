<?php

namespace Drupal\brimw\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\Menu;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\paragraphs\Entity\Paragraph;

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

    usort($personalizedMenus, fn ($a, $b) => $a->weight->value > $b->weight->value);

    if ($personalizedMenus) {
      $normalized['personalized_menu'] = $this->serializer->normalize($personalizedMenus, 'json_recursive');
    }

    return $normalized;
  }

}
