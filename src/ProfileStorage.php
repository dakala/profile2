<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileStorage.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the entity storage for profile.
 */
class ProfileStorage extends SqlContentEntityStorage implements ProfileStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByUser(AccountInterface $account, $profile_type, $active = PROFILE_ACTIVE) {
    $result = $this->loadByProperties([
      'uid' => $account->id(),
      'type' => $profile_type,
      'status' => $active,
    ]);

    return reset($result);
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultipleByUser(AccountInterface $account, $profile_type, $active = PROFILE_ACTIVE) {
    return $this->loadByProperties([
        'uid' => $account->id(),
        'type' => $profile_type,
        'status' => $active,
      ]);
  }

}
