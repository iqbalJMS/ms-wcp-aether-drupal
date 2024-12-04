<?php

namespace Drupal\brimw\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal;

/**
 * Returns responses for Bricc routes.
 */
class SimulationController extends ControllerBase {
  public function submit(Request $request, string $simulation): JsonResponse 
  {
    Drupal::service('brimw.simulation_request')->validate($simulation);

    return new JsonResponse([
      'data' => Drupal::service('brimw.simulation_remote_data')
                  ->{$simulation}($request),
    ]);
  }
}
