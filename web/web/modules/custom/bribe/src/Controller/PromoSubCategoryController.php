<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoSubCategoryService;

class PromoSubCategoryController extends ControllerBase
{
    protected $subCategory;

    public function __construct(PromoSubCategoryService $subCategory) {
        $this->subCategory = $subCategory;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('bribe.promo.sub_category')
        );
    }
    function getlist($id) {
        $list = $this->subCategory->promoSubCategoryList($id);
        
        return $list;
    }
    function getByID($data) {
        $detail = $this->subCategory->promoSubCategoryDetail($data);
        
        return $detail;
    }
    function remoteCreate($node) {

        $send = array(
            str_replace(' ', '', strtolower($node->getTitle())),
            $node->getTitle()
        );

        $create = $this->subCategory->promoSubCategoryCreate($send);

        if(isset($create['errors'])) {
            return 'Error Connection Or From Data';
        }

        $node->set('field_subcategory_id', $create['data']['createSubCategory']['_id']);

        return $node;
    }
    function remoteUpdate($node) {

        $send = array(
            $node->get('field_subcategory_id')->value,
            $node->getTitle()
        );

        $update = $this->subCategory->promoSubCategoryUpdate($send);

        if(isset($update['errors'])) {
            return 'Error Connection Or From Data';
        }
        
        return $update;
    }
    function remoteDelete($id){

        $send = array(
            $id
        );

        $delete = $this->subCategory->promoSubCategoryDelete($send);

        return $delete;
    }
}