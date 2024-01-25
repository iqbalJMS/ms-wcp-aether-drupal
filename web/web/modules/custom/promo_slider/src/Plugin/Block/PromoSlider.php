<?php

namespace Drupal\promo_slider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Renderer;
use Drupal\promo_slider\Plugin\Block\content\Promos;
use Drupal\views\Views;

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
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->accessCheck(FALSE); // Bypass access checks (user perms, etc
        $result = $query->execute();

        \Drupal::logger('promo_slider')->notice(print_r($result));

        $test = Promos::getTopPromo();
        return [
            '#theme' => 'promo_slider',
            '#test' => $test,
            '#promo' => $result,
        ];
    }
}
