<?php

namespace Drupal\promo_slider\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
        $cacheBackend = \Drupal::cache('data');
        $cacheKey = 'promo_slider';

        if ($cacheItem = $cacheBackend->get($cacheKey)) {
            // Data found in cache. Use cached data.
            $result = $cacheItem->data;
        }else{
            $database = \Drupal::database();
            $query = $database->select('node_field_data', 'n');
            $query->fields('n', ['title', 'created']);
            $query->condition('n.type', 'article');
            // sort desc
            $query->orderBy('n.created', 'DESC');

            $query->leftJoin('node__field_image', 'nf', 'n.nid = nf.entity_id');
            $query->leftJoin('file_managed', 'fm', 'nf.field_image_target_id = fm.fid');
            $query->addField('fm', 'uri', 'image_uri');

            $query->leftJoin('taxonomy_index', 'ti', 'n.nid = ti.nid');
            $query->leftJoin('taxonomy_term_field_data', 'ttfd', 'ti.tid = ttfd.tid');
            $query->addField('ttfd', 'name', 'tag');

            $resultd = $query->execute()->fetchAll();
            $cacheBackend->set($cacheKey, $resultd);
            $result = $resultd;
        }

        // $json = file_get_contents('https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=BBRI&apikey=5NNP2I07G2HLTVVH');

        // $data = json_decode($json, true);

        return [
            '#theme' => 'promo_slider',
            '#promo' => $result,
            '#attached' => [
                'library' => [
                    'promo_slider/global-styling'
                ]
            ]
        ];
    }
}
