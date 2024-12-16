<?php

namespace Drupal\bribe\Service;

use Drupal\bribe\Service\RemoteService;

class PromoCategoryService
{
    private $remote;

    public function __construct(RemoteService $remote)
    {
        $this->remote = $remote;
    }

    public function promoCategoryList()
    {
        $query = <<<GQL
        query{
            getCategories {
                _id
                name
                subCategories {
                    _id
                    name
                }
            }
        }
        GQL;

        $setData = array(
            'data' => '',
            'schema' => $query
        );

        $getList = $this->remote->request($setData);

        return $getList;
    }

    public function promoCategoryDetail($id)
    {
        $query = <<<GQL
        query{
            getCategories(id: "") {
                _id
                name
            }
        }
        GQL;

        $setData = array(
            'data' => $id,
            'schema' => $query
        );

        $getDetail = $this->remote->request($setData);

        return $getDetail;
    }

    public function promoCategoryCreate($data)
    {
        $mutation = <<<GQL
        mutation {
            createCategory(createCategoryInput:{
                name: "%s"
            }) { id }
        }
        GQL;

        $setData = array(
            'data' => $data,
            'schema' => $mutation
        );

        $getCreate = $this->remote->request($setData);

        return $getCreate;
    }

    public function promoCategoryUpdate($data)
    {
        $mutation = <<<GQL
        mutation {
            updateCategory(input: {
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

    public function promoCategoryDelete($data)
    {
        $mutation = $mutation = <<<GQL
        mutation {
            deleteCategory(id: "%s")
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