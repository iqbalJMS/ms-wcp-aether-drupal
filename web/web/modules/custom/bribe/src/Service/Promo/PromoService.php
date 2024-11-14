<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PromoService
{
    /**  
     * @var \GuzzleHttp\Client  
     */
    protected $client;

    private $promoServiceUrl;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
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

        return $query;
    }

    public function promoCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createPromo(createPromoInput: {
                    categoryIds: "66ffa8cca29b4179f4b3141e"
                    endDate: "2024-12-31T23:59:59.000Z"
                    imagePromoUrl:"https://example.com/promo.jpg"
                    lokasiPromo: [
                        "New York",
                        "San Francisco"
                    ]
                    micrositeOwnerIds: "66ffa8d0a29b4179f4b31421"
                    promoTitle: "Promo baru"
                    startDate: "2024-12-31T23:59:59.000Z"
                    subCategoryIds: [
                        "elec1",
                        "elec2"
                    ]
                    termsAndConditions: "Valid for selected items only"
                    }
                ) {
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

        return $mutation;
    }

    public function promoUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updatePromo(updatePromoInput: {
                id: "670e5781e7d1be9e5502c548",
                categoryIds: "asdasdasdsad"
                endDate: "2024-12-31T23:59:59.000Z"
                imagePromoUrl:"https://asdsadasdas///asd/sad/"
                lokasiPromo: [
                    "New York",
                    "San Francisco"
                ]
                micrositeOwnerIds: "66ffa8d0a29b4179asdf4b31421"
                promoTitle: "Promo baru"
                startDate: "2024-12-31T23:59:59.000Z"
                subCategoryIds: [
                    "asdasdsd",
                    "asdsad"
                ]
                termsAndConditions: "Valid for selected items only update error lgasdasd"
                }
                ){
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

        return $mutation;
    }

    public function promoDelete($id)
    {
        $mutation = <<<GQL
            mutation {
                deletePromo(id: "")
            }
        GQL;

        return $mutation;
    }
}