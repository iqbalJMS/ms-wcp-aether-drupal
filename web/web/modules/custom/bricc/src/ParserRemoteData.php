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
    $query = <<< GRAPHQL
      query {
        creditCardTypes {
          descCardType
          idCardType
        }
      }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    return $this->post($this->sourceBaseUrl, $options);
  }

  public function formattedCardType(): array {
    $card_type = $this->listCardType();
    $card_type_options = [];
    if (isset($card_type['data']['creditCardTypes'])) {
      foreach ($card_type['data']['creditCardTypes'] as $card) {
        $card_type_options[trim($card['idCardType'])] = $card['descCardType'];
      }
    }

    return $card_type_options;
  }

  public function listEducation() {
    $query = <<< GRAPHQL
      query {
        education {
          education
          educationDesc
        }
      }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    return $this->post($this->sourceBaseUrl, $options);
  }

  public function listMaritalStatusAsOptions() {
    $query = <<< GRAPHQL
    query {
      maritalStatus {
        maritalStatus
        maritalStatusDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
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
    $query = <<< GRAPHQL
    query {
      homeStatus {
        homeStatus
        homeStatusDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['homeStatus'])) {
      foreach ($result['data']['homeStatus'] as $item) {
        $values[$item['homeStatus']] = $item['homeStatusDesc'];
      }
    }
    return $values;
  }

  public function listEmergencyRelation() {
    $query = <<< GRAPHQL
    query {
      emergencyRelation {
        emergencyRelation
        emergencyRelationDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['emergencyRelation'])) {
      foreach ($result['data']['emergencyRelation'] as $item) {
        $values[$item['emergencyRelation']] = $item['emergencyRelationDesc'];
      }
    }
    return $values;
  }

  public function listJobCategory() {
    $query = <<< GRAPHQL
    query {
      jobCategories {
        jobCategory
        jobCategoryDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['jobCategories'])) {
      foreach ($result['data']['jobCategories'] as $item) {
        $values[$item['jobCategory']] = $item['jobCategoryDesc'];
      }
    }
    return $values;
  }

  public function listJobStatus() {
    $query = <<< GRAPHQL
    query {
      jobStatus {
        jobStatus
        jobStatusDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['jobStatus'])) {
      foreach ($result['data']['jobStatus'] as $item) {
        $values[$item['jobStatus']] = $item['jobStatusDesc'];
      }
    }
    return $values;
  }

  public function listJobField() {
    $query = <<< GRAPHQL
    query {
      jobField {
        jobBidangUsaha
        jobBidangUsahaDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['jobField'])) {
      foreach ($result['data']['jobField'] as $item) {
        $values[$item['jobBidangUsaha']] = $item['jobBidangUsahaDesc'];
      }
    }
    return $values;
  }

  public function listSubJobField() {
    $query = <<< GRAPHQL
    query {
      subJobField {
        jobSubBidangUsaha
        jobSubBidangUsahaDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['subJobField'])) {
      foreach ($result['data']['subJobField'] as $item) {
        $values[$item['jobSubBidangUsaha']] = $item['jobSubBidangUsahaDesc'];
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

  public function listTotalEmployee() {
    $query = <<< GRAPHQL
    query {
      totalEmployee {
        totalEmployee
        totalEmployeeDesc
      }
    }
    GRAPHQL;
    $options['body'] = json_encode(['query' => $query]);
    $result = $this->post($this->sourceBaseUrl, $options);
    $values = [];
    if (isset($result['data']['totalEmployee'])) {
      foreach ($result['data']['totalEmployee'] as $item) {
        $values[$item['totalEmployee']] = $item['totalEmployeeDesc'];
      }
    }
    return $values;
  }

}
