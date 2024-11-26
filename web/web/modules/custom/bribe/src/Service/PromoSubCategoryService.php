<?php

namespace Drupal\bribe\Service;

use Drupal\bribe\Service\RemoteService;

class PromoSubCategoryService
{
    private $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function promoSubCategoryList($id)
    {

        $query = <<<GQL
            query {
                getSubCategories(categoryId: "%s") {
                    _id
                    name
                }
            }
        GQL;

        $setData = array(
            'data' => $id,
            'schema' => $query
        );

        $getList = $this->remote->request($setData);

        return $getList;
    }

    public function promoSubCategoryDetail($id)
    {
        $mutation = <<<GQL
            query{
                getSubCategoryById(id: "%s") {
                    _id
                    name
                }
            }
        GQL;

        $setData = array(
            'data' => array($id),
            'schema' => $mutation
        );


        $getDetail = $this->remote->request($setData);

        return $getDetail;
    }

    public function promoSubCategoryCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createSubCategory(createSubCategoryInput: {
                    _id: "%s",
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

    public function promoSubCategoryUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updateSubCategory(input: {
                    _id: "%s",
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

    public function promoSubCategoryDelete($id)
    {
        $mutation = <<<GQL
            mutation{
                deleteSubCategory(id: "%s")
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