<?php

namespace Drupal\brimw\Normalizer;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Routing\RouteMatch;
use Symfony\Component\HttpFoundation\Request;

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

    // $node = \Drupal::routeMatch()->getParameter('node');

    // $parameters = new MenuTreeParameters();

    // $menu_tree = \Drupal::menuTree();

    // $tree = $menu_tree->load($entity->field_menu->target_id, $parameters);

    // // Apply some manipulators (checking the access, sorting).
    // $manipulators = [
    //   ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
    //   ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    //   ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    // ];
    // $tree = $menu_tree->transform($tree, $manipulators);
    
    // $route_match = RouteMatch::createFromRequest(\Drupal::request());

//     $a = \Drupal::service('menu_breadcrumb.breadcrumb.default');
//     $router = \Drupal::service('router.no_access_checks');
// $result = $router->match('/node/1');
// dd(\Drupal::routeMatch());
// dd(\Drupal::service('breadcrumb')->build(\Drupal::routeMatch()));
// \Drupal::service('menu.active_trail');
// dd(\Drupal::service('menu.active_trail')->getActiveTrailIds('main'));
//     // dd($result);
//     dd($a->applies(new RouteMatch($result['_route'], $result['_route_object'])));
//     dd($a->build(new RouteMatch($result['_route'], $result['_route_object'])));
//     dd(($tree));

    return $normalized;
  }
}
