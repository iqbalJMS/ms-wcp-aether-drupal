<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PromoSubCategoryService
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

    public function promoSubCategoryList($id)
    {

        $query = <<<GQL
            query {
                getSubCategories(categoryId: "") {
                    _id
                    name
                }
            }
        GQL;
        return $$query;
    }

    public function promoSubCategoryDetail($id)
    {
        $mutation = <<<GQL
            query{
                getSubCategoryById(id: "") {
                    _id
                    name
                }
            }
        GQL;

        return $mutation;
    }

    public function promoSubCategoryCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createSubCategory(createSubCategoryInput: {
                    _id: "subCat5",
                    name: "Laptops"
                }) {
                    _id
                    name
                }
            }
        GQL;

        return $mutation;
    }

    public function promoSubCategoryUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                createSubCategory(createSubCategoryInput: {
                    _id: "subCat5",
                    name: "Laptops"
                }) {
                    _id
                    name
                }
            }
        GQL;
    }

    public function promoSubCategoryDelete($id)
    {
        $mutation = <<<GQL
            mutation{
                deleteSubCategory(id: "subCat5")
            }
        GQL;

        return $mutation;
    }

}