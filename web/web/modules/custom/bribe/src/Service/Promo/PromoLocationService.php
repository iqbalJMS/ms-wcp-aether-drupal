<?php

namespace Drupal\bribe\Service;  
  
use GuzzleHttp\Client;  
use GuzzleHttp\Exception\RequestException;  
  
class PromoLocationService {  
  /**  
  * @var \GuzzleHttp\Client  
  */  
  protected $client;  

  private $promoServiceUrl;
  
  public function __construct(Client $client) {  
   $this->client = $client;  
   $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
  }  

  public function promoLocationList() {

  }
  
  public function promoLocationDetail($id) {
  }

  public function promoLocationCreate($data) {

  }

  public function promoLocationUpdate($data) {

  }

  public function promoLocationDelete($id) {

  }

}