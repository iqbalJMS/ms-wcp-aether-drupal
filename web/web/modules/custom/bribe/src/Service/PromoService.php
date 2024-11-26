<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PromoService
{
    private $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function promoDetail($id)
    {
        $query = <<<GQL
            query {
                getPromoById(id: ""){
                    _id
                    category {
                        _id
                        name
                        subCategories {
                            _id
                            name
                        }
                    }
                    endDate
                    startDate
                    imagePromoUrl
                    lokasiPromo {
                        _id
                        locationAddress
                        locationName
                    }
                    micrositeOwner {
                        _id
                        name
                    }
                    promoTitle
                    subCategory {
                        _id
                        name
                    }
                    termsAndConditions
                }
            }
        GQL;

        $setData = array(
            'data' => array($id),
            'schema' => $query
        );


        $getDetail = $this->remote->request($setData);

        return $getDetail;
    }

    public function promoCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createPromo(createPromoInput: {
                        promoTitle: "%s"
                        startDate: "%s"
                        endDate: "%s"
                        termsAndConditions: "%s"
                        imagePromoUrl: "%s"
                        categoryIds: "%s"
                        subCategoryIds: %s
                        lokasiPromo: %s
                        micrositeOwnerIds: %s
                    }) {
                    _id
                }
            }
        GQL;

        $setData = array(
            'data' => $data,
            'schema' => $mutation
        );

        $getCreate = $this->remote->request($setData);

        return $getCreate;
    }

    public function promoUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updatePromo(updatePromoInput: {
                        id: "%s"
                        promoTitle: "%s"
                        startDate: "%s"
                        endDate: "%s"
                        termsAndConditions: "%s"
                        imagePromoUrl: "%s"
                        categoryIds: "%s"
                        subCategoryIds: %s
                        lokasiPromo: %s
                        micrositeOwnerIds: %s
                    }) {
                    _id
                }
            }
        GQL;

        $setData = array(
            'data' => $data,
            'schema' => $mutation
        );

        $getUpdate = $this->remote->request($setData);

        return $getUpdate;
    }

    public function promoDelete($id)
    {
        $mutation = <<<GQL
            mutation {
                deletePromo(id: "%s")
            }
        GQL;

        $setData = array(
            'data' => $id,
            'schema' => $mutation
        );

        $getDelete = $this->remote->request($setData);

        return $getDelete;
    }
}