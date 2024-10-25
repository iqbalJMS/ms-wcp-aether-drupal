<?php

namespace Drupal\bribe\Form;  
  
use Drupal\Core\Form\FormBase;  
use Drupal\Core\Form\FormStateInterface;  
  
class AddPromoForm extends FormBase {  
  public function getFormId() {  
   return 'add_promo_form';  
  }  
  
  public function buildForm(array $form, FormStateInterface $form_state) {  
  
   $form['promoTitle'] = [  
    '#type' => 'textfield',  
    '#title' => 'Promo Title',  
    '#required' => TRUE,
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];
   
   $form['categoryIds'] = [  
    '#type' => 'textfield',  
    '#title' => 'Category IDs',  
    '#required' => TRUE,  
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ]; 

   $form['subCategoryIds'] = [  
    '#type' => 'textfield',  
    '#title' => 'Sub Category IDs',  
    '#required' => TRUE,  
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];  
  
   $form['imagePromoUrl'] = [  
    '#type' => 'textfield',  
    '#title' => 'Image Promo URL',  
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];  
  
   $form['lokasiPromo'] = [  
    '#type' => 'textfield',  
    '#title' => 'Lokasi Promo',  
    '#required' => TRUE,  
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];  
  
   $form['micrositeOwnerIds'] = [  
    '#type' => 'textfield',  
    '#title' => 'Microsite Owner IDs',  
    '#required' => TRUE,  
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];
  
   $form['startDate'] = [  
    '#type' => 'date',  
    '#title' => 'Start Date',  
    '#attributes' => [
      'style' => 'min-width: 100%;'
    ],
   ];  
   $form['endDate'] = [  
    '#type' => 'date',  
    '#title' => 'End Date',
    '#attributes' => [
      'style' => 'min-width: 100%;'
    ],
   ];  
  
   $form['termsAndConditions'] = [  
    '#type' => 'textarea',  
    '#title' => 'Terms and Conditions', 
    '#attributes' => [
      'style' => 'width: 100%;'
    ],
   ];  
  
   $form['submit'] = [  
    '#type' => 'submit',  
    '#value' => 'Add Promo',  
   ];  
  
   return $form;  
  }  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
   $data = [  
    'categoryIds' => $form_state->getValue('categoryIds'),  
    'endDate' => $form_state->getValue('endDate'),  
    'imagePromoUrl' => $form_state->getValue('imagePromoUrl'),  
    'lokasiPromo' => $form_state->getValue('lokasiPromo'),  
    'micrositeOwnerIds' => $form_state->getValue('micrositeOwnerIds'),  
    'promoTitle' => $form_state->getValue('promoTitle'),  
    'startDate' => $form_state->getValue('startDate'),  
    'subCategoryIds' => $form_state->getValue('subCategoryIds'),  
    'termsAndConditions' => $form_state->getValue('termsAndConditions'),  
   ];  
  
   $result = \Drupal::service('bribe.service')->createData($data);  
  
   if ($result) {  
    \Drupal::messenger()->addMessage('Promo added successfully!');  
   } else {  
    \Drupal::messenger()->addError('Error adding promo!');  
   }  
  }  
}