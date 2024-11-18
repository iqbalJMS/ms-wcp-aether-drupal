<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoLocationService;

class PromoLocationController extends ControllerBase
{
    
    protected $location;

    public function __construct(PromoLocationService $location) {
        $this->location = $location;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('bribe.promo.sub_category')
        );
    }
    function getlist($id) {
        $list = $this->location->promoLocationList($id);
        
        return $list;
    }
    function getByID(EntityInterface $entity) {
        $detail = $this->location->promoLocationDetail($entity);
        
        return $detail;
    }
    function remoteCreate(EntityInterface $entity) {
        $create = $this->location->promoLocationCreate($entity);
        
        return $create;
    }
    function remoteUpdate(EntityInterface $entity) {
        
        $update = $this->location->promoLocationUpdate($entity);
        
        return $update;
    }
    function remoteDelete(EntityInterface $entity){
        $delete = $this->location->promoLocationDelete($entity);

        return $delete;
    }
}

