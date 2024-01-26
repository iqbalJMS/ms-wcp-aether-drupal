<?php

namespace Drupal\home_header\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeHeader' Block.
 *
 * @Block(
 *   id = "home_header",
 *   admin_label = @Translation("Home Header"),
 * )
 */
class HomeHeader extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('home_header')->getPath() . "/dist";
        $image_bg = $module_path . "/images/banner.webp";

        return [
            '#theme' => 'home_header',
            '#image_bg' => $image_bg,
            '#attached' => [
                'library' => [
                    'home_header/global-styling'
                ]
            ]
        ];
    }
}
