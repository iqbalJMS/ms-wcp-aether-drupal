<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class SearchRemoteData extends BaseRemoteData 
{
  protected function gqlUrl(): string
  {
    return $_ENV['SEARCH_URL'];
  }

  public function error($response) {
    return Drupal::service('brimw.search_request')->finalizeValidation(['error' => $response['errors'][0]['message'] ?? 'Server error']);
  }

  public function allSearch(Request $request): array
  { 
    $query = <<< GRAPHQL
      query {
        allSearch (param: {
          filter: {
            ngrams: "{$request->get('ngrams')}"
          }
        }) {
          product {
            id
            content
            type
            service_url
            category
          }
          promo {
            id
            content
            type
            service_url
            category
          }
          report {
            id
            content
            type
            service_url
            category
          }
          news {
            id
            content
            type
            service_url
            category
          }
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['allSearch'] ?? $this->error($response);
  }
}
