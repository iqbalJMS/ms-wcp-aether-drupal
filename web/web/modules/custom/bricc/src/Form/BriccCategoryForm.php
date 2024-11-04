<?php

declare(strict_types=1);

namespace Drupal\bricc\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the category entity edit forms.
 */
final class BriccCategoryForm extends ContentEntityForm {

  /**
   * @inheritDoc
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form(
      $form,
      $form_state
    );
    $form['card_count']['#access'] = FALSE;
//    $form['status']['#access'] = FALSE;
    $form['uid']['#access'] = FALSE;
    $form['created']['#access'] = FALSE;
    $form['status']['#access'] = FALSE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->toLink()->toString()];
    $logger_args = [
      '%label' => $this->entity->label(),
      'link' => $this->entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New category %label has been created.', $message_args));
        $this->logger('bricc')->notice('New category %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The category %label has been updated.', $message_args));
        $this->logger('bricc')->notice('The category %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl());

    return $result;
  }

}
