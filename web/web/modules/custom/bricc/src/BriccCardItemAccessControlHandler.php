<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the card item entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class BriccCardItemAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view bricc_card_item'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit bricc_card_item'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete bricc_card_item'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete bricc_card_item revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view bricc_card_item revision', 'view bricc_card_item']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert bricc_card_item revision', 'edit bricc_card_item']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create bricc_card_item', 'administer bricc_card_item'], 'OR');
  }

}
