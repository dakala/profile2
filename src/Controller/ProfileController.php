<?php

/**
 * @file
 * Contains \Drupal\node\Controller\NodeController.
 */

namespace Drupal\profile2\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use Drupal\profile2\ProfileTypeInterface;
use Drupal\profile2\ProfileInterface;

/**
 * Returns responses for Node routes.
 */
class ProfileController extends ControllerBase {

  /**
   * Provides the profile submission form.
   *
   * @param \Drupal\profile2\ProfileTypeInterface $type
   *   The node type entity for the node.
   *
   * @return array
   *   A node submission form.
   */
  public function add($user, $type) {
    // @todo: edit profile uses this form too.
    // @todo: check access if not current user.
    // @todo: deny access if this profile exists - multiple profiles allowed?
    
    $config = \Drupal::config('profile2.type.' . $type);
    $langcode = $config->get('langcode');

    $profile = $this->entityManager()->getStorage('profile2')->create(array(
      'uid' => $user,
      'type' => $config->get('id'),
      'langcode' => $langcode ? $langcode : $this->languageManager()->getCurrentLanguage()->id,
    ));

    $form = $this->entityFormBuilder()->getForm($profile, 'add', array('uid' => $user, 'created' => REQUEST_TIME));

    return $form;
  }

  /**
   * The _title_callback for the profile2.account_edit_profile route.
   *
   * @param \Drupal\profile2\ProfileTypeInterface $profile_type
   *   The current profile type.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(ProfileTypeInterface $profile_type) {
    // @todo: edit profile uses this form too.
    return $this->t('Create @label', array('@label' => $profile_type->label()));
  }

}
