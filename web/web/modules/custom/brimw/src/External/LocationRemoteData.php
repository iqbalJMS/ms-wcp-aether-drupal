<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class LocationRemoteData extends BaseRemoteData {
  protected function gqlUrl(): string {
    return $_ENV['LOCATION_URL'];
  }

  public function getAllLocations(): array {
    $query = <<< GRAPHQL
      query {
        allLocations (param: {
          skip: 0
          limit: 10
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

    $result = array_column($this->gql($query)['data']['allLocations']['data'], null, 'id');
    return array_values($result);
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

    $result = array_column($this->gql($query)['data']['allProvinces'], null, 'id');
    return array_values($result);
  }

}
