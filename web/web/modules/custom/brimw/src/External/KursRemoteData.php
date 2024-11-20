<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class KursRemoteData extends BaseRemoteData 
{
  protected function gqlUrl(): string
  {
    return $_ENV['KURS_URL'];
  }

  public function getKurs(): array
  {
    $query = <<< GRAPHQL
      query {
        getKurs {
          buyRateCounter
          buyRateERate
          currency
          isShow
          sellRateCounter
          sellRateERate
        }
      }
    GRAPHQL;

    $result = array_column($this->gql($query)['data']['getKurs'], null, 'currency');
    
    $terms = Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('currency');

    foreach ($terms as $_term) {
      $term = Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($_term->tid);
      if (isset($result[$term->name->value])) {
        $result[$term->name->value]['image'] = $term?->field_image?->entity?->field_media_image?->entity?->createFileUrl();;
      }
    }

    return array_values($result);
  }

  public function getStock(): array
  {
    /*
      available fields:
        buy
        buyPrice
        buyVolume
        change
        country
        cumulativeVol
        currency
        datetime
        delay
        fullName
        high
        high52WKS
        id
        lastDone
        lastDonePrice
        lastUpdated
        low
        low52WKS
        name
        open
        openPrice
        percentChange
        remarks
        sell
        sellPrice
        sellVolume
        shortName
        stockId
        stockName
        symbolType
        volume
    */
    $query = <<< GRAPHQL
      query {
        getStock {
          buyPrice
          cumulativeVol
          high
          high52WKS
          lastUpdated
          low
          low52WKS
          percentChange
          stockId
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['getStock'] ?: [];
  }


  public function postBuyRateCounterCalculator(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        postBuyRateCounterCalculator(
          input: {
            amount: {
              currency: "{$request->get('fromCurrency')}"
              value: {$request->get('amount')}
            }
            to: "{$request->get('toCurrency')}"
          }
        )
      }
    GRAPHQL;

    return $this->gql($query)['data'] ?: [];
  }

  public function postBuyRateeRateCalculator(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        postBuyRateeRateCalculator(
          input: {
            amount: {
              currency: "{$request->get('fromCurrency')}"
              value: {$request->get('amount')}
            }
            to: "{$request->get('toCurrency')}"
          }
        )
      }
    GRAPHQL;

    return $this->gql($query)['data'] ?: [];
  }

  public function postSellRateCounterCalculator(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        postSellRateCounterCalculator(
          input: {
            amount: {
              currency: "{$request->get('fromCurrency')}"
              value: {$request->get('amount')}
            }
            to: "{$request->get('toCurrency')}"
          }
        )
      }
    GRAPHQL;

    return $this->gql($query)['data'] ?: [];
  }

  public function postSellRateeRateCalculator(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        postSellRateeRateCalculator(
          input: {
            amount: {
              currency: "{$request->get('fromCurrency')}"
              value: {$request->get('amount')}
            }
            to: "{$request->get('toCurrency')}"
          }
        )
      }
    GRAPHQL;

    return $this->gql($query)['data'] ?: [];
  }
}
