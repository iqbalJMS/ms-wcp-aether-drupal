<?php

namespace Drupal\bribe\Form;  
  
use Drupal\Core\Form\FormBase;  
use Drupal\Core\Form\FormStateInterface;  
  
class PromoSearchForm extends FormBase {  
  public function getFormId() {  
   return 'promo_search_form';  
  }  
  
  public function buildForm(array $form, FormStateInterface $form_state) {  
   $form['category'] = [  
    '#type' => 'textfield',  
    '#title' => 'Category',  
    '#placeholder' => 'Search by category',  
   ];  
  
   $form['submit'] = [  
    '#type' => 'submit',  
    '#value' => 'Search',  
   ];  
  
   return $form;  
  }  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
   $category = $form_state->getValue('category');  
   $data = \Drupal::service('bribe.service')->getData($category);  
   // Update the table with the search results  
   $form_state->setRedirect('promo', ['data' => $data]);  
  }  
}