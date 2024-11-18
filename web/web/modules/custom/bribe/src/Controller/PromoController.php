<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoService;

class PromoController extends ControllerBase
{
    protected $promo;

    public function __construct(PromoService $promo) {
        $this->promo = $promo;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('bribe.promo.sub_category')
        );
    }
    function getByID(EntityInterface $entity) {
        $detail = $this->promo->promoDetail($entity);
        
        return $detail;
    }
    function remoteCreate(EntityInterface $entity) {
        $create = $this->promo->promoCreate($entity);
        
        return $create;
    }
    function remoteUpdate(EntityInterface $entity) {
        
        $update = $this->promo->promoUpdate($entity);
        
        return $update;
    }
    function remoteDelete(EntityInterface $entity){
        $delete = $this->promo->promoDelete($entity);

        return $delete;
    }
}

