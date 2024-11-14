<?php

namespace Drupal\bribe\Service;

use Drupal\bribe\Service\RemoteService;

class PromoCategoryService
{
    public function __construct()
    {

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

        return $query;
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

        return $query;
    }

    public function promoCategoryCreate($data)
    {
        $mutation = <<<GQL
        mutation {
            createCategory(input: {
                name: ""
                subCategoryIds: []
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

        return $mutation;
    }

    public function promoCategoryUpdate($data)
    {
        $mutation = <<<GQL
        mutation {
            updateCategory(input: {
                _id: ""
                name: ""
                subCategoryIds: []
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

        return $mutation;
    }

    public function promoCategoryDelete($id)
    {
        $mutation = $mutation = <<<GQL
        mutation {
            deleteCategory(id: $id)
        }
        GQL;

        return $mutation;
    }

}