<?php

namespace Drupal\bribe\Service;  
  
use GuzzleHttp\Client;  
use GuzzleHttp\Exception\RequestException;  
  
class PromoMicrositeService {  
  /**  
  * @var \GuzzleHttp\Client  
  */  
  protected $client;  

  private $promoServiceUrl;
  
  public function __construct(Client $client) {  
   $this->client = $client;  
   $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
  }  
  
  public function promoMicrositeList($id) {
    $list = <<<GQL
        query {
            getMicrositeOwners{
                _id
                name
            }
        }
    GQL;
  }
  public function promoMicrositeDetail($id) {
    $detail = <<<GQL
        query {
            getMicrositeOwner(id:""){
                _id
                name
            }
        }
    GQL;

    return $detail;
  }

  public function promoMicrositeCreate($data) {
    $mutation = <<<GQL
        mutation {
            createMicrositeOwner(createMicrositeOwnerInput: {
                name: "Microsite 03"
            }) {
                _id
                name
            }
        }
    GQL;

    return $mutation;
  }

  public function promoMicrositeUpdate($data) {
    $mutation = <<<GQL
        mutation {
            updateMicrositeOwner(updateMicrositeOwnerInput: {
                _id: ""
                name: ""
            }) {
                _id
                name
            }
        }
    GQL;

    return $mutation;
  }

  public function promoMicrositeDelete($id) {
    $mutation = <<<GQL
        mutation {
            deleteMicrositeOwner(id: $id)
        }
    GQL;

    return $mutation;
  }

}