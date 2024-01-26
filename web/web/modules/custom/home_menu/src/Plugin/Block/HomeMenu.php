<?php

namespace Drupal\home_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeMenu' Block.
 *
 * @Block(
 *   id = "home_menu",
 *   admin_label = @Translation("Home Menu"),
 * )
 */
class HomeMenu extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {   
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('home_menu')->getPath();
        return [
            '#theme' => 'home_menu',
            '#test' => $module_path,
            '#attached' => [
                'library' => [
                    'home_menu/global-styling'
                ]
            ]
        ];
    }
}
