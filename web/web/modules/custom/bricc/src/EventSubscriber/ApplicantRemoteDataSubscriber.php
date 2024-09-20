<?php

declare(strict_types=1);

namespace Drupal\bricc\EventSubscriber;

use Drupal\bricc\ApplicantRemoteData;
use Drupal\views\ResultRow;
use Drupal\views_remote_data\Events\RemoteDataQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This to fetch remote data
 */
final class ApplicantRemoteDataSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\bricc\ApplicantRemoteData
   */
  private ApplicantRemoteData $applicantRemoteData;

  /**
   * Constructs a new ViewsRemoteDataSubscriber object.
   *
   * @param \Drupal\views_remote_data_pokeapi\PokeApi $poke_api
   *   The PokeApi client.
   */
  public function __construct(ApplicantRemoteData $applicantRemoteData) {
    $this->applicantRemoteData = $applicantRemoteData;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RemoteDataQueryEvent::class => ['onQuery'],
    ];
  }

  /**
   * Kernel response event handler.
   */
  public function onQuery(RemoteDataQueryEvent $event): void {
    $supported_bases = ['bricc_applicant_remote_data'];
    $base_tables = array_keys($event->getView()->getBaseTables());
    if (count(array_intersect($supported_bases, $base_tables)) > 0) {

      $view = $event->getView();
      $export_display_ids = [
        'data_export_1',
        'data_export_2',
      ];
      $is_export = FALSE;
      if (in_array($view->current_display, $export_display_ids)) {
        $is_export = TRUE;
      }

      // Filter
      $params = $event->getView()->getExposedInput();

      // Pagination data
      $offset =  $event->getOffset();
      $limit = $event->getLimit();
      if (!empty($params['items_per_page'])) {
        $limit = (int) $params['items_per_page'];
        unset($params['items_per_page']);
      }

      // No cache for applicant list
      $params['nocache'] = TRUE;

      // Fetch data
      $remote_data = $this->applicantRemoteData->listApplicantProcess($params);

      if (!empty($remote_data)) {
        $event->getView()->getPager()->total_items = count($remote_data);
        $card_type_options = \Drupal::service('bricc.parser_remote_data')->formattedCardType();
        $card_type_brigate_link = $this->applicantRemoteData->listCardItem();

        // Pagination
        $end_index = $offset + $limit;

        if ($is_export) {
          $offset = 0;
          $end_index = count($remote_data);
        }

        foreach ($remote_data as $idx => $item) {
          if ($idx >= $offset && $idx < $end_index) {
            if (isset($item['isDeduped'])) {
              $cek_class = '';
              $cek_deduped = 'Pending';
              if ($item['isDeduped'] === TRUE) {
                $cek_class = 'published';
                $cek_deduped = 'Berhasil';
              }
              elseif ($item['isDeduped'] === FALSE) {
                $cek_class = 'danger';
                $cek_deduped = 'Gagal';
              }
              $item['status_dedup'] = [
                'class' => $cek_class,
                'text' => $cek_deduped,
              ];
            }
            if (isset($item['isDukcapil'])) {
              $cek_class = '';
              $cek_dukcapil = 'Pending';
              if ($item['isDukcapil'] === TRUE) {
                $cek_class = 'published';
                $cek_dukcapil = 'Berhasil';
              }
              elseif ($item['isDukcapil'] === FALSE) {
                $cek_class = 'danger';
                $cek_dukcapil = 'Gagal';
              }
              $item['status_dukcapil'] = [
                'class' => $cek_class,
                'text' => $cek_dukcapil,
              ];
            }
            if (isset($item['isSubmitted'])) {
              $cek_class = '';
              $cek_submit = 'Pending';
              if ($item['isSubmitted'] === TRUE) {
                $cek_class = 'published';
                $cek_submit = 'Berhasil';
              }
              elseif ($item['isSubmitted'] === FALSE) {
                $cek_class = 'danger';
                $cek_submit = 'Gagal';
              }
              $item['status_submit'] = [
                'class' => $cek_class,
                'text' => $cek_submit,
              ];
            }
            if (isset($item['tanggalPengajuan'])) {
              $item['tanggalPengajuan'] = date('Y-m-d H:i', strtotime($item['tanggalPengajuan']));
            }
            if (isset($card_type_options[$item['jenisKartuKredit']])) {
              $id_card = $item['jenisKartuKredit'];
              $item['jenisKartuKredit'] = $card_type_options[$item['jenisKartuKredit']];
              $item['jenisKartuKreditInDrupal'] = '';
              if (isset($card_type_brigate_link[$id_card])) {
                $item['jenisKartuKreditInDrupal'] = $card_type_brigate_link[$id_card];
              }
            }

            $event->addResult(new ResultRow($item));
          }
        }
      }

    }
  }
}
