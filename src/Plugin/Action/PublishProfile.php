<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Action\PublishProfile.
 */

namespace Drupal\profile\Plugin\Action;

use Drupal\Core\Action\ActionBase;

/**
 * Publishes a profile.
 *
 * @Action(
 *   id = "profile_publish_action",
 *   label = @Translation("Publish selected profile"),
 *   type = "profile"
 * )
 */
class PublishProfile extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    $entity->status = PROFILE_ACTIVE;
    $entity->save();
  }

}
