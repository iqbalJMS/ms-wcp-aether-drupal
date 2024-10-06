<?php

declare(strict_types=1);

namespace Drupal\brimw\Controller;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for brimw routes.
 */
final class LocationController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly LocationRemoteData $locationRemoteData,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('brimw.location_remote_data'),
    );
  }

  /**
   * Builds the response.
   *
   * @todo Pagination
   */
  public function location(Request $request, string $type): JsonResponse {
    $result['data'] = [];

    $query = $request->query->all();

    if ($type === 'province') {
      $result = $this->locationRemoteData->getAllProvinces();
    }
    else {
      $result = $this->locationRemoteData->getAllLocations();
    }

    return new JsonResponse([
      'data' => $result,
    ]);
  }

  public function admin() {
    $output = [
      '#theme' => 'admin_block_content',
      '#content' => [
        [
          'title' => $this->t('Location'),
          'url' => Url::fromRoute('view.location.page_1'),
          'description' => 'List of all location.',
        ],
        [
          'title' => $this->t('Province'),
          'url' => Url::fromRoute('view.location.province'),
          'description' => 'Manage province.',
        ],
        [
          'title' => $this->t('Region'),
          'url' => Url::fromRoute('view.location.page_1'),
          'description' => 'Manage region.',
        ],
        [
          'title' => $this->t('City'),
          'url' => Url::fromRoute('view.location.page_1'),
          'description' => 'Manage cities.',
        ],
      ],
    ];

    return $output;
  }

  public function listProvinces(): JsonResponse {
    $result = $this->locationRemoteData->getAllLocations();

    return new JsonResponse([
      'data' => $result,
    ]);
  }

}
