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

      // Filter
      $params = $event->getView()->getExposedInput();

      // Pagination data
      $offset = $event->getView()->getPager()->getCurrentPage();
      $limit = $event->getLimit();
      if (!empty($params['items_per_page'])) {
        $limit = (int) $params['items_per_page'];
        unset($params['items_per_page']);
      }

      // TODO replace with total amount of data
      $event->getView()->getPager()->total_items = 21;

      // Fetch data
      $remote_data = $this->applicantRemoteData->listApplicant($offset, $limit, $params);

      foreach ($remote_data as $item) {
        $event->addResult(new ResultRow($item));
      }
    }
  }
}
