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
          data {
            buyRateCounter
            buyRateERate
            currency
            isShow
            sellRateCounter
            sellRateERate
          }
          note {
            timeUpdated
            value
          }
        }
      }
    GRAPHQL;

    $result = $this->gql($query)['data']['getKurs'] ?? [];

    $terms = Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('currency');

    $currencies = array_column($this->gql($query)['data']['getKurs']['data'] ?? [], null, 'currency');

    foreach ($terms as $_term) {
      $term = Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($_term->tid);
      if (isset($currencies[$term->name->value])) {
        $currencies[$term->name->value]['image'] = $term?->field_image?->entity?->field_media_image?->entity?->createFileUrl();
      }
    }

    $result['data'] = $currencies;

    return $result;
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


  public function calcBuyCounter(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        calcBuyCounter(
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

  public function calcBuyeRate(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        calcBuyeRate(
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

  public function calcSellCounter(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        calcSellCounter(
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

  public function calcSelleRate(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        calcSelleRate(
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
