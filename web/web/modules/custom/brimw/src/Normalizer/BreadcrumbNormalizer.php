<?php

namespace Drupal\brimw\Normalizer;

use Drupal;
use Drupal\Core\Link;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Url;

class BreadcrumbNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'breadcrumb';

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

    $node = \Drupal::routeMatch()->getParameter('node');

    $parameters = new MenuTreeParameters();

    $menu_tree = \Drupal::menuTree();

    $tree = $menu_tree->load($entity->field_menu->target_id, $parameters);

    // Apply some manipulators (checking the access, sorting).
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $trees = $menu_tree->transform($tree, $manipulators);
    
    $_trails = $this->getMenuTrail($trees, $node);

    $trails[] = [
      'title' => t('Home')->__toString(),
      'url' => Url::fromRoute("<front>")->toString(),
    ];

    foreach ($_trails as $menu) {
      $link = $menu->link;
      $trails[] = [
        'title' => $link->getTitle(),
        'url' => $link->getUrlObject()->toString(),
      ];
    }

    $normalized['data'] = $trails;

    return $normalized;
  }

  protected function getMenuTrail($trees, $node, $trail = [])
  {
    foreach ($trees as $tree) {
      $trail[] = $tree;
      if ($tree->link->getUrlObject()->toUriString() === $node->toUrl()->toUriString()) {
        return $trail;
      }
      if ($trail = $this->getMenuTrail($tree->subtree, $node, $trail)) {
        return $trail;
      }
    }

    return [];
  }
}
