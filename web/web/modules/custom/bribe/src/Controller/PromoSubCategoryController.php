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
    function getByID(EntityInterface $entity) {
        $detail = $this->subCategory->promoSubCategoryDetail($entity);
        
        return $detail;
    }
    function remoteCreate(EntityInterface $entity) {
        $create = $this->subCategory->promoSubCategoryCreate($entity);
        
        return $create;
    }
    function remoteUpdate(EntityInterface $entity) {

        $update = $this->subCategory->promoSubCategoryUpdate($entity);
        
        return $update;
    }
    function remoteDelete(EntityInterface $entity){
        $delete = $this->subCategory->promoSubCategoryDelete($entity);

        return $delete;
    }
}


