<?php

/**
 * @file
 * Contains \Drupal\profile\Controller\ProfileController.
 */

namespace Drupal\profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\profile\Entity\ProfileTypeInterface;
use Drupal\profile\Entity\Profile;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for ProfileController routes.
 */
class ProfileController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Provides the profile submission form.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user account.
   * @param \Drupal\profile\Entity\ProfileTypeInterface $profile_type
   *   The profile type entity for the profile.
   *
   * @return array
   *   A profile submission form.
   */
  public function addProfile(UserInterface $user, ProfileTypeInterface $profile_type) {

    $profile = $this->entityTypeManager()->getStorage('profile')->create([
      'uid' => $user->id(),
      'type' => $profile_type->id(),
    ]);

    return $this->entityFormBuilder()->getForm($profile, 'add', ['uid' => $user->id(), 'created' => REQUEST_TIME]);
  }

  /**
   * Provides profile delete form.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user account.
   * @param \Drupal\profile\Entity\ProfileTypeInterface $profile_type
   *   The profile type entity for the profile.
   * @param int $id
   *   The id of the profile to delete.
   *
   * @return array
   */
  public function deleteProfile(UserInterface $user, ProfileTypeInterface $profile_type, $id) {
    return $this->entityFormBuilder()->getForm(Profile::load($id), 'delete');
  }

  /**
   * The _title_callback for the entity.profile.add_form route.
   *
   * @param \Drupal\profile\Entity\ProfileTypeInterface $profile_type
   *   The current profile type.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(ProfileTypeInterface $profile_type) {
    // @todo: edit profile uses this form too?
    return $this->t('Create @label', ['@label' => $profile_type->label()]);
  }

  public function userProfileForm(RouteMatchInterface $route_match, UserInterface $user, ProfileTypeInterface $profile_type) {
    $profile = $this->entityTypeManager()->getStorage('profile')->create([
      'uid' => $user->id(),
      'type' => $profile_type->id(),
    ]);

    return $this->entityFormBuilder()->getForm($profile, 'add', ['uid' => $user->id(), 'created' => REQUEST_TIME]);
  }

}
