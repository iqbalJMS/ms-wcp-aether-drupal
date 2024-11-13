<?php

namespace Drupal\brimw\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal;

/**
 * Returns responses for Bricc routes.
 */
class SearchController extends ControllerBase {
  public function index(Request $request): JsonResponse 
  {
    Drupal::service('brimw.search_request')->validate();

    return new JsonResponse([
      'data' => Drupal::service('brimw.search_remote_data')
                  ->allSearch($request),
    ]);
  }
}
