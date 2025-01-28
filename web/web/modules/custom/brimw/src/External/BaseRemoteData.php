<?php

namespace Drupal\brimw\External;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class BaseRemoteData {

  protected $isNoCache = FALSE;

  /**
   * @var string
   */
  private string $gqlUrl;

  /**
   * @var \GuzzleHttp\Client
   */
  protected Client $client;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected CacheBackendInterface $cache;

  /**
   * @var string
   */
  protected string $cacheDuration;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * Constructs a new object.
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
    $this->gqlUrl = $this->gqlUrl();
    $this->cacheDuration = '+1 day';
  }

  protected function gqlUrl(): string
  {
    throw new \Exception('Please set graphql url.');
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
  public function gql(string $query): array {
    if ($this->isNoCache) {
      $options = [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        'body' => json_encode(['query' => $query]),
      ];
      try {
        $response = $this->client->post($this->gqlUrl, $options);
      }
      catch (\Exception $e) {
        $this->logger->warning($e->getMessage());
        $data = Json::decode((string) $e->getResponse()->getBody()->__toString());
        return $data ?? ['error' => [['message' => "Server error"]]];
      }

      return Json::decode((string) $response->getBody());
    }

    $cache_key = sprintf('brimw:%s:%s', urlencode($this->gqlUrl), json_encode($query));

    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(['query' => $query]),
    ];

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }

    try {
      $response = $this->client->post($this->gqlUrl, $options);
      $data = Json::decode((string) $response->getBody());
      $this->cache->set($cache_key, $data, strtotime($this->cacheDuration));
      return $data;
    } catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
      $data = Json::decode((string) $e->getResponse()->getBody()->__toString());
      return $data ?? ['error' => [['message' => "Server error"]]];
    }
  }
}
