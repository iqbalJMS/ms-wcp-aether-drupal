<?php

namespace Drupal\bribe\Controller;  
  
use Drupal\Core\Controller\ControllerBase;  
use Drupal\bribe\Form\AddPromoForm;  
use Drupal\bribe\Form\UpdatePromoForm;  
use Drupal\bribe\Form\DeletePromoForm;  
  
class PromoController extends ControllerBase {  
  public function promo() {  
   return [  
    '#theme' => 'promo',  
    '#data' => \Drupal::service('bribe.service')->getData(),  
   ];  
  }  
  
  public function addPromo() {  
   $form = \Drupal::formBuilder()->getForm('Drupal\bribe\Form\AddPromoForm');  
   return [  
    '#theme' => 'add_promo_dialog',  
    '#form' => $form,  
   ];  
  }  
  
  public function updatePromo($id) {  
    $form = new UpdatePromoForm();  
    $form_state = new \Drupal\Core\Form\FormState();  
    $form = $form->buildForm([], $form_state, $id);  
    return [  
     '#theme' => 'update_promo_dialog',  
     '#form' => $form,  
    ];  
  }  
  
  public function deletePromo($id) {  
    $form = new DeletePromoForm();  
    $form_state = new \Drupal\Core\Form\FormState();  
    $form = $form->buildForm([], $form_state, $id);  
    return [  
     '#theme' => 'delete_promo_dialog',  
     '#form' => $form,  
    ];  
  }  
}