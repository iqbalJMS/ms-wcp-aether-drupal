<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

class RemoteService
{
    /**  
     * @var \GuzzleHttp\Client  
     */
    protected Client $httpClient;

    private $promoServiceUrl;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->promoServiceUrl = $_ENV['PROMO_URL'];
    }

    function request($data)
    {
        $reqData = sprintf($data['schema'], ...$data['data']);
        \Drupal::logger('bribe.promo')->notice('Request merging: @message', ['@message' => $reqData]);
        try {
            $response = $this->httpClient->post($this->promoServiceUrl, [
                'json' => ['query' => $reqData]
            ]);
            \Drupal::logger('bribe.promo')->warning($response->getBody());
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            \Drupal::logger('bribe.promo')->error(json_encode($e->getMessage()));
            return array('errors' => 'Error On Connection To Backend');
        } catch (GuzzleException $e) {
            \Drupal::logger('bribe.promo')->error(json_encode($e->getMessage()));
            return array('errors' => 'Error On Connection To Backend');
        }
    }

}