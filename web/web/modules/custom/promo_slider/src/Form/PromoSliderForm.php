<?php

namespace Drupal\promo_slider\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class PromoSliderForm extends FormBase
{

    public function getFormId()
    {
        return 'promo_slider';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Your email address'),
            '#required' => TRUE,
        ];

        $form['message'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Your message'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#ajax' => [
                'callback' => '::setMessage',
                'wrapper' => 'edit-output',
            ],
        ];

        $form['output'] = [
            '#type' => 'markup',
            '#markup' => '<div id="edit-output"></div>',
        ];

        return $form;
    }

    public function setMessage(array &$form, FormStateInterface $form_state)
    {
        $response = new AjaxResponse();
        $response->addCommand(new HtmlCommand('#edit-output', 'Your message has been sent.'));
        return $response;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // No need to do anything here.
    }
}
