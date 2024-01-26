<?php

namespace Drupal\home_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeFooter' Block.
 *
 * @Block(
 *   id = "home_footer",
 *   admin_label = @Translation("Home Footer"),
 * )
 */
class HomeFooter extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'home_footer',
            '#attached' => [
                'library' => [
                    'home_footer/global-styling'
                ]
            ]
        ];
    }
}
