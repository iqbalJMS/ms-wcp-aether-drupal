<?php

namespace Drupal\bricc;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class ApplicantRemoteData
{

  /**
   * @var string
   */
  private $sourceBaseUrl = '';

  /**
   * @var string
   */
  private $fileServiceUrl = '';

  /**
   * @var string
   */
  private $applicantImageBaseUrl = '';

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
   * @var \Drupal\Core\File\FileSystemInterface
   */
  private FileSystemInterface $file_system;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $em;

  /**
   * Constructs a new PokeApi object.
   *
   * @param \GuzzleHttp\Client $client
   *   The HTTP client.
   * @param CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(
    Client $client,
    CacheBackendInterface $cache,
    LoggerInterface $logger,
    FileSystemInterface $file_system,
    EntityTypeManagerInterface $entity_type_manager
  )   {
    $this->client = $client;
    $this->cache = $cache;
    $this->logger = $logger;
    $this->sourceBaseUrl = $_ENV['APPLICANT_URL'] ?? '';
    $this->fileServiceUrl = $_ENV['FILE_SERVICE_URL'] ?? '';
    $this->file_system = $file_system;
    $this->em = $entity_type_manager;
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
  public function post(string $uri, array $options = []): array
  {
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
    } catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
      return [];
    }
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
  public function postGql(string $query = ''): array
  {
    $cache_key = sprintf('gql:%s', json_encode($query));

    $options = array_merge([
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode(['query' => $query]),
    ]);

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    try {
      $response = $this->client->post($this->sourceBaseUrl, $options);
      $data = Json::decode((string) $response->getBody());
      $this->cache->set($cache_key, $data);
      return $data;
    } catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
      return [];
    }
  }

  /**
   * Convert image id to url.
   *
   * @param string $id
   *   Image's id.
   *
   * @return string
   *   The image url.
   */
  public function getImage(string $id): string {
    if ($id && $this->fileServiceUrl) {
      return $this->fileServiceUrl . '/' . $id;
    }

    return '';
  }

