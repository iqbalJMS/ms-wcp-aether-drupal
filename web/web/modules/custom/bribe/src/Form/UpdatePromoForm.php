<?php

    namespace Drupal\bribe\Form;  
  
use Drupal\Core\Form\FormBase;  
use Drupal\Core\Form\FormStateInterface;  
  
class UpdatePromoForm extends FormBase {  
  public function getFormId() {  
   return 'update_promo_form';  
  }  
  
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {  
   $promo = \Drupal::service('bribe.service')->getPromoById($id);  
  
   $form['id'] = [  
    '#type' => 'hidden',  
    '#value' => $id,  
   ];  
  
   $form['categoryIds'] = [  
    '#type' => 'textfield',  
    '#title' => 'Category IDs',  
    '#required' => TRUE,  
    '#default_value' => $promo['category']['_id'],  
   ];  
  
   $form['endDate'] = [  
    '#type' => 'date',  
    '#title' => 'End Date',  
    '#required' => TRUE,  
    '#default_value' => $promo['endDate'],  
   ];  
  
   $form['imagePromoUrl'] = [  
    '#type' => 'textfield',  
    '#title' => 'Image Promo URL',  
    '#required' => TRUE,  
    '#default_value' => $promo['imagePromoUrl'],  
   ];  
  
   $form['lokasiPromo'] = [  
    '#type' => 'textarea',  
    '#title' => 'Lokasi Promo',  
    '#required' => TRUE,  
    '#default_value' => $promo['lokasiPromo'],  
   ];  
  
   $form['micrositeOwnerIds'] = [  
    '#type' => 'textfield',  
    '#title' => 'Microsite Owner IDs',  
    '#required' => TRUE,  
    '#default_value' => $promo['micrositeOwner']['_id'],  
   ];  
  
   $form['promoTitle'] = [  
    '#type' => 'textfield',  
    '#title' => 'Promo Title',  
    '#required' => TRUE,  
    '#default_value' => $promo['promoTitle'],  
   ];  
  
   $form['startDate'] = [  
    '#type' => 'date',  
    '#title' => 'Start Date',  
    '#required' => TRUE,  
    '#default_value' => $promo['startDate'],  
   ];  
  
   $form['subCategoryIds'] = [  
    '#type' => 'textarea',  
    '#title' => 'Sub Category IDs',  
    '#required' => TRUE,  
    '#default_value' => $promo['subCategory']['_id'],  
   ];  
  
   $form['termsAndConditions'] = [  
    '#type' => 'textarea',  
    '#title' => 'Terms and Conditions',  
    '#required' => TRUE,  
    '#default_value' => $promo['termsAndConditions'],  
   ];  
  
   $form['submit'] = [  
    '#type' => 'submit',  
    '#value' => 'Update Promo',  
   ];  
  
   return $form;  
  }  
  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
   $id = $form_state->getValue('id');  
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
  
   $result = \Drupal::service('bribe.service')->updateData($id, $data);  
  
   if ($result) {  
    \Drupal::messenger()->addMessage('Promo updated successfully!');  
   } else {  
    \Drupal::messenger()->addError('Error updating promo!');  
   }  
  }  
}