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
    public function getlist() {
        $list = $this->microsite->promoMicrositeList();
        
        return $list;
    }
    public function getByID($data) {
        $detail = $this->microsite->promoMicrositeDetail($data);
        
        return $detail;
    }
    public function remoteCreate($node) {

        $send = array(
            $node->getTitle()
        );


        $create = $this->microsite->promoMicrositeCreate($send);

        if(isset($create['errors'])) {
            return 'Error Connection Or From Data';
        }

        $node->set('field_microsite_id', $create['data']['createMicrositeOwner']['_id']);

        return $node;

    }
    public function remoteUpdate($node) {

        $send = array(
            $node->get('field_microsite_id')->value,
            $node->getTitle()
        );

        $update = $this->microsite->promoMicrositeUpdate($send);

        if(isset($update['errors'])) {
            return 'Error Connection Or From Data';
        }
        
        return $node;
    }
    public function remoteDelete($id){
        
        $send = array(
            $id
        );

        $delete = $this->microsite->promoMicrositeDelete($send);

        if(isset($delete['errors'])) {
            return 'Error Connection Or From Data';
        }

        return $delete;
    }
}

