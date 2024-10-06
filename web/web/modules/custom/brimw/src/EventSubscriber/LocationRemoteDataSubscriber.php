<?php

declare(strict_types=1);

namespace Drupal\brimw\EventSubscriber;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\ResultRow;
use Drupal\views_remote_data\Events\RemoteDataQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Add description for this subscriber.
 */
final class LocationRemoteDataSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a LocationRemoteDataSubscriber object.
   */
  public function __construct(
    private readonly LocationRemoteData $locationRemoteData,
  ) {}

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
    $supported_bases = ['brimw_location_remote_data'];
    $base_tables = array_keys($event->getView()->getBaseTables());
    if (count(array_intersect($supported_bases, $base_tables)) > 0) {

      // Check display ID
      $display_id = $event->getView()->current_display;

      // Pagination data
      $params['skip'] =  $event->getOffset();
      $params['limit'] = $event->getLimit();

      // Fetch data
      if ($display_id === 'page_1') {
        $remote_data = $this->locationRemoteData->getAllLocations($params);
      }
      elseif ($display_id === 'province') {
        $remote_data = $this->locationRemoteData->getAllProvinces($params);
      }

      if (!empty($remote_data)) {
        $event->getView()->getPager()->total_items = $remote_data['pagination']['total'];

        foreach ($remote_data['data'] as $idx => $item) {
          $event->addResult(new ResultRow($item));
        }
      }

    }
  }

}
