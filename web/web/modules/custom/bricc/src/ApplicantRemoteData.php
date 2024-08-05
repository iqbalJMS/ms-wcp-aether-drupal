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
    $this->sourceBaseUrl = $_ENV['APPLICANT_URL'];
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
      ]
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
   * @param int $offset
   * @param int $limit
   * @param array $params
   *
   * @return array
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function listApplicant(array $params = []): array {
    // If no params, return empty first.
    if (empty($params)) {
      return [];
    }

    // Filter type
    $filter_type = $params['filter_type'] ?? 'date';

    $options = [];

    if ($filter_type == 'date') {
      // Filter by date
      $start_date = $params['startdate'] ?? '';
      $end_date = $params['enddate'] ?? '';
      $jenis_kartu = $params['jeniskartu'] ?? '';
      $qgl_str = '{"query":"{\n  personalInfoByDate(\n    startDate: \"%s\",\n    endDate: \"%s\",\n    jenisKartu: \"%s\"\n  ) {\n    namaNasabah,\n    nik,\n    noHp,\n    tanggalLahir,\n    tanggalVerif\n  }\n}"}';
      $options['body'] = sprintf($qgl_str, $start_date, $end_date, $jenis_kartu);

      $result = $this->post($this->sourceBaseUrl, $options);
      if (isset($result['data']['personalInfoByDate'])) {
        return $result['data']['personalInfoByDate'];
      }
    }
    else {
      // TODO Filter by name

    }

    return [];
  }

}
