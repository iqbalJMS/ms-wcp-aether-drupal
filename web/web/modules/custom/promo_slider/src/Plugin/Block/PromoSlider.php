<?php

namespace Drupal\promo_slider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\promo_slider\Plugin\Block\content\Promos;

/**
 * Provides a 'PromoSlider' Block.
 *
 * @Block(
 *   id = "promo_slider",
 *   admin_label = @Translation("Promo Slider"),
 * )
 */
class PromoSlider extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $test = Promos::getTopPromo();
        $form = \Drupal::formBuilder()->getForm('Drupal\promo_slider\Form\PromoSliderForm');
        return [
            '#theme' => 'promo_slider',
            '#form' => $form,
            '#test' => $test,
        ];
    }
}
