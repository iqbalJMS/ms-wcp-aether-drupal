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
        return [
            '#theme' => 'home_menu',
            '#test' => 'mihoyo'
        ];
    }
}
