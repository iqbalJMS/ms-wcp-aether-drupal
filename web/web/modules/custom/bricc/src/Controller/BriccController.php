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

  public function applicantDetail($id): array {
    $detail = \Drupal::service('bricc.application_remote_data')->applicantDetail($id);

    // Rename jenis kartu
    $remote_card_types = \Drupal::service('bricc.parser_remote_data')->listCardType();
    $card_type_options = [];
    if (isset($remote_card_types['data']['creditCardTypes'])) {
      foreach ($remote_card_types['data']['creditCardTypes'] as $card) {
        $card_type_options[$card['idCardType']] = $card['descCardType'];
      }
    }
    if (isset($card_type_options[$detail['jenisKartuKredit']])) {
      $detail['jenisKartuKredit'] = $card_type_options[$detail['jenisKartuKredit']];
    }

    // Description untuk edukasi
    $list_edukasi = \Drupal::service('bricc.parser_remote_data')->listEducationAsOptions();
    if (isset($list_edukasi[$detail['edukasi']])) {
      $detail['edukasi'] = $list_edukasi[$detail['edukasi']];
    }

    // Description untuk marital status
    $list_maritalstatus = \Drupal::service('bricc.parser_remote_data')->listMaritalStatusAsOptions();
    if (isset($list_maritalstatus[$detail['statusNikah']])) {
      $detail['statusNikah'] = $list_maritalstatus[$detail['statusNikah']];
    }

    // Description untuk home status
    $list_homestatus = \Drupal::service('bricc.parser_remote_data')->listHomeStatusAsOptions();
    if (isset($list_homestatus[$detail['statusRumah']])) {
      $detail['statusRumah'] = $list_homestatus[$detail['statusRumah']];
    }

    $build['detail_applicant'] = [
      '#theme' => 'applicant_detail',
      '#detail' => $detail,
    ];

    return $build;
  }

  public function documentDetail($type, $id): array {
    $types = [
      'ktp' => 'ktpUrl',
      'npwp' => 'npwpUrl',
      'slip-gaji' => 'slipGajiUrl',
      'swafoto-ktp' => 'swafotoKtpUrl',
    ];
    $allowed_types = array_keys($types);

    if (!in_array($type, $allowed_types)) {
      return [
        '#markup' => '<p>Invalid type</p>',
      ];
    }

    // Call API to get document detail.
    $document = \Drupal::service('bricc.application_remote_data')->documentDetail($id);
    dump($document);
    if (isset($document['data']['document'])) {
      $key = $types[$type];
      $document_url = $document['data']['document'][$key];
      return [
        '#theme' => 'item_list',
        '#items' => [
          [
            '#markup' => $document_url,
          ],
          [
            '#theme' => 'image',
            '#uri' => $document_url,
            '#alt' => 'Document',
            '#title' => 'Document',
          ],
          [
          '#type' => 'link',
          '#title' => $this->t('Download'),
          '#url' => 'http://example.com',
          '#attributes' => [
            'target' => '_blank',
            'class' => ['bri-download-document'],
          ],
        ],
        ],
        '#attributes' => ['class' => ['my-custom-page']],
      ];
    }

    return [
      '#markup' => '<p>Image not found</p>',
    ];
  }
}
