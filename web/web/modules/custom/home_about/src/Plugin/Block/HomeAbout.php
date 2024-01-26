<?php

namespace Drupal\home_about\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeAbout' Block.
 *
 * @Block(
 *   id = "home_about",
 *   admin_label = @Translation("Home About"),
 * )
 */
class HomeAbout extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('home_about')->getPath() . "/dist";
        $image_bg = $module_path . "/images/bg.webp";

        $content = array(
            [
                'title' => 'Produk Lengkap',
                'content' => 'Produk dan Layanan Bank BRI lengkap dan beragam untuk semua segmen sesuai kebutuhan nasabah',
                'image' => $module_path . "/images/bri-pl.webp"
            ],
            [
                'title' => 'Human Capital Kompeten',
                'content' => 'Human Capital Bank BRI yang kompeten dan profesional siap melayani nasabah dengan setulus hati',
                'image' => $module_path . "/images/bri-hck.webp"
            ],
            [
                'title' => 'GCG yang Prudent',
                'content' => 'Bank BRI menerapkan GCG yang prudent demi menjamin keberlangsungan usaha yang baik',
                'image' => $module_path . "/images/bri-gcg.webp"
            ],
            [
                'title' => 'Jaringan Terbesar',
                'content' => 'Jaringan Bank BRI terbesar dan terluas secara real-time online di seluruh Indonesia',
                'image' => $module_path . "/images/bri-jt.webp"
            ],
            [
                'title' => 'Terus Berinovasi',
                'content' => 'Bank BRI terus berinovasi mengembangkan produk yang sesuai dengan perkembangan jaman untuk memenuhi kebutuhan nasabah',
                'image' => $module_path . "/images/bri-tb.webp"
            ]
        );

        return [
            '#theme' => 'home_about',
            '#test' => $module_path,
            '#img_bg' => $image_bg,
            '#content' => $content,
            '#attached' => [
                'library' => [
                    'home_about/global-styling'
                ]
            ]
        ];
    }
}
