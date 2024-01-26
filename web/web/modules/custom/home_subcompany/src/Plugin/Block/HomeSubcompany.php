<?php

namespace Drupal\home_subcompany\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeSubcompany' Block.
 *
 * @Block(
 *   id = "home_subcompany",
 *   admin_label = @Translation("Home Subcompany"),
 * )
 */
class HomeSubcompany extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('home_subcompany')->getPath() . "/dist/images";

        $result = array(
            $module_path . "/1.webp",
            $module_path . "/2.webp",
            $module_path . "/3.webp",
            $module_path . "/4.webp",
            $module_path . "/5.webp",
            $module_path . "/6.webp",
            $module_path . "/7.webp",
            $module_path . "/8.webp",
        );

        // $json = file_get_contents('https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=BBRI&apikey=5NNP2I07G2HLTVVH');

        // $data = json_decode($json, true);

        return [
            '#theme' => 'home_subcompany',
            '#promo' => $result,
            '#attached' => [
                'library' => [
                    'home_subcompany/global-styling'
                ]
            ]
        ];
    }
}
