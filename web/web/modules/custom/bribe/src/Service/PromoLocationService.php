<?php

namespace Drupal\bribe\Service;  
  
use Drupal\bribe\Service\RemoteService; 
  
class PromoLocationService 
{  
  private $remote;

  public function __construct(RemoteService $remote)
  {
      $this->remote = $remote;
  }

  public function promoLocationList($id) {
    $query = <<<GQL
        query {
            getLocations {
              _id
              locationAddress
              locationName
            }
        }
    GQL;
    return $query;
  }
  
  public function promoLocationDetail($id) {
    $query = <<<GQL
        query {
            getLocationById(id: "%s") {
              _id
              locationAddress
              locationName
          }
        }
    GQL;
    return $query;
  }

  public function promoLocationCreate($data) {
    $mutation = <<<GQL
        mutation{
            createLocation(input: {
              locationName: "%s"
              locationAddress: "%s"
            }){ _id }
        }
    GQL;

    $setData = array(
      'data' => $data,
      'schema' => $mutation
  );

    $getCreate = $this->remote->request($setData);

    return $getCreate;
  }

  public function promoLocationUpdate($data) {
    $mutation = <<<GQL
        mutation {
            updateLocation(input: {
              id: "%s"
              locationName: "%s"
              locationAddress: "%s"
            }) { _id }
        }
    GQL;

    $setData = array(
      'data' => $data,
      'schema' => $mutation
  );

    $getUpdate = $this->remote->request($setData);

    return $getUpdate;
  }

  public function promoLocationDelete($data) {
    $mutation = <<<GQL
        mutation {
            deleteLocation(id: "%s")
        }
    GQL;

    $setData = array(
      'data' => $data,
      'schema' => $mutation
  );

    $getDelete = $this->remote->request($setData);

    return $getDelete;
  }

}