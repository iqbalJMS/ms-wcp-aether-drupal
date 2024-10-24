<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class LocationRemoteData extends BaseRemoteData {
  protected $isNoCache = TRUE;

  protected function gqlUrl(): string {
    return $_ENV['LOCATION_URL'];
  }

  public function getAllLocations($params = []): array {
    $skip = $params['skip'] ?? 0;
    $limit = $params['limit'] ?? 10;

    $query = <<< GRAPHQL
      query {
        allLocations (param: {
          skip: $skip
          limit: $limit
          filter: {
            search: ""
          }
        }) {
          data {
            id
            mid
            name
            address
            phone
            service
            category
            tipe
            lat
            long
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
    return $result['data']['allLocations'];
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
    return $result['data']['allProvinces'];
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
    return $result['data']['getByIdProvince'];
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
        getAllTypes(input:{
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
    return $result['data']['getAllTypes'];
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
    return $result['data']['allCategories'];
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
    return $result['data']['createCategory']['id'];
  }


  public function createType($name) {
    $query = <<< GRAPHQL
      mutation {
        createType(name: "$name") {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['createType']['id'];
  }

  public function getCategory($id) {
    return [];
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
        createLocation(
          name: "$name"
          address: "$address"
          lat: "$latitude"
          long: "$longitude"
        ) {
          id
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['createProvince']['id'];
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
    return $result['data']['allCitys'];
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
          province
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getByIdCity'];
  }

  public function getType($id) {
    $query = <<< GRAPHQL
      query {
        getByIdType(id: "$id") {
          id
          name
        }
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['getByIdType'];
  }

  public function updateType($id, $name) {
    $query = <<< GRAPHQL
      mutation {
        updateType (id: "$id", name: "$name")
      }
    GRAPHQL;
    $result = $this->gql($query);
    return $result['data']['updateType'];
  }
}
