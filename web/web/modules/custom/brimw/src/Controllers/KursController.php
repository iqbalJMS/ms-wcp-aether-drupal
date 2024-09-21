<?php

namespace Drupal\brimw\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal;

/**
 * Returns responses for Bricc routes.
 */
class KursController extends ControllerBase {
  public function submit(Request $request, string $type): JsonResponse 
  {
    Drupal::service('brimw.kurs_request')->validate($type);

    return new JsonResponse([
      'data' => Drupal::service('brimw.kurs_remote_data')
                  ->{$type}($request),
    ]);
  }
}
