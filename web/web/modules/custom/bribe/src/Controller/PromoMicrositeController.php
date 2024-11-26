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
    function getByID($data) {
        $detail = $this->microsite->promoMicrositeDetail($data);
        
        return $detail;
    }
    function remoteCreate($node) {

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
    function remoteUpdate($node) {

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
    function remoteDelete($id){
        
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

