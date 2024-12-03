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
    $page = $request->get('page') ?: 1;
    $query = <<< GRAPHQL
      query {
        allSearch (param: {
          filter: "{$request->get('filter')}"
          category: "{$request->get('category')}"
          parent: "{$request->get('parent')}"
          page: {$page}
        }) {
          list {
            id
            title
            content
            sub_content
            type
            service_url
            image {
              fileId
              url
            }
            parent
            category
            recordInfo {
              created_at
              updated_at
              deleted_at
            }
          }
          pagination {
            totalData
            totalPages
            currentPage
            isPrev
            isNext
          }
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['allSearch'] ?? $this->error($response);
  }
}
