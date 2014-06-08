<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileAccessController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the profile entity type.
 */
class ProfileAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return TRUE;
        break;

      case 'update':
        return TRUE;
        break;

      case 'delete':
        return TRUE;
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return TRUE;
  }


}
