<?php

declare(strict_types=1);

namespace Drupal\bricc\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a bricc form.
 */
final class BriccCategoryFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bricc_category_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $request = \Drupal::request();

    $form['filter'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['form--inline', 'clearfix'],
      ],
    ];

    $form['filter']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category title'),
      '#default_value' => $request->get('label') ?? '',
    ];

    $form['filter']['status'] = [
      '#type' => 'select',
      '#title' => 'Status',
      '#options' => [
        '' => '- Any -',
        'active' => 'Active',
        'inactive' => 'Inactive',
      ],
      '#default_value' => $request->get('status') ?? '',
    ];

    $form['filter']['wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['form-item', 'filter-submit']],
    ];

    $form['filter']['wrapper']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Filter',
    ];

    if ($request->getQueryString()) {
      $form['filter']['wrapper']['reset'] = [
        '#type' => 'submit',
        '#value' => 'Reset',
        '#submit' => ['::resetForm'],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

  public function resetForm(array $form, FormStateInterface &$form_state): void {
    $form_state->setRedirect('entity.bricc_category.collection');
  }


}
