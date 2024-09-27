<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent;

/**
 * @deprecated If everything related to menu icon is working, this can be removed
 */
final class MenuIconManipulators{

  /**
   * Constructs a MenuIconManipulators object.
   */
  public function __construct(
    private readonly EntityTypeRepositoryInterface $entityTypeRepository,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  private function menuLinkLoadIcon(MenuLinkInterface $instance) {

  }

  /**
   * Load menu icon
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $tree
   *    The menu link tree to manipulate.
   *
   * @return \Drupal\Core\Menu\MenuLinkTreeElement[]
   *    The manipulated menu link tree.
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function loadMenuIcon(array $tree): array {
    foreach ($tree as $key => $element) {
      if ($element->link instanceof MenuLinkContent) {
        $entity = $element->link->getEntity();
        if ($entity->hasField('field_image')) {
          if (!$entity->get('field_image')->isEmpty()) {
            $media_image = $entity->get('field_image')->referencedEntities()[0];
            if ($media_image instanceof Media) {
              if (!$media_image->get('field_media_image')->isEmpty()) {
                /** @var \Drupal\file\Entity\File $file_image */
                $file_image = $media_image->get('field_media_image')->referencedEntities()[0];
                $tree[$key]->options['query']['icon'] = $file_image->createFileUrl();
              }
            }
          }
        }

        if ($tree[$key]->subtree) {
          $tree[$key]->subtree = $this->loadMenuIcon($tree[$key]->subtree);
        }
      }
    }

    return $tree;
  }

}
