<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\bribe\Service\PromoService;

class PromoController extends ControllerBase
{
    protected $promo;

    public function __construct(PromoService $promo)
    {
        $this->promo = $promo;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('bribe.promo')
        );
    }
    function getByID($data)
    {
        $detail = $this->promo->promoDetail($data);

        return $detail;
    }
    function remoteCreate($node)
    {

        $categories = $node->get('field_promo_category')->referencedEntities();
        $locations = $node->get('field_promo_location')->referencedEntities();
        $microsites = $node->get('field_promo_microsite_owner')->referencedEntities();
        // $subCategories = $node->get('field_promo_sub_category')->referencedEntities();

        $category = "";
        // $subCategory = array();
        $location = array();
        $microsite = "";

        foreach ($categories as $cat) {
            $category = $cat->get('field_category_id')->value;
        }
        // foreach ($subCategories as $sub) {
        //     $subCategory[] = $sub->get('field_subcategory_id')->value;
        // }
        foreach ($locations as $loc) {
            $location[] = $loc->get('field_location_id')->value;
        }
        foreach ($microsites as $mic) {
            $microsite = $mic->get('field_microsite_id')->value;
        }

        $media = $node->get('field_promo_image')->entity;
        $file = (!empty($media->get('field_media_image')->entity) ? $media->get('field_media_image')->entity : "");
        $uri = ($file != "" ? $file->getFileUri() : "");
        $relative_path = str_replace('public://', '', $uri); 

        $dateStart = $node->get('field_promo_start_date')->value;
        $getStart = new \DateTime($dateStart);
        $start = $getStart->format('Y-m-d H:i:s');

        $dateEnd = $node->get('field_promo_start_date')->value;
        $getEnd = new \DateTime($dateEnd);
        $end = $getEnd->format('Y-m-d H:i:s');
        $hotOffer = $node->get('field_hot_offers')->value == 1 ? 'true' : 'false';

        $send = array(
            $node->getTitle(),
            $start,
            $end,
            $node->get('field_term_and_condition')->value,
            $relative_path,
            $category,
            json_encode($location),
            json_encode($microsite),
            $hotOffer
        );

        $create = $this->promo->promoCreate($send);

        if (isset($create['errors'])) {
            return 'Error Connection Or From Data';
        }

        $node->set('field_promo_id', $create['data']['createPromo']['_id']);

        return $create;
    }
    function remoteUpdate($node)
    {

        $categories = $node->get('field_promo_category')->referencedEntities();
        $locations = $node->get('field_promo_location')->referencedEntities();
        $microsites = $node->get('field_promo_microsite_owner')->referencedEntities();
        // $subCategories = $node->get('field_promo_sub_category')->referencedEntities();

        $category = "";
        // $subCategory = array();
        $location = array();
        $microsite = "";

        foreach ($categories as $cat) {
            $category = $cat->get('field_category_id')->value;
        }
        // foreach ($subCategories as $sub) {
        //     $subCategory[] = $sub->get('field_subcategory_id')->value;
        // }
        foreach ($locations as $loc) {
            $location[] = $loc->get('field_location_id')->value;
        }
        foreach ($microsites as $mic) {
            $microsite = $mic->get('field_microsite_id')->value;
        }

        $media = $node->get('field_promo_image')->entity;
        $file = (!empty($media->get('field_media_image')->entity) ? $media->get('field_media_image')->entity : "");
        $uri = ($file != "" ? $file->getFileUri() : "");
        $relative_path = str_replace('public://', '', $uri); 

        $dateStart = $node->get('field_promo_start_date')->value;
        $getStart = new \DateTime($dateStart);
        $start = $getStart->format('Y-m-d H:i:s');

        $dateEnd = $node->get('field_promo_start_date')->value;
        $getEnd = new \DateTime($dateEnd);
        $end = $getEnd->format('Y-m-d H:i:s');
        $hotOffer = $node->get('field_hot_offers')->value == 1 ? 'true' : 'false';

        $send = array(
            $node->get('field_promo_id')->value,
            $node->getTitle(),
            $start,
            $end,
            $node->get('field_term_and_condition')->value,
            $relative_path,
            $category,
            json_encode($location),
            json_encode($microsite),
            $hotOffer
        );


        $update = $this->promo->promoUpdate($send);

        if (isset($update['errors'])) {
            return 'Error Connection Or From Data';
        }

        return $node;
    }
    function remoteDelete($id)
    {

        $send = array(
            $id
        );

        $delete = $this->promo->promoDelete($send);

        if (isset($delete['errors'])) {
            return 'Error Connection Or From Data';
        }
        return $delete;
    }
}

