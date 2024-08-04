<?php

namespace Drupal\bricc;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class ApplicantRemoteData {

  /**
   * @var string
   */
  private $sourceBaseUrl = 'https://66aa39d9613eced4eba8176b.mockapi.io/bricc-api/applicant';

  /**
   * @var \GuzzleHttp\Client
   */
  private Client $client;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private CacheBackendInterface $cache;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Constructs a new PokeApi object.
   *
   * @param \GuzzleHttp\Client $client
   *   The HTTP client.
   * @param CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(Client $client, CacheBackendInterface $cache, LoggerInterface $logger) {
    $this->client = $client;
    $this->cache = $cache;
    $this->logger = $logger;
  }

  /**
   * Gets results from the API.
   *
   * @param string $uri
   *   The uri.
   *
   * @return array
   *   The result.
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function get(string $uri, int $offset = 0, int $limit = 10, array $params = []): array {
    $cache_key = "bricc:$uri";
    $cache_key = time();

    // TODO page or offset
    $page = $offset + 1;

    $queryParams = [
      'page' => $page,
      'limit' => $limit,
    ] + $params;

    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'query' => $queryParams,
    ];

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    try {
      $response = $this->client->get($uri, $options);
      $data = Json::decode((string) $response->getBody());
      $this->cache->set($cache_key, $data);
      return $data;
    }
    catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
      return [];
    }
  }

  /**
   * @param int $offset
   * @param int $limit
   * @param array $params
   *
   * @return array
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function listApplicant(int $offset, int $limit = 10, array $params = []): array {
    // If no params, return empty first.
    if (empty($params)) {
//      return [];
    }

    // Filter type
    $filter_type = $params['filter_type'] ?? 'date';

    if ($filter_type == 'date') {

    }
    else {
      // Filter by name

    }

    $endpoint = $this->sourceBaseUrl;

    return $this->get($endpoint, $offset, $limit, $params);
  }

}
