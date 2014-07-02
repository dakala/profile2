<?php

/**
 * @file
 * Contains \Drupal\profile\Controller\ProfileController.
 */

namespace Drupal\profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\profile\ProfileTypeInterface;

/**
 * Returns responses for ProfileController routes.
 */
class ProfileController extends ControllerBase {

  /**
   * Provides the profile submission form.
   *
   * @param \Drupal\profile\ProfileTypeInterface $type
   *   The profile type entity for the node.
   *
   * @return array
   *   A profile submission form.
   */
  public function addProfile($user, $type) {
    $config = \Drupal::config('profile.type.' . $type);
    $langcode = $config->get('langcode');

    $profile = $this->entityManager()->getStorage('profile')->create(array(
      'uid' => $user,
      'type' => $config->get('id'),
      'langcode' => $langcode ? $langcode : $this->languageManager()->getCurrentLanguage()->id,
    ));

    return $this->entityFormBuilder()->getForm($profile, 'add', array('uid' => $user, 'created' => REQUEST_TIME));
  }

  /**
   * Provides profile edit form.
   *
   * @param $user
   * @param $type
   * @param $id
   *
   * @return array
   */
  public function editProfile($user, $type, $id) {
    return $this->entityFormBuilder()->getForm(entity_load('profile', $id), 'edit', array('changed' => REQUEST_TIME));
  }

  /**
   * Provides profile delete form.
   *
   * @param $user
   * @param $type
   * @param $id
   *
   * @return array
   */
  public function deleteProfile($user, $type, $id) {
    return $this->entityFormBuilder()->getForm(entity_load('profile', $id), 'delete');
  }

  /**
   * The _title_callback for the profile.account_add_profile route.
   *
   * @param \Drupal\profile\ProfileTypeInterface $profile_type
   *   The current profile type.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(ProfileTypeInterface $profile_type) {
    // @todo: edit profile uses this form too?
    return $this->t('Create @label', array('@label' => $profile_type->label()));
  }

}
