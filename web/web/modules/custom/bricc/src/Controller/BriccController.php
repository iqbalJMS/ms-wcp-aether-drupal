<?php

declare(strict_types=1);

namespace Drupal\bricc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Url;
use Drupal\system\SystemManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Bricc routes.
 */
class BriccController extends ControllerBase {

  /**
   * The active menu trail service.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrailInterface
   */
  protected $menuActiveTrail;

  /**
   * The controller constructor.
   * @param MenuActiveTrailInterface $menu_active_trail
   */
  public function __construct(MenuActiveTrailInterface $menu_active_trail) {
    $this->menuActiveTrail = $menu_active_trail;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('menu.active_trail'),
    );
  }

  /**
   * Builds the response.
   */
  public function build(): array {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Credit Card'),
          'url' => Url::fromRoute('bricc.admin.credit_card'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
        [
          'title' => $this->t('Management'),
          'url' => Url::fromRoute('bricc.admin.management'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
      ],
    ];

    return $output;
  }

  /**
   * Page credit card
   *
   * @return array
   */
  public function pageCreditCard(): array {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Category'),
          'url' => Url::fromRoute('entity.bricc_category.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
        [
          'title' => $this->t('Items'),
          'url' => Url::fromRoute('entity.bricc_card_item.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
      ],
    ];

    return $output;
  }

  public function pageManagement(): array {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Region'),
          'url' => Url::fromRoute('bricc.admin.management.region'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
        [
          'title' => $this->t('Applicant List'),
          'url' => Url::fromRoute('entity.bricc_card_item.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
        [
          'title' => $this->t('Applicant Status'),
          'url' => Url::fromRoute('entity.bricc_applicant_status.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
      ],
    ];

    return $output;
  }

  public function pageRegion(): array {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Province'),
          'url' => Url::fromRoute('entity.bricc_province.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
        [
          'title' => $this->t('City'),
          'url' => Url::fromRoute('entity.bricc_city.collection'),
          'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ],
      ],
    ];

    return $output;
  }
}
