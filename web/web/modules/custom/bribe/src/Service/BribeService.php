<?php

namespace Drupal\bribe\Service;  
  
use GuzzleHttp\Client;  
use GuzzleHttp\Exception\RequestException;  
  
class BribeService implements BribeServiceInterface {  
  /**  
  * @var \GuzzleHttp\Client  
  */  
  protected $client;  

  private $promoServiceUrl;
  
  public function __construct(Client $client) {  
   $this->client = $client;  
   $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
  }  
  
  public function getData($category = NULL) {  
   $url = $this->promoServiceUrl;  
   $query = '';  
  
   if($category) {  
    $query = <<< GRAPHQL
        query {  
            searchPromoByCategory (category: "' . $category . '", limit: 10, offset: 0) {  
            category {  
                name  
                _id  
            }  
            endDate  
            _id  
            imagePromoUrl  
            lokasiPromo  
            micrositeOwner {  
                _id  
                name  
            }  
            promoTitle  
            startDate  
            subCategory {  
                name  
                _id  
            }  
            termsAndConditions  
            }  
        }
    GRAPHQL;  
   } else {  
    $query = <<< GRAPHQL
        query {  
            sortPromoByTitleAlphabet(limit: 10, offset: 0) {  
            category {  
                name  
                _id  
            }  
            endDate  
            _id  
            imagePromoUrl  
            lokasiPromo  
            micrositeOwner {  
                _id  
                name  
            }  
            promoTitle  
            startDate  
            subCategory {  
                name  
                _id  
            }  
            termsAndConditions  
            }  
        }
    GRAPHQL ;  
   }  
  
   try {  
    $response = $this->client->post($url, [  
      'headers' => [
          'Content-Type' => 'application/json',
        ],
      'body' => json_encode(['query' => $query]),  
    ]);  

    $data = json_decode($response->getBody()->getContents(), TRUE);
    if(isset($data['data']) && $data['data'] != null) {
      if ($category) {  
        return $data['data']['searchPromoByCategory'];  
      } else {  
        return $data['data']['sortPromoByTitleAlphabet'];  
      } 
    } else {
      return [];
    }
   } catch (RequestException $e) {  
    \Drupal::logger('bribe')->error('Error retrieving data from external API: @error', ['@error' => $e->getMessage()]);  
    return [];  
   }  
  }  
  
  public function createData($data) {  
   $url = $this->promoServiceUrl;  
   $mutation = 'mutation {  
    createPromo(createPromoInput: {  
      categoryIds: "' . $data['categoryIds'] . '"  
      endDate: "' . $data['endDate'] . '"  
      imagePromoUrl: "' . $data['imagePromoUrl'] . '"  
      lokasiPromo: ' . json_encode($data['lokasiPromo']) . '  
      micrositeOwnerIds: "' . $data['micrositeOwnerIds'] . '"  
      promoTitle: "' . $data['promoTitle'] . '"  
      startDate: "' . $data['startDate'] . '"  
      subCategoryIds: ' . json_encode($data['subCategoryIds']) . '  
      termsAndConditions: "' . $data['termsAndConditions'] . '"  
    }) {  
      _id  
      category {  
       _id  
       name  
      }  
      endDate  
      imagePromoUrl  
      lokasiPromo  
      micrositeOwner {  
       _id  
       name  
      }  
      promoTitle  
      startDate  
      subCategory {  
       _id  
       name  
      }  
      termsAndConditions  
    }  
   }';  
  
   try {  
    $response = $this->client->post($url, [  
      'json' => ['query' => $mutation],  
    ]);  
  
    $data = json_decode($response->getBody()->getContents(), TRUE);  
    return $data['data']['createPromo'];  
   } catch (RequestException $e) {  
    \Drupal::logger('bribe')->error('Error creating data on external API: @error', ['@error' => $e->getMessage()]);  
    return [];  
   }  
  }  
  
  public function updateData($id, $data) {  
   $url = $this->promoServiceUrl;  
   $mutation = 'mutation {  
    updatePromo(updatePromoInput: {  
      id: "' . $id . '"  
      categoryIds: "' . $data['categoryIds'] . '"  
      endDate: "' . $data['endDate'] . '"  
      imagePromoUrl: "' . $data['imagePromoUrl'] . '"  
      lokasiPromo: ' . json_encode($data['lokasiPromo']) . '  
      micrositeOwnerIds: "' . $data['micrositeOwnerIds'] . '"  
      promoTitle: "' . $data['promoTitle'] . '"  
      startDate: "' . $data['startDate'] . '"  
      subCategoryIds: ' . json_encode($data['subCategoryIds']) . '  
      termsAndConditions: "' . $data['termsAndConditions'] . '"  
    }) {  
      _id  
      category {  
       _id  
       name  
      }  
      endDate  
      imagePromoUrl  
      lokasiPromo  
      micrositeOwner {  
       _id  
       name  
      }  
      promoTitle  
      startDate  
      subCategory {  
       _id  
       name  
      }  
      termsAndConditions  
    }  
   }';  
  
   try {  
    $response = $this->client->post($url, [  
      'json' => ['query' => $mutation],  
    ]);  
  
    $data = json_decode($response->getBody()->getContents(), TRUE);  
    return $data['data']['updatePromo'];  
   } catch (RequestException $e) {  
    \Drupal::logger('bribe')->error('Error updating data on external API: @error', ['@error' => $e->getMessage()]);  
    return [];  
   }  
  }  
  
  public function getPromoById($id) {  
   $url = $this->promoServiceUrl;  
   $query = 'query {  
    getPromoById(id: "' . $id . '") {  
      category {  
       name  
       _id  
      }  
      endDate  
      _id  
      imagePromoUrl  
      lokasiPromo  
      micrositeOwner {  
       _id  
       name  
      }  
      promoTitle  
      startDate  
      subCategory {  
       name  
       _id  
      }  
      termsAndConditions  
    }  
   }';  
  
   try {  
    $response = $this->client->post($url, [  
      'json' => ['query' => $query],  
    ]);  
  
    $data = json_decode($response->getBody()->getContents(), TRUE);  
    return $data['data']['getPromoById'];  
   } catch (RequestException $e) {  
    \Drupal::logger('bribe')->error('Error retrieving promo by ID from external API: @error', ['@error' => $e->getMessage()]);  
    return [];  
   }  
  }  
  
  public function deletePromo($id) {  
   $url = $this->promoServiceUrl;  
   $mutation = 'mutation {  
    deletePromo(id: "' . $id . '")  
   }';  
  
   try {  
    $response = $this->client->post($url, [  
      'json' => ['query' => $mutation],  
    ]);  
  
    $data = json_decode($response->getBody()->getContents(), TRUE);  
    return $data['data']['deletePromo'];  
   } catch (RequestException $e) {  
    \Drupal::logger('bribe')->error('Error deleting promo from external API: @error', ['@error' => $e->getMessage()]);  
    return FALSE;  
   }  
  }  
}