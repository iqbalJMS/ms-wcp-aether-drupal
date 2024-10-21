<?php

namespace Drupal\bribe\Form;  
  
use Drupal\Core\Form\FormBase;  
use Drupal\Core\Form\FormStateInterface;  
  
class DeletePromoForm extends FormBase {  
  public function getFormId() {  
   return 'delete_promo_form';  
  }  
  
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {  
   $form['id'] = [  
    '#type' => 'hidden',  
    '#value' => $id,  
   ];  
  
   $form['confirm'] = [  
    '#type' => 'checkbox',  
    '#title' => 'Confirm deletion',  
    '#required' => TRUE,  
   ];  
  
   $form['submit'] = [  
    '#type' => 'submit',  
    '#value' => 'Delete',  
   ];  
  
   return $form;  
  }  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
   $id = $form_state->getValue('id');  
   $result = \Drupal::service('bribe.service')->deletePromo($id);  
  
   if ($result) {  
    \Drupal::messenger()->addMessage('Promo deleted successfully!');  
   } else {  
    \Drupal::messenger()->addError('Error deleting promo!');  
   }  
  }  
}