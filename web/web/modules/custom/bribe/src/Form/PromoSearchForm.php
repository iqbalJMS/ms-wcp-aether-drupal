<?php

namespace Drupal\bribe\Form;  
  
use Drupal\Core\Form\FormBase;  
use Drupal\Core\Form\FormStateInterface;  
  
class PromoSearchForm extends FormBase {  
  public function getFormId() {  
   return 'promo_search_form';  
  }  
  
  public function buildForm(array $form, FormStateInterface $form_state) {  
   $form['container'] = [
    '#type' => 'fieldset',
    '#attributes' => [
        'class' => ['container-inline', 'views-exposed-form'],
    ],
   ];
   $form['container']['category'] = [  
    '#type' => 'textfield',  
    '#placeholder' => 'Search by Title, Category, Sub Catogry, And Location',  
   ];  
   $form['container']['submit'] = [  
    '#type' => 'submit',  
    '#value' => 'Search',  
   ];  
  
   return $form;  
  }  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
   $category = $form_state->getValue('category');  
   $data = \Drupal::service('bribe.service')->getData($category);  
   // Update the table with the search results  
   $form_state->setRedirect('bribe.promo', ['data' => $data]);  
  }  
}