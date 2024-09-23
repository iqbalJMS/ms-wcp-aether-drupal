<?php

declare(strict_types=1);

namespace Drupal\brimw\Controller;

use Drupal\Core\Controller\ControllerBase;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for brimw routes.
 */
final class LocationController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly ClientInterface $httpClient,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('http_client'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
