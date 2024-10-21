<?php

namespace Drupal\bribe\Plugin\Block;  
  
use Drupal\Core\Block\BlockBase;  
use Drupal\Core\Form\FormBuilderInterface;  
use Drupal\Core\Form\FormStateInterface;  
use Drupal\Core\Pager\PagerManagerInterface;  
use Drupal\Core\Pager\PagerParameters;  
use Drupal\Core\Table\Table;  
use Drupal\Core\Url;  
use Symfony\Component\DependencyInjection\ContainerInterface;  

use Drupal\bribe\Service\BribeServiceInterface;
  
class PromoPage extends BlockBase {  
  /**  
  * @var \Drupal\bribe\Service\BribeServiceInterface  
  */  
  protected $bribeService;  
  
  /**  
  * @var \Drupal\Core\Pager\PagerManagerInterface  
  */  
  protected $pagerManager;  
  
  public function __construct(BribeServiceInterface $bribe_service, PagerManagerInterface $pager_manager) {  
   $this->bribeService = $bribe_service;  
   $this->pagerManager = $pager_manager;  
  }  
  
  public function build() {  
   $form = \Drupal::formBuilder()->getForm('Drupal\bribe\Form\PromoSearchForm');  
   $data = $this->bribeService->getData();  
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
    $table['#rows'][] = [  
      'data' => [  
       $row['category']['name'],  
       $row['promoTitle'],  
       $row['startDate'],  
       $row['endDate'],  
       $row['lokasiPromo'],  
       [  
        '#type' => 'dropbutton',  
        '#links' => [  
          [  
           'title' => 'Edit',  
           'url' => Url::fromRoute('bribe.update_promo', ['id' => $row['_id']]),  
           'attributes' => [  
            'class' => ['btn', 'btn-primary'],  
            'data-toggle' => 'modal',  
            'data-target' => '#update-promo-dialog',  
           ],  
          ],  
          [  
           'title' => 'Delete',  
           'url' => Url::fromRoute('bribe.delete_promo', ['id' => $row['_id']]),  
           'attributes' => [  
            'class' => ['btn', 'btn-danger'],  
            'data-toggle' => 'modal',  
            'data-target' => '#delete-promo-dialog',  
           ],  
          ],  
        ],  
       ],  
      ],  
    ];  
   }  
  
   $pager = $this->pagerManager->createPager(count($data), 10);  
   $table['#pager'] = $pager;  
  
   return [  
    'form' => $form,  
    'table' => $table,  
   ];  
  }  
}