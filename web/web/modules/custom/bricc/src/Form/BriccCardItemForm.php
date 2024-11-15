<?php

declare(strict_types=1);

namespace Drupal\bricc\Form;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Form controller for the card item entity edit forms.
 */
final class BriccCardItemForm extends ContentEntityForm {

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
        $this->messenger()->addStatus($this->t('New card item %label has been created.', $message_args));
        $this->logger('bricc')->notice('New card item %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The card item %label has been updated.', $message_args));
        $this->logger('bricc')->notice('The card item %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $entity = $this->getEntity();
    $cache_tags = $entity->getCacheTags();
    Cache::invalidateTags($cache_tags);

    $nid = 3;
    $config_bricc = ConfigPages::config('bri_cc');
    if ($config_bricc instanceof ConfigPages) {
      if (!$config_bricc->get('field_id_jenis_kartu_kredit')->isEmpty()) {
        $nid = $config_bricc->get('field_id_jenis_kartu_kredit')->value;
      }
    }

    $node = Node::load($nid);
    if ($node) {
      // Invalidate the cache for this specific node.
      Cache::invalidateTags($node->getCacheTags());
    }

    $form_state->setRedirectUrl($this->entity->toUrl());

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $form['revision_information']['#group'] = 'advanced';
    $form['advanced']['#type'] = 'fieldset';
    return $form;
  }

}