  /**
   * @param array $params
   *
   * @return array
   * @throws GuzzleException
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
      $qgl_str = <<< GRAPHQL
      query {
        personalInfoByDate(
          startDate: \"%s\"
          endDate: \"%s\"
          jenisKartu: \"%s\"
        ) {
          _id
          namaNasabah
          nik
          noHp
          tanggalLahir
          tanggalVerif
        }
      }
      GRAPHQL;
      $options['body'] = sprintf($qgl_str, $start_date, $end_date, $jenis_kartu);

      $result = $this->post($this->sourceBaseUrl, $options);
      if (isset($result['data']['personalInfoByDate'])) {
        return $result['data']['personalInfoByDate'];
      }
    }
    else {
      // Filter by name
      $name = $params['name'] ?? '';
      $phone = $params['phone'] ?? '';
      $tgllahir = $params['tgllahir'] ?? '';

      $gql_str = '{"query":"query {\n  personalInfosByName(\n    namaNasabah:\"%s\"\n    noHp:\"%s\"\n    tanggalLahir:\"%s\"\n  ){\n    _id\n    namaNasabah\n    jenisKartuKredit\n    nik\n    noHp\n    tanggalLahir\n    tanggalVerif\n  }\n}"}';
      $options['body'] = sprintf($gql_str, $name, $phone, $tgllahir);
      $result = $this->post($this->sourceBaseUrl, $options);
      if (isset($result['data']['personalInfosByName'])) {
        return $result['data']['personalInfosByName'];
      }
    }

    return [];
  }

  public function applicantDetail ($_id) {
    $qgl_str = <<<'GRAPHQL'
    query {
      personalInfo (id:"%s") {
        documents {
          ktpId
          npwpId
          slipGajiId
          swafotoKtpId
        }

        _id
        jenisKartuKredit
        namaDepan
        namaBelakang
        namaDiKartu
        sexType
        kewarganegaraan
        nik
        tempatLahir
        tanggalLahir
        email
        statusRumah
        tinggalSejak
        alamat1
        alamat2
        alamat3
        asalKota
        noTelp
        noHp
        statusNikah
        jumlahAnak
        edukasi
        namaIbuKandung

        emergencyRelation {
          namaKontakDarurat
          hubungan
          alamat1
          alamat2
          alamat3
          emergencyCity
          noTelp
        }

        jobInfo {
          namaPerusahaan
          totalPegawai
          kategoriPekerjaan
          statusPekerjaan
          bidangPekerjaan
          subBidangPekerjaan
          titelPekerjaan
          bekerjaSejak
          alamatPerusahaan1
          alamatPerusahaan2
          alamatPerusahaan3
          kotaPerusahaan
          kodePos
          teleponPerusahaan
          penghasilanPerTahun
        }
      }
    }
    GRAPHQL;

    $options = sprintf($qgl_str, $_id);

    $result = $this->postGql($options);

    if (isset($result['data']['personalInfo'])) {
      $result['data']['personalInfo']['documents']['ktpUrl'] = $this->getImage($result['data']['personalInfo']['documents']['ktpId'] ?? '');
      $result['data']['personalInfo']['documents']['npwpUrl'] = $this->getImage($result['data']['personalInfo']['documents']['npwpId'] ?? '');
      $result['data']['personalInfo']['documents']['slipGajiUrl'] = $this->getImage($result['data']['personalInfo']['documents']['slipGajiId'] ?? '');
      $result['data']['personalInfo']['documents']['swafotoKtpUrl'] = $this->getImage($result['data']['personalInfo']['documents']['swafotoKtpId'] ?? '');

      return $result['data']['personalInfo'];
    }

    return [];
  }

  public function listApplicantProcess(array $params = []): array {
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
      $qgl_str = '{"query":"query{\n  personalInfoByDate(\n    startDate: \"%s\",\n    endDate: \"%s\",\n    jenisKartu:\"%s\") {_id, namaNasabah, nik,noHp,tanggalLahir,jenisKartuKredit,isDeduped,isDukcapil,isSubmitted}}"}';
      $options['body'] = sprintf($qgl_str, $start_date, $end_date, $jenis_kartu);

      $result = $this->post($this->sourceBaseUrl, $options);

      if (isset($result['data']['personalInfoByDate'])) {
        return $result['data']['personalInfoByDate'];
      }
    }
    else {
      // Filter by name
      $name = $params['name'] ?? '';
      $phone = $params['phone'] ?? '';
      $tgllahir = $params['tgllahir'] ?? '';

      $gql_str = '{"query":"query {\n\tpersonalInfosByName (\n\t\tnamaNasabah:\"%s\", noHp:\"%s\", tanggalLahir:\"%s\") {\n\t\t_id,\n\t\tnamaNasabah,\n\t\tjenisKartuKredit,\n\t\tnik,  \n\t\tnoHp,\n\t\ttanggalLahir,\n\t\tjenisKartuKredit,\n\t\tisDeduped,\n\t\tisDukcapil,\n\t\tisSubmitted\n\t}\n}"}';
      $options['body'] = sprintf($gql_str, $name, $phone, $tgllahir);
      $result = $this->post($this->sourceBaseUrl, $options);
      if (isset($result['data']['personalInfosByName'])) {
        return $result['data']['personalInfosByName'];
      }
    }

    return [];
  }

  /**
   * @throws \GuzzleHttp\Exception\GuzzleException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function documentDetail ($type, $applicantId): string {
    $qgl_str = '{"query":"{\n  document(id:\"%s\") {\n    ktpUrl,\n    npwpUrl,\n    slipGajiUrl,\n    swafotoKtpUrl\n  }\n}"}';
    $options['body'] = sprintf($qgl_str, $applicantId);
    $response_doc = $this->post($this->sourceBaseUrl, $options);

    if (isset($response_doc['data']['document'][$type])) {
      $doc_id = $response_doc['data']['document'][$type];

      // Get binary
      $file_name = sprintf('%s--%s--%s', $type, $applicantId, $doc_id);
      $destination = 'public://' . $file_name;

      $file = $this->em->getStorage('file')->loadByProperties([
        'uri' => $destination
      ]);
      if ($file) {
        // File already exist
        $file = reset($file);

        // Return the URL
        return $file->createFileUrl();
      }
      else {
        // File not exist, fetch
        try {
          $endpoint_url = $this->fileServiceUrl . '/' . $doc_id;
          $response = $this->client->get($endpoint_url, [
            'stream' => true,
            'headers' => [
              'x-api-key' => 'auth',
            ],
          ]);

          if ($response->getStatusCode() === 200) {
            $data = $response->getBody()->getContents();

            // Save the file data to Drupal's file system.
            $directory = dirname($destination);
            $this->file_system->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
            $destination = $this->file_system->getDestinationFilename($destination, FileExists::Replace);
            $file_uri = $this->file_system->saveData($data, $destination, FileExists::Replace);

            if ($file_uri) {
              // Create a file entity using the URI.
              // Don't set status to make it marked as unmanaged.
              $file = File::create([
                'uri' => $file_uri,
              ]);
              $file->save();

              $this->logger->info(t('File saved with ID: @fid', ['@fid' => $file->id()]));

              return $file->createFileUrl();
            }
          }
        }
        catch (\Exception $e) {
          $this->logger->error($e->getMessage());
        }
      }
    }

    return '';
  }
}
