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

  public function getLocationType() {
    // TODO Put correct query
    $query = <<< GRAPHQL
      query {
        allProvinces(param:{
          skip:0,
          limit: 50,
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

  /**
   * @param $data
   *
   * @return bool
   * @todo Implementation
   */
  public function addLocation($data): bool {
    return TRUE;
  }

}
