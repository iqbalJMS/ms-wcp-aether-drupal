<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoMicrositeService;

class PromoMicrositeController extends ControllerBase
{
    protected $microsite;

    public function __construct(PromoMicrositeService $microsite) {
        $this->microsite = $microsite;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('bribe.promo.microsite')
        );
    }
    function getlist() {
        $list = $this->microsite->promoMicrositeList();
        
        return $list;
    }
    function getByID(EntityInterface $entity) {
        $detail = $this->microsite->promoMicrositeDetail($entity);
        
        return $detail;
    }
    function remoteCreate(EntityInterface $entity) {
        $create = $this->microsite->promoMicrositeCreate($entity);

        throw new \Drupal\Core\Entity\EntityStorageException('Save operation aborted due to validation errors.');
        
        // return $create;
    }
    function remoteUpdate(EntityInterface $entity) {

        $update = $this->microsite->promoMicrositeUpdate($entity);
        throw new \Drupal\Core\Entity\EntityStorageException('Save operation aborted due to validation errors.');
        
        // return $update;
    }
    function remoteDelete(EntityInterface $entity){
        $delete = $this->microsite->promoMicrositeDelete($entity);

        return $delete;
    }
}

