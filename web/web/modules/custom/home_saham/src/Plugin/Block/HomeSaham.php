<?php

namespace Drupal\home_saham\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeSaham' Block.
 *
 * @Block(
 *   id = "home_saham",
 *   admin_label = @Translation("Home Saham"),
 * )
 */
class HomeSaham extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'home_saham',
            '#test' => 'saham'
        ];
    }
}
