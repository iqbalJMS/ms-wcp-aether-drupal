<?php

namespace Drupal\brimw\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Drupal\brimw\Requests\SimulationRequest;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal;

/**
 * Returns responses for Bricc routes.
 */
class SimulationController extends ControllerBase {
  public function submit(Request $request, string $type): JsonResponse 
  {
    Drupal::service('brimw.simulation_request')->validate($type);

    return new JsonResponse([
      'data' => \Drupal::service('brimw.simulation_remote_data')
                  ->{$type}($request),
    ]);
  }
}
