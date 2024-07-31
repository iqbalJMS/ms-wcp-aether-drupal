<?php

declare(strict_types=1);

namespace Drupal\bricc\EventSubscriber;

use Drupal\bricc\ApplicantRemoteData;
use Drupal\views\ResultRow;
use Drupal\views_remote_data\Events\RemoteDataQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @todo Add description for this subscriber.
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
      $remote_data = $this->applicantRemoteData->listApplicant(0, 0);

      foreach ($remote_data as $item) {
        $event->addResult(new ResultRow($item));
      }
    }
  }
}
