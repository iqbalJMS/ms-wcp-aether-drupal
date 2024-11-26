<?php

namespace Drupal\bribe\Service;


class PromoMicrositeService
{

    private $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function promoMicrositeList()
    {
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

        $getList = $this->remote->request($setData);

        return $getList;
    }
    public function promoMicrositeDetail($data)
    {
        $detail = <<<GQL
            query {
                getMicrositeOwner(id:"%s"){
                    _id
                    name
                }
            }
        GQL;

        $setData = array(
            'data' => $data,
            'schema' => $detail
        );

        $getDetail = $this->remote->request($setData);

        return $getDetail;
    }

    public function promoMicrositeCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createMicrositeOwner(createMicrositeOwnerInput: {
                    name: "%s"
                }) { _id }
            }
        GQL;

        $setData = array(
            'data' => $data,
            'schema' => $mutation
        );

        $getCreate = $this->remote->request($setData);

        return $getCreate;
    }

    public function promoMicrositeUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updateMicrositeOwner(updateMicrositeOwnerInput: {
                    _id: "%s"
                    name: "%s"
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

    public function promoMicrositeDelete($data)
    {
        $mutation = <<<GQL
            mutation {
                deleteMicrositeOwner(id: "%s")
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