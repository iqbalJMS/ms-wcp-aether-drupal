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

        $getList = $this->remote->list($setData);

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


        $getDetail = $this->remote->read($setData);

        return $getDetail;
    }

    public function promoSubCategoryCreate($data)
    {
        $mutation = <<<GQL
            mutation {
                createSubCategory(createSubCategoryInput: {
                    _id: "%s",
                    name: "%s"
                }) {
                    _id
                    name
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

    public function promoSubCategoryUpdate($data)
    {
        $mutation = <<<GQL
            mutation {
                updateSubCategory(updateSubCategoryInput: {
                    _id: "%s",
                    name: "%s"
                }) {
                    _id
                    name
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

    public function promoSubCategoryDelete($id)
    {
        $mutation = <<<GQL
            mutation{
                deleteSubCategory(id: "subCat5")
            }
        GQL;

        $getDelete = $this->remote->delete($id);

        return $getDelete;
    }

}