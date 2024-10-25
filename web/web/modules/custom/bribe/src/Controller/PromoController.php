<?php

namespace Drupal\bribe\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\bribe\Form\UpdatePromoForm;
use Drupal\bribe\Form\DeletePromoForm;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Url;

class PromoController extends ControllerBase
{
    public function promo()
    {
        $block_manager = \Drupal::service('plugin.manager.block');
        $block = $block_manager->createInstance('promo', []);
        $render_array = $block->build();
        return [
            '#theme' => 'promo',
            '#data' => $render_array
        ];
    }

    public function addPromo()
    {
        \Drupal::logger('bribe')->debug('promo add called');
        $form = \Drupal::formBuilder()->getForm('Drupal\bribe\Form\AddPromoForm');
        return $form;
    }

    public function updatePromo($id)
    {
        $form = new UpdatePromoForm();
        $form_state = new \Drupal\Core\Form\FormState();
        $form = $form->buildForm([], $form_state, $id);
        return [
            '#theme' => 'update_promo_dialog',
            '#form' => $form,
        ];
    }

    public function deletePromo($id)
    {
        $form = new DeletePromoForm();
        $form_state = new \Drupal\Core\Form\FormState();
        $form = $form->buildForm([], $form_state, $id);
        return [
            '#theme' => 'delete_promo_dialog',
            '#form' => $form,
        ];
    }
}