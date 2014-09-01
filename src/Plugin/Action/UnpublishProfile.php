<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Action\UnpublishProfile.
 */

namespace Drupal\profile\Plugin\Action;

use Drupal\Core\Action\ActionBase;

/**
 * Unpublishes a profile.
 *
 * @Action(
 *   id = "profile_unpublish_action",
 *   label = @Translation("Unpublish selected profile"),
 *   type = "profile"
 * )
 */
class UnpublishProfile extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    $entity->status = PROFILE_NOT_ACTIVE;
    $entity->save();
  }

}
