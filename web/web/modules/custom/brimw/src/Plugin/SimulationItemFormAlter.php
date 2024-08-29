<?php

namespace Drupal\brimw\Plugin\FormAlter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\pluginformalter\Plugin\FormAlterBase;

/**
 * Class SimulationItemFormAlter.
 *
 * @InlineEntityFormAlter(
 *   id = "brimw_form_alter",
 *   label = @Translation("Altering simulation item paragraphs form."),
 *   type = "entity_form",
 *   entity_type = "node",
 *   bundle = "*"
 * )
 *
 * @package Drupal\brimw\Plugin\FormAlter
 */
class SimulationItemFormAlter extends FormAlterBase {

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id) {
    $form['dummy_markup'] = [
      '#markup' => '<h3>Here we go</h3>',
    ];
    dd($form);
  }

}