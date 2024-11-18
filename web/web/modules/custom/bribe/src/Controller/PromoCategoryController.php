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
    function getByID(EntityInterface $entity) {
        $detail = $this->category->promoCategoryDetail($entity);
        
        return $detail;
    }
    function remoteCreate(EntityInterface $entity) {
        $create = $this->category->promoCategoryCreate($entity);
        
        return $create;
    }
    function remoteUpdate(EntityInterface $entity) {
        
        $update = $this->category->promoCategoryUpdate($entity);
        
        return $update;
    }
    function remoteDelete(EntityInterface $entity){
        $delete = $this->category->promoCategoryDelete($entity);

        return $delete;
    }
}

