<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
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
    public function getlist($data) {
        $list = $this->location->promoLocationList($data);
        
        return $list;
    }
    public function getByID($data) {
        $detail = $this->location->promoLocationDetail($data);
        
        return $detail;
    }
    public function remoteCreate($node) {

        $send = array(
            $node->getTitle(),
            "https://maps.google.com/?q=".$node->get('field_location_coordinate')->value
        );

        $create = $this->location->promoLocationCreate($send);

        if(isset($create['errors'])) {
            return 'Error Connection Or From Data';
        }

        $node->set('field_location_id', $create['data']['createLocation']['_id']);

        return $node;
        
    }
    public function remoteUpdate($node) {
        
        $send = array(
            $node->get('field_location_id')->value,
            $node->getTitle(),
            "https://maps.google.com/?q=".$node->get('field_location_coordinate')->value
        );

        $update = $this->location->promoLocationUpdate($send);

        if(isset($update['errors'])) {
            return 'Error Connection Or From Data';
        }
        
        return $update;
    }
    public function remoteDelete($id){

        $send = array(
            $id
        );

        $delete = $this->location->promoLocationDelete($send);

        return $delete;
    }
}

