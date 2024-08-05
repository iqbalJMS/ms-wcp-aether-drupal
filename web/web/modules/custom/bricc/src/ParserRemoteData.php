<?php

namespace Drupal\bricc;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class ParserRemoteData {

  /**
   * @var string
   */
  private $sourceBaseUrl = '';

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
    $this->sourceBaseUrl = $_ENV['PARSER_URL'];
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
  public function post(string $uri, array $options = []): array {
    $cache_key = "bricc:$uri";

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    try {
      $response = $this->client->post($uri, $options);
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
   * @return array
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function listCardType(): array {
    $graphqlQuery = '{"query": "query { creditCardTypes { descCardType idCardType}}"}';
    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => $graphqlQuery,
    ];

    return $this->post($this->sourceBaseUrl, $options);
  }

}
