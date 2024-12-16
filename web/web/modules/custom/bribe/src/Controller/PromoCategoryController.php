<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoCategoryService;

class PromoCategoryController extends ControllerBase
{
    protected $category;

    public function __construct(PromoCategoryService $category) {
        $this->category = $category;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('bribe.promo.category')
        );
    }
    function getlist() {
        $list = $this->category->promoCategoryList();
        
        return $list;
    }
    function getByID($data) {
        $detail = $this->category->promoCategoryDetail($data);
        
        return $detail;
    }
    function remoteCreate($node) {

        // $subCategorylist = $node->get('field_promo_sub_category')->referencedEntities();

        // $subCategory = array();

        // foreach ($subCategorylist as $sub) {
        //     $subCategory[] = $sub->get('field_subcategory_id')->value;
        // }

        $send = array(
            $node->getTitle(),
            // json_encode($subCategory)
        );
        $create = $this->category->promoCategoryCreate($send);

        if(isset($create['errors'])) {
            return 'Error Connection Or From Data';
        }

        $node->set('field_category_id', $create['data']['createCategory']['id']);
        
        return $node;
    }
    function remoteUpdate($node) {

        // $subCategorylist = $node->get('field_promo_sub_category')->referencedEntities();

        // $subCategory = array();
        
        // foreach ($subCategorylist as $sub) {
        //     $subCategory[] = $sub->get('field_subcategory_id')->value;
        // }
        

        $send = array(
            $node->get('field_category_id')->value,
            $node->getTitle()
            // json_encode($subCategory)
        );
        
        $update = $this->category->promoCategoryUpdate($send);

        if(isset($update['errors'])) {
            return 'Error Connection Or From Data';
        }
        
        return $node;
    }
    function remoteDelete($idCategory)
    {

        $send = array(
            $idCategory
        );

        $delete = $this->category->promoCategoryDelete($send);

        if(isset($delete['errors'])) {
            return 'Error Connection Or From Data';
        }

        return $delete;
    }
}

