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
              locationAddress: "%s"
              locationName: "%s"
            }){
            _id
            locationAddress
            locationName
          }
        }
    GQL;

    $setData = array(
        'data' => array($data['name'], $data['address']),
        'schema' => $mutation
    );

    $getCreate = $this->remote->create($setData);

    return $getCreate;
  }

  public function promoLocationUpdate($data) {
    $mutation = <<<GQL
        mutation {
            updateLocation(input: {
              id: "%s"
              locationAddress: "%s"
              locationName: "%s"
            }) {
            _id
            locationAddress
            locationName
          }
        }
    GQL;

    $setData = array(
        'data' => array($data['id'], $data['name'], $data['address']),
        'schema' => $mutation
    );

    $getUpdate = $this->remote->update($setData);

    return $getUpdate;
  }

  public function promoLocationDelete($id) {
    $mutation = <<<GQL
        mutation {
            deleteLocation(id: "%s")
        }
    GQL;

    $setData = array(
      'data' => array($id),
      'schema' => $mutation
  );

    $getDelete = $this->remote->delete($id);

    return $getDelete;
  }

}