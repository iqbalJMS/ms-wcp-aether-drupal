<?php

namespace Drupal\bribe\Service;  
  
  
class PromoMicrositeService 
{  

  private $remote;

  public function __construct(RemoteService $remote)
  {
      $this->remote = $remote;
  }
  
  public function promoMicrositeList() {
    $list = <<<GQL
        query {
            getMicrositeOwners{
            _id
            name
        }
        }
    GQL;
    
    $setData = array(
        'data' => '',
        'schema' => $list
    );

    $getList = $this->remote->list($setData);

    return $getList;
  }
  public function promoMicrositeDetail($id) {
    $detail = <<<GQL
        query {
            getMicrositeOwner(id:"%s"){
                _id
                name
            }
        }
    GQL;

    $setData = array(
        'data' => $id,
        'schema' => $detail
    );

    $getDetail = $this->remote->read($setData);

    return $getDetail;
  }

  public function promoMicrositeCreate($data) {
    $mutation = <<<GQL
        mutation {
            createMicrositeOwner(createMicrositeOwnerInput: {
                name: "Microsite 04"
            }) {
                _id
                name
            }
        }
    GQL;

    $setData = array(
        'data' => $data,
        'schema' => $mutation
    );

    $getCreate = $this->remote->create($setData);

    return $getCreate;
  }

  public function promoMicrositeUpdate($data) {
    $mutation = <<<GQL
        mutation {
            updateMicrositeOwner(updateMicrositeOwnerInput: {
                _id: "%s"
                name: "%s"
            }) {
                _id
                name
            }
        }
    GQL;

    $setData = array(
        'data' => $data,
        'schema' => $mutation
    );

    $getUpdate = $this->remote->update($setData);
    
    return $getUpdate;
  }

  public function promoMicrositeDelete($id) {
    $mutation = <<<GQL
        mutation {
            deleteMicrositeOwner(id: "%s")
        }
    GQL;

    $setData = array(
        'data' => $id,
        'schema' => $mutation
    );

    $getDelete = $this->remote->delete($setData);

    return $getDelete;
  }

}