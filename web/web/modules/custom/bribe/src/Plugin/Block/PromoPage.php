<?php

namespace Drupal\bribe\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**  
 * @Block(  
 *  id = "promo",  
 *  admin_label = @Translation("Promo Page"),  
 *  category = @Translation("Custom")  
 * )  
 */

class PromoPage extends BlockBase
{
  /**  
   * @var \Drupal\bribe\Service\BribeServiceInterface  
   */
  protected $bribeService;

  /**  
   * @var \Drupal\Core\Pager\PagerManagerInterface  
   */
  protected $pagerManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->bribeService = \Drupal::service('bribe.service');
    $this->pagerManager = \Drupal::service('pager.manager');
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  public function build()
  {
    $form = \Drupal::formBuilder()->getForm('Drupal\bribe\Form\PromoSearchForm');
    $data = $this->bribeService->getData();
    // $add_promo_button = [
    //   '#type' => 'link',
    //   '#title' => 'Add Promo',
    //   '#url' => Url::fromRoute('bribe.add_promo'),
    //   '#ajax' => [
    //     'dialogType' => 'modal',
    //     'dialog' => ['height' => 400, 'width' => 700],
    //   ],
    //   '#attributes' => [
    //     'class' => ['button', 'button--action', 'button--primary']
    //   ],
    // ];
    $table = [
      '#type' => 'table',
      '#header' => [
        'Category',
        'Promo Title',
        'Start Date',
        'End Date',
        'Location',
        'Actions',
      ],
      '#rows' => [],
    ];
    foreach ($data as $row) {
      $category_name = [];
      foreach ($row['category'] as $value) {
        $category_name[] = $value['name'];
      }
      $category_name = implode(', ', $category_name);
      $locName = implode(', ', $row['lokasiPromo']);
      $table['#rows'][] = [
        'data' => [
          $category_name,
          $row['promoTitle'],
          date_format(date_create($row['startDate']), 'Y-m-d H:i:s'),
          date_format(date_create($row['endDate']), 'Y-m-d H:i:s'),
          $locName,
          [
            'data' => [
              '#type' => 'dropbutton',
              '#dropbutton_type' => 'small',
              '#links' => [
                [
                  'title' => 'Edit',
                  'url' => Url::fromRoute('bribe.update_promo', ['id' => $row['_id']]),
                  'attributes' => [
                    'class' => ['btn', 'btn-primary', 'btn-small'],
                    'data-toggle' => 'modal',
                    'data-target' => '#update-promo-dialog',
                  ],
                ],
                [
                  'title' => 'Delete',
                  'url' => Url::fromRoute('bribe.delete_promo', ['id' => $row['_id']]),
                  'attributes' => [
                    'class' => ['btn', 'btn-danger', 'btn-small'],
                    'data-toggle' => 'modal',
                    'data-target' => '#delete-promo-dialog',
                  ],
                ],
              ],
            ]
          ],
        ],
      ];
    }
    $pager = $this->pagerManager->createPager(count($data), 10);
    $table['#pager'] = $pager;
    $table['#empty'] = 'There are no items promo to display';

    return [
      'form' => $form,
      'table' => $table,
      '#data' => $data,
    ];
  }
}