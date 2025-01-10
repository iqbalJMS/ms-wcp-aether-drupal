<?php

declare(strict_types=1);

namespace Drupal\brimw\Controller;

use Drupal\brimw\External\LocationRemoteData;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
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
    $type = strtolower($type);

    $query = $request->query->all();

    if ($type === 'province') {
      $rows = $this->entityTypeManager()->getStorage('bricc_province')
        ->loadByProperties(['status' => TRUE]);

      /**
       * @var int $id
       * @var \Drupal\bricc\Entity\BriccProvince $row
       */
      foreach ($rows as $id => $row) {
        $result['data'][] = [
          'id' => $id,
          'name' => $row->label(),
          'uuid' => $row->uuid(),
        ];
      }
    }
    elseif ($type === 'name') {
      // Autosuggest name
      $result = $this->locationRemoteData->getLocationAutosuggest($query);
    }
    elseif ($type === 'tipe') {
      $result = $this->locationRemoteData->getAllLocationType($query);
      $vocabulary = 'location_type';
      $query = \Drupal::entityQuery('taxonomy_term')
        ->accessCheck(FALSE)
        ->condition('vid', $vocabulary);
      $tids = $query->execute();
      $term_icons = [];

      if (!empty($tids)) {
        // Load full term entities.
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);

        /** @var \Drupal\taxonomy\Entity\Term $term */
        foreach ($terms as $term) {
          if ($term->hasField('field_bri_location_type')) {
            if (!$term->get('field_bri_location_type')->isEmpty()) {
              $field_value = $term->get('field_bri_location_type')->first()->getValue();

              if (!$term->get('field_image')->isEmpty()) {
                $media = $term->get('field_image')->entity;
                if ($media && $media->hasField('field_media_image') && !$media->get('field_media_image')->isEmpty()) {
                  // Get the File entity referenced in the Media field.
                  $file = $media->get('field_media_image')->entity;

                  if ($file instanceof File) {
                    // Get the file URL.
                    $url = \Drupal::service('file_url_generator')->generateString($file->getFileUri());
                    $term_icons[$field_value['type_id']] = $url;
                  }
                }
              }
            }
          }
        }
      }

      foreach ($result['data'] as $idx => $row) {
        if (isset($term_icons[$row['id']])) {
          $result['data'][$idx]['term_icon'] = $term_icons[$row['id']];
        }
      }
    }
    elseif ($type === 'category') {
      $result = $this->locationRemoteData->getAllLocationCategory($query);

      // Manually filter because no filter option from remote endpoint
      if (isset($query['tipe_id']) && isset($result['data'])) {
        foreach ($result['data'] as $idx => &$category) {
          if ($category['type']['id'] !== $query['tipe_id']) {
            unset($result['data'][$idx]);
          }
        }
      }
    }
    else {
      $result = $this->locationRemoteData->getAllLocations($query);
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
//        [
//          'title' => $this->t('Province'),
//          'url' => Url::fromRoute('view.location.province'),
//          'description' => 'Manage province.',
//        ],
//        [
//          'title' => $this->t('City'),
//          'url' => Url::fromRoute('view.location.city'),
//          'description' => 'Manage cities.',
//        ],
        [
          'title' => $this->t('Type'),
          'url' => Url::fromRoute('view.location.type'),
          'description' => 'Manage location type.',
        ],
        [
          'title' => $this->t('Type icon'),
          'url' => Url::fromRoute('entity.taxonomy_vocabulary.overview_form', [
            'taxonomy_vocabulary' => 'location_type',
          ]),
          'description' => 'Manage icon for location type.',
        ],
        [
          'title' => $this->t('Category'),
          'url' => Url::fromRoute('view.location.category'),
          'description' => 'Manage location category.',
        ],
//        [
//          'title' => $this->t('Region'),
//          'url' => Url::fromRoute('view.location.page_1'),
//          'description' => 'Manage region.',
//        ],
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

  public function autocompleteLocation(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    $all_locations = $this->locationRemoteData->getLocationsOptions();

    $filtered_locations = array_filter($all_locations, function($location) use ($input) {
      return stripos($location, $input) > -1;
    });

    foreach ($filtered_locations as $location_id => $location_name) {
      $results[] = [
        'value' => sprintf('%s (%s)', $location_name, $location_id),
        'label' => sprintf('%s (%s)', $location_name, $location_id),
      ];
    }

    return new JsonResponse($results);
  }

  public function autocompleteLocationType(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    $all_types = $this->locationRemoteData->getTypeOptions();

    $filtered_types = array_filter($all_types, function($type_name) use ($input) {
      return stripos($type_name, $input) > -1;
    });

    foreach ($filtered_types as $type_id => $type_name) {
      $results[] = [
        'value' => sprintf('%s (%s)', $type_name, $type_id),
        'label' => sprintf('%s (%s)', $type_name, $type_id),
      ];
    }

    return new JsonResponse($results);
  }

}
