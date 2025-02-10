<?php

declare(strict_types=1);

namespace Drupal\bricc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Link;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\system\SystemManager;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
  public function build(): RedirectResponse {
    $roles = $this->currentUser()->getRoles();
    if (in_array('admin_credit_card', $roles)) {
      return $this->redirect('bricc.admin_bricc');
    }
    else {
      return $this->redirect('brimw.location.admin');
    }
  }

  public function buildBricc() {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Credit Card'),
          'url' => Url::fromRoute('bricc.admin.credit_card'),
          'description' => 'Manage Credit Card category, and list of Credit Card.',
        ],
        [
          'title' => $this->t('Management'),
          'url' => Url::fromRoute('bricc.admin.management'),
          'description' => 'Manage region, credit card applicant list, and credit card applicant process status.',
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
          'description' => 'Manage category for categorizing Credit Card.',
        ],
        [
          'title' => $this->t('Items'),
          'url' => Url::fromRoute('entity.bricc_card_item.collection'),
          'description' => 'List of Credit Card.',
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
          'description' => 'Manage list of region.',
        ],
        [
          'title' => $this->t('Applicant List'),
          'url' => Url::fromRoute('view.applicant.page_1'),
          'description' => 'View list of credit card applicant.',
        ],
        [
          'title' => $this->t('Applicant Process'),
          'url' => Url::fromRoute('view.applicant.page_2'),
          'description' => 'View list of credit card applicant process status.',
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
          'description' => 'Manage Province data.',
        ],
        [
          'title' => $this->t('City'),
          'url' => Url::fromRoute('entity.bricc_city.collection'),
          'description' => 'Manage City data.',
        ],
      ],
    ];

    return $output;
  }

  public function applicantDetail($id, $mode = 'default'): array|Response {
    $detail = \Drupal::service('bricc.application_remote_data')->applicantDetail($id);
    $card_type_brigate_link = \Drupal::service('bricc.application_remote_data')->listCardItem();

    $unmasked = \Drupal::currentUser()->hasPermission('view full applicant detail');
    $detail['unmasked'] = $unmasked;
    $this->getLogger('bricc')->info('Applicant %applicant_id viewed by %user_id.', [
      '%applicant_id' => $id,
      '%user_id' => \Drupal::currentUser()->getAccountName(),
    ]);

    if (isset($detail['documents']['ktpId'])) {
      $detail['documents']['ktpUrl'] = \Drupal::service('bricc.application_remote_data')->documentDetail('ktp', $detail['documents']['ktpId']);
    }
    if (isset($detail['documents']['npwpId'])) {
      $detail['documents']['npwpUrl'] = \Drupal::service('bricc.application_remote_data')->documentDetail('npwp', $detail['documents']['npwpId']);
    }
    if (isset($detail['documents']['slipGajiId'])) {
      $detail['documents']['slipGajiUrl'] = \Drupal::service('bricc.application_remote_data')->documentDetail('slip-gaji', $detail['documents']['slipGajiId']);
    }
    if (isset($detail['documents']['swafotoKtpId'])) {
      $detail['documents']['swafotoKtpUrl'] = \Drupal::service('bricc.application_remote_data')->documentDetail('swafoto-ktp', $detail['documents']['swafotoKtpId']);
    }

    // Rename jenis kartu
    $remote_card_types = \Drupal::service('bricc.parser_remote_data')->listCardType();
    $card_type_options = [];
    if (isset($remote_card_types['data']['creditCardTypes'])) {
      foreach ($remote_card_types['data']['creditCardTypes'] as $card) {
        $card_type_options[$card['idCardType']] = $card['descCardType'];
      }
    }
    if (isset($card_type_options[$detail['jenisKartuKredit']])) {
      $idcardtype = $detail['jenisKartuKredit'];
      $jenisKartuBrigate = $card_type_options[$idcardtype];
      $jenisKartuDrupal = $card_type_brigate_link[$idcardtype];

      $detail['jenisKartuKredit'] = sprintf('%s (%s)', $jenisKartuDrupal, $jenisKartuBrigate);
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

    // Description untuk emergency relation
    $emergency_relation = \Drupal::service('bricc.parser_remote_data')->listEmergencyRelation();
    if (isset($emergency_relation[$detail['emergencyRelation']['hubungan']])) {
      $detail['emergencyRelation']['hubungan'] = $emergency_relation[$detail['emergencyRelation']['hubungan']];
    }

    // Description untuk emergency relation
    $emergency_relation = \Drupal::service('bricc.parser_remote_data')->listJobCategory();
    if (isset($emergency_relation[$detail['jobInfo']['kategoriPekerjaan']])) {
      $detail['jobInfo']['kategoriPekerjaan'] = $emergency_relation[$detail['jobInfo']['kategoriPekerjaan']];
    }

    // Description untuk emergency relation
    $emergency_relation = \Drupal::service('bricc.parser_remote_data')->listJobStatus();
    if (isset($emergency_relation[$detail['jobInfo']['statusPekerjaan']])) {
      $detail['jobInfo']['statusPekerjaan'] = $emergency_relation[$detail['jobInfo']['statusPekerjaan']];
    }

    // Description untuk emergency relation
    $emergency_relation = \Drupal::service('bricc.parser_remote_data')->listJobField();
    if (isset($emergency_relation[$detail['jobInfo']['bidangPekerjaan']])) {
      $detail['jobInfo']['bidangPekerjaan'] = $emergency_relation[$detail['jobInfo']['bidangPekerjaan']];
    }

    // Description untuk emergency relation
    $emergency_relation = \Drupal::service('bricc.parser_remote_data')->listSubJobField();
    if (isset($emergency_relation[$detail['jobInfo']['subBidangPekerjaan']])) {
      $detail['jobInfo']['subBidangPekerjaan'] = $emergency_relation[$detail['jobInfo']['subBidangPekerjaan']];
    }

    // Total employee
    $total_employee = \Drupal::service('bricc.parser_remote_data')->listTotalEmployee();
    if (isset($total_employee[$detail['jobInfo']['totalPegawai']])) {
      $detail['jobInfo']['totalPegawai'] = $total_employee[$detail['jobInfo']['totalPegawai']];
    }

    if ($mode === 'default') {
      $build['detail_applicant'] = [
        '#theme' => 'applicant_detail',
        '#detail' => $detail,
      ];
      return $build;
    }
    else {
      throw new NotFoundHttpException('Page Not Found');
    }
  }

  public function documentDetail($type, $id): array {
    $types = [
      'ktp' => 'ktpId',
      'npwp' => 'npwpId',
      'slip-gaji' => 'slipGajiId',
      'swafoto-ktp' => 'swafotoKtpId',
    ];
    $allowed_types = array_keys($types);

    if ($id == '000') {
      return [
        '#markup' => '<p>Image not found</p>',
      ];
    }

    if (!in_array($type, $allowed_types)) {
      return [
        '#markup' => '<p>Invalid type</p>',
      ];
    }

    // Call API to get document detail.
    $document = \Drupal::service('bricc.application_remote_data')->documentDetail($types[$type], $id);
    if ($document) {
      return [
        '#theme' => 'item_list',
        '#items' => [
          [
            '#theme' => 'image',
            '#uri' => $document,
            '#alt' => 'Document',
            '#title' => 'Document',
          ],
          [
            '#type' => 'link',
            '#title' => $this->t('Download'),
            '#url' => Url::fromUri('internal:'.$document),
            '#attributes' => [
              'target' => '_blank',
              'class' => ['bri-download-document'],
            ],
          ],
        ],
        '#attributes' => ['class' => ['no-style', 'text-center']],
      ];
    }

    return [
      '#markup' => '<p>Image not found</p>',
    ];
  }
}
