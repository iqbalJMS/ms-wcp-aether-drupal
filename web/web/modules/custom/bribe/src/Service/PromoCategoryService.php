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

        $getList = $this->remote->list($setData);

        return $getList;
    }

    public function promoCategoryDetail($id)
    {
        $query = <<<GQL
        query{
            getCategories(id: "") {
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
            'data' => $id,
            'schema' => $query
        );

        $getDetail = $this->remote->list($setData);

        return $getDetail;
    }

    public function promoCategoryCreate($data)
    {
        $mutation = <<<GQL
        mutation {
            createCategory(input: {
                name: "%s"
                subCategoryIds: [%s]
            }) {
                id
                name
                subCategories {
                    id
                    name
                }
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

    public function promoCategoryUpdate($data)
    {
        $mutation = <<<GQL
        mutation {
            updateCategory(input: {
                _id: "%s"
                name: "%s"
                subCategoryIds: [%s]
            }) {
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
            'data' => $data,
            'schema' => $mutation
        );

        $getUpdate = $this->remote->update($setData);

        return $getUpdate;
    }

    public function promoCategoryDelete($id)
    {
        $mutation = $mutation = <<<GQL
        mutation {
            deleteCategory(id: $id)
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