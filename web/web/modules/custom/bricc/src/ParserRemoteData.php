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
    $cache_key = sprintf('bricc:%s:%s', md5($uri), json_encode($options));

    $options = array_merge([
      'headers' => [
        'Content-Type' => 'application/json',
      ],
    ], $options);

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
    $options['body'] = '{"query": "query { creditCardTypes { descCardType idCardType}}"}';
    return $this->post($this->sourceBaseUrl, $options);
  }

  public function listEducation() {
    $options['body'] = '{"query":"query {\n  education {\n    education\n    educationDesc\n  }\n}"}';
    return $this->post($this->sourceBaseUrl, $options);
  }

  public function listMaritalStatusAsOptions() {
    $options['body'] = '{"query":"query {\n  maritalStatus{\n    maritalStatus\n    maritalStatusDesc\n  }\n}"}';
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['maritalStatus'])) {
      foreach ($result['data']['maritalStatus'] as $maritalStatus) {
        $values[$maritalStatus['maritalStatus']] = $maritalStatus['maritalStatusDesc'];
      }
    }
    return $values;
  }

  public function listHomeStatusAsOptions() {
    $options['body'] = '{"query":"query {\n  homeStatus{\n    homeStatus\n    homeStatusDesc\n  }\n}"}';
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['homeStatus'])) {
      foreach ($result['data']['homeStatus'] as $item) {
        $values[$item['homeStatus']] = $item['homeStatusDesc'];
      }
    }
    return $values;
  }

  public function listEducationAsOptions(){
    $result = $this->listEducation();
    $values = [];
    if (isset($result['data']['education'])) {
      foreach ($result['data']['education'] as $education) {
        $values[$education['education']] = $education['educationDesc'];
      }
    }
    return $values;
  }

}
