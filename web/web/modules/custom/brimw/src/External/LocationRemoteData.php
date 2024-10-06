<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class LocationRemoteData extends BaseRemoteData {
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

  public function getAllProvinces(): array {
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

}
