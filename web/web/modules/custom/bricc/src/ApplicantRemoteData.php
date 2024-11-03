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
  public function post(string $uri, array $options = [], $nocache = FALSE): array
  {
    $cache_key = sprintf('bricc:%s:%s', md5($uri), md5(json_encode($options)));

    $options = array_merge([
      'headers' => [
        'Content-Type' => 'application/json',
      ],
    ], $options);

    if ($nocache === FALSE) {
      if ($cache = $this->cache->get($cache_key)) {
        return $cache->data;
      }
    }

    try {
      $response = $this->client->post($uri, $options);
      $data = Json::decode((string) $response->getBody());

      if ($nocache === FALSE) {
        $this->cache->set($cache_key, $data);
      }

      return $data;
    } catch (\Exception $e) {
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
    $filter_type = $params['filtering_type'] ?? 'date';

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
          documents {
            ktpId
            npwpId
            slipGajiId
            swafotoKtpId
          }
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
      return $result['data']['personalInfo'];
    }

    return [];
  }

  /**
   * @param array $params
   *
   * @return array
   * @throws \GuzzleHttp\Exception\GuzzleException
   * @todo Query document
   */
  public function listApplicantProcess(array $params = []): array {
    // If no params, return empty first.
    if (empty($params)) {
      return [];
    }

    // Filter type
    $filter_type = $params['filtering_type'] ?? 'date';

    $options = [];

    $nocache = TRUE;

    if ($filter_type == 'date') {
      // Filter by date
      $start_date = $params['startdate'] ?? '';
      $end_date = $params['enddate'] ?? '';
      $jenis_kartu = $params['jeniskartu'] ?? '';
      $gql_str = <<< GRAPHQL
      query {
        personalInfoByDate (
          startDate: "$start_date"
          endDate: "$end_date"
          jenisKartu:"$jenis_kartu"
        ) {
          _id
          tanggalPengajuan
          namaNasabah
          nik
          noHp
          tanggalLahir
          tanggalPengajuan
          jenisKartuKredit
          apregNo
          isDeduped
          isDukcapil
          isSubmitted
          documents {
            ktpId
            npwpId
            slipGajiId
            swafotoKtpId
          }
        }
      }
      GRAPHQL;
      $options['body'] = json_encode(['query' => $gql_str]);
      $result = $this->post($this->sourceBaseUrl, $options, TRUE);

      if (isset($result['data']['personalInfoByDate'])) {
        return $result['data']['personalInfoByDate'];
      }
    }
    else {
      // Filter by name
      $name = $params['name'] ?? '';
      $phone = $params['phone'] ?? '';
      $tgllahir = $params['tgllahir'] ?? '';

      $gql_str = <<< GRAPHQL
      query {
        personalInfosByName (
          namaNasabah:"%s"
          noHp:"%s"
          tanggalLahir:"%s"
        ) {
          _id
          tanggalPengajuan
          namaNasabah
          jenisKartuKredit
          apregNo
          nik
          noHp
          tanggalLahir
          jenisKartuKredit
          isDeduped
          isDukcapil
          isSubmitted
          documents {
            ktpId
            npwpId
            slipGajiId
            swafotoKtpId
          }
        }
      }
      GRAPHQL;
      $options['body'] = sprintf($gql_str, $name, $phone, $tgllahir);
      $options['body'] = json_encode(['query' => $options['body']]);

      $result = $this->post($this->sourceBaseUrl, $options, $nocache);
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
  public function documentDetail ($type, $doc_id): string {
    // Get binary
    $file_name = sprintf('%s--%s', $type, $doc_id);
    $destination = 'public://' . $file_name;

    $file = $this->em->getStorage('file')->loadByProperties([
      'uri' => $destination,
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
        else {
          $this->logger->error(t('Failed retrieving file from @link', ['@link' => $endpoint_url]));
        }
      }
      catch (\Exception $e) {
        $this->logger->error($e->getMessage());
      }
    }

    return '';
  }

  public function listCardItem (): array {
    $cache_key = sprintf('bricc:%s', 'listCardItem');

    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    try {
      // Fetch data
      $data = [];

      $card_items = $this->em->getStorage('bricc_card_item')->loadMultiple();
      foreach ($card_items as $card_item) {
        if (!$card_item->get('field_idcardtype')->isEmpty()) {
          $idcardtype = $card_item->get('field_idcardtype')->value;
          $data[$idcardtype] = $card_item->label();
        }
      }

      $this->cache->set($cache_key, $data);
      return $data;
    } catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
    }

    return [];
  }
}
