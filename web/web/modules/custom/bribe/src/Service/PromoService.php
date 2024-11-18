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


        $getDetail = $this->remote->read($setData);

        return $getDetail;
    }

    public function promoCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createPromo(createPromoInput: {
                        categoryIds: "%s"
                        endDate: "%s"
                        imagePromoUrl: "%s"
                        lokasiPromo: [%s]
                        micrositeOwnerIds: "%s"
                        promoTitle: "%s"
                        startDate: "%s"
                        subCategoryIds: [%s]
                        termsAndConditions: "%s"
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
            }
        GQL;

        $setData = array(
            'data' => array($data['nid'], $data['title']),
            'schema' => $mutation
        );

        $getCreate = $this->remote->create($setData);

        return $getCreate;
    }

    public function promoUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updatePromo(updatePromoInput: {
                        id: "%s"
                        categoryIds: "%s"
                        endDate: "%s"
                        imagePromoUrl: "%s"
                        lokasiPromo: [%s]
                        micrositeOwnerIds: "%s"
                        promoTitle: "%s"
                        startDate: "%s"
                        subCategoryIds: [%s]
                        termsAndConditions: "%s"
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
            }
        GQL;

        $setData = array(
            'data' => array($data['nid'], $data['title']),
            'schema' => $mutation
        );

        $getUpdate = $this->remote->update($setData);

        return $getUpdate;
    }

    public function promoDelete($id)
    {
        $mutation = <<<GQL
            mutation {
                deletePromo(id: "")
            }
        GQL;

        $getDelete = $this->remote->delete($id);

        return $getDelete;
    }
}