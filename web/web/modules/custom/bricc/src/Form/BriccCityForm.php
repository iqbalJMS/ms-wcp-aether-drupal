<?php

declare(strict_types=1);

namespace Drupal\bricc\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the city entity edit forms.
 */
final class BriccCityForm extends ContentEntityForm {

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
        $this->messenger()->addStatus($this->t('New city %label has been created.', $message_args));
        $this->logger('bricc')->notice('New city %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The city %label has been updated.', $message_args));
        $this->logger('bricc')->notice('The city %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form(
      $form,
      $form_state
    );
    $form['uid']['#access'] = FALSE;
    $form['created']['#access'] = FALSE;
    $form['status']['#access'] = FALSE;

    return $form;
  }

}
