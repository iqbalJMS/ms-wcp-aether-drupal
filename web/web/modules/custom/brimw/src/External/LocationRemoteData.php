<?php

namespace Drupal\brimw\External;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Drupal;

class LocationRemoteData extends BaseRemoteData {
  protected $isNoCache = TRUE;

  public const string CACHEKEY_CATEGORY_OPTIONS = 'category_options';
  public const string CACHEKEY_TYPE_OPTIONS = 'type_options';
  public const string CACHEKEY_LOCATION_OPTIONS = 'location_options';
  public const string CACHEKEY_LOCATION_PREFIX = 'LocationRemoteData_getLocation_';

  protected function gqlUrl(): string {
    return $_ENV['LOCATION_URL'];
  }

  public function formatError($errors) {
    $output = [];
    if (is_array($errors)) {
      foreach ($errors as $error) {
        $output[] = $error['message'];
      }
    }
    return implode(". ", $output);
  }

  public function getAllLocations($params = []): array {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;
    $search = $params['name'] ?? '';
    $province = $params['province'] ?? '';
    $category = $params['category'] ?? '';
    $tipe = $params['tipe'] ?? '';
    $site = $params['site'] ?? '';

    $query = <<< GRAPHQL
      query {
        allLocations (param: {
          skip: $skip
          limit: $limit
          filter: {
            search: "$search"
            province: "$province"
            category: "$category"
            tipe: "$tipe"
            site: "$site"
          }
        }) {
          data {
            id
            name
            address
            phone
            category
            tipe
            lat
            long
            urlMaps
            province
            city
            kodeAgen
            site
          }
          pagination {
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allLocations'] ?? $result;
  }

  public function getLocationAutosuggest($params) {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;
    $search = $params['key'] ?? '';
    $tipe = $params['tipe'] ?? '';

    $query = <<< GRAPHQL
      query {
        allLocations (param: {
          skip: $skip
          limit: $limit
          filter: {
            search: "$search"
            tipe: "$tipe"
          }
        }) {
          data {
            id
            name
          }
          pagination {
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allLocations'] ?? $result;
  }

  public function getAllProvinces($params = []): array {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        allProvinces(param:{
          skip:$skip,
          limit: $limit,
          filter: {
            name: ""
          }
        }){
          data{
            id
            name
          }
          pagination{
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allProvinces'] ?? $result;
  }

  public function createProvince($name): string {
    $query = <<< GRAPHQL
      mutation {
        createProvince(name: "$name") {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['createProvince']['id'];
  }

  public function getProvince($id) {
    $query = <<< GRAPHQL
      query {
        getByIdProvince (id: "$id") {
          id
          name
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getByIdProvince'] ?? $result;
  }

  public function updateProvince($id, $name) {
    $query = <<< GRAPHQL
      mutation {
        updateProvince (id: "$id", name: "$name")
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['updateProvince'];
  }

  public function deleteProvince($id) {
    $query = <<< GRAPHQL
      mutation {
        deleteProvince (id: "$id")
      }
    GRAPHQL;

    return $this->gql($query);
  }

  public function getAllLocationType($params) {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        allTypes(param:{
          skip:$skip,
          limit: $limit,
          filter: {
            name: ""
          }
        }){
          data{
            id
            name
            site
          }
          pagination{
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allTypes'] ?? $result;
  }

  public function getAllLocationCategory($params) {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        allCategories(param:{
          skip:$skip,
          limit: $limit,
          filter: {
            name: ""
          }
        }){
          data{
            id
            name
            type {
              id
              name
            }
          }
          pagination{
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allCategories'] ?? $result;
  }

  /**
   * @param $type_id
   *
   * @return string[]
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getCategoryByType($type_id) {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        getCategoryByType (type_id: "$type_id"){
          data{
            id
            name
            type {
              id
              name
            }
          }
          pagination{
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getCategoryByType'] ?? $result;
  }

  public function getCategoryByTypeOptions($type_id) {

    if ($type_id === '' || $type_id === null || $type_id === 'none') {
      return [];
    }

    $categories = $this->getCategoryByType($type_id);

    if ($categories['data']) {
      return array_column($categories['data'], 'name', 'id');
    }
  }

  public function getCategoryOptions() {
    $cache_key = self::CACHEKEY_CATEGORY_OPTIONS;
    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    else {
      try {
        $categories = $this->getAllLocationCategory([
          'skip' => 0,
          'limit' => 99,
        ]);
        $category_options = array_column($categories['data'], 'name', 'id');
        $this->cache->set($cache_key, $category_options, strtotime($this->cacheDuration));
        return $category_options;
      } catch (RequestException $e) {
        $this->logger->warning($e->getMessage());
        return [];
      }
    }
  }

  public function getTypeOptions() {
    $cache_key = self::CACHEKEY_TYPE_OPTIONS;
    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    else {
      try {
        $types = $this->getAllLocationType([
          'skip' => 0,
          'limit' => 99,
        ]);

        if (isset($types['data'])) {
          $type_options = array_column($types['data'], 'name', 'id');
          $this->cache->set($cache_key, $type_options, strtotime($this->cacheDuration));
          return $type_options;
        }
        else {
          return [];
        }
      } catch (RequestException $e) {
        $this->logger->warning($e->getMessage());
        return [];
      }
    }
  }

  public function createCategory($type_id, $name) {
    $query = <<< GRAPHQL
      mutation {
        createCategory(input: {
          name: "$name"
          type_id: "$type_id"
        }) {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    $this->cache->delete(self::CACHEKEY_CATEGORY_OPTIONS);
    return $result['data']['createCategory']['id'];
  }

  public function createType($name, $site) {
    $query = <<< GRAPHQL
      mutation {
        createType(input: {
          name: "$name"
          site: "$site"
        }) {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    $this->cache->delete(self::CACHEKEY_TYPE_OPTIONS);
    return $result['data']['createType']['id'];
  }

  public function getCategory($id) {
    $query = <<< GRAPHQL
      query {
        getCategoryById (id: "$id") {
          id
          name
          type {
            id
            name
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getCategoryById'] ?? $result;
  }

  public function updateCategory($id, $name, $type_id) {
    // Update category
    $query = <<< GRAPHQL
      mutation {
        updateCategory(input: {
          id: "$id",
          name: "$name"
          type_id: "$type_id"
        })
      }
    GRAPHQL;
    $result = $this->gql($query);
    $this->cache->delete(self::CACHEKEY_CATEGORY_OPTIONS);
    return $result['data']['updateCategory'];
  }

  /**
   * @param $data
   *
   * @return bool
   * @todo Implementation
   */
  public function createLocation($data): bool {
    extract($data);

    $query = <<< GRAPHQL
      mutation {
        createLocation(input: {
          name: "$name"
          address: "$address"
          lat: $lat
          long: $long
          province: "$id_province"
          city: "$id_city"
          zip: "$zip"
          site: "$site"
          category: "$category"
          phone: "$phone"
          tipe: "$type"
          kodeAgen: "$kodeAgen"
        }) {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['createLocation']['id'];
  }

  public function getAllCities($params) {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        allCitys(param:{
          skip:$skip,
          limit: $limit,
          filter: {
            name: ""
          }
        }){
          data{
            id
            name
            province {
              id
              name
            }
          }
          pagination{
            total
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['allCitys'] ?? $result;
  }

  public function createCity($id_province, $name): string {
    $query = <<< GRAPHQL
      mutation {
        createCity(id_province: "$id_province", name: "$name") {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['createCity']['id'];
  }

  public function getCity($id) {
    $query = <<< GRAPHQL
      query {
        getByIdCity (id: "$id") {
          id
          name
          province {
            id
            name
          }
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getByIdCity'] ?? $result;
  }

  public function updateCity($id, $name, $province_id) {
    $query = <<< GRAPHQL
      mutation {
        updateCity (
          id: "$id",
          data: {
            id_province: "$province_id"
            name: "$name"
          })
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['updateCity'];
  }

  public function deleteCity($id) {
    $query = <<< GRAPHQL
      mutation {
        deleteCity (id: "$id")
      }
    GRAPHQL;

    return $this->gql($query);
  }

  public function getType($id) {
    $query = <<< GRAPHQL
      query {
        getTypeById(id: "$id"){
          id
          name
          site
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getTypeById'] ?? $result;
  }

  public function updateType($id, $name, $site) {
    $query = <<< GRAPHQL
      mutation {
        updateType(input: {
          id: "$id"
          name: "$name"
          site: "$site"
        })
      }
    GRAPHQL;
    $result = $this->gql($query);
    $this->cache->delete(self::CACHEKEY_TYPE_OPTIONS);
    return $result['data']['updateType'];
  }

  public function getLocation($id) {
    try {
      $query = <<< GRAPHQL
      query {
        getLocationById (id: "$id") {
          id
          name
          address
          lat
          long
          province
          city
          zip
          category
          tipe
          phone
          urlMaps
          site
          kodeAgen
        }
      }
    GRAPHQL;
      $result = $this->gql($query);
      return $result['data']['getLocationById'];
    } catch (RequestException $e) {
      $this->logger->warning($e->getMessage());
      return [];
    }
  }

  public function updateLocation($id, $new_location_data) {
    extract($new_location_data);

    $query = <<< GRAPHQL
      mutation {
        updateLocation(
          id: "$id",
          data: {
            name: "$name"
            address: "$address"
            lat: $lat
            long: $long
            city: "$id_city"
            province: "$id_province"
            zip: "$zip"
            category: "$category"
            phone: "$phone"
            tipe: "$type"
            site: "$site"
            urlMaps: "$url_maps"
          }
        )
      }
    GRAPHQL;

    return $this->gql($query);
  }

  public function deleteLocation($id) {
    $query = <<< GRAPHQL
      mutation {
        deleteLocation (id: "$id")
      }
    GRAPHQL;

    return $this->gql($query);
  }

  public function deleteLocationType($id) {
    $query = <<< GRAPHQL
      mutation {
        deleteType (id: "$id")
      }
    GRAPHQL;

    return $this->gql($query);
  }

  public function deleteLocationCategory($id) {
    $query = <<< GRAPHQL
      mutation {
        deleteCategory(id: "$id")
      }
    GRAPHQL;

    return $this->gql($query);
  }

  /**
   * @param $id
   *
   * @return true
   * @todo Implement locatin ID validation
   */
  public function validateLocationId($id) {
    return TRUE;
  }
  /**
   * @param $id
   *
   * @return true
   * @todo Implement location Type ID validation
   */
  public function validateLocationTypeId($id) {
    return TRUE;
  }

  public function getLocationsOptions() {
    $cache_key = self::CACHEKEY_LOCATION_OPTIONS;
    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    else {
      try {
        $types = $this->getAllLocations([
          'skip' => 0,
          'limit' => 99999,
        ]);
        $type_options = array_column($types['data'], 'name', 'id');
        $this->cache->set($cache_key, $type_options, strtotime($this->cacheDuration));
        return $type_options;
      } catch (RequestException $e) {
        $this->logger->warning($e->getMessage());
        return [];
      }
    }
  }
}
