<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileAccessController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessControllerInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Entity\EntityAccessController;

//
///**
// * Access controller for profiles.
// */
//class ProfileAccessController extends EntityAccessController {
//
//  /**
//   * Static cache for access checks.
//   *
//   * @var array
//   */
//  protected $accessCache = array();
//
//  /**
//   * Implements EntityAccessControllerInterface::viewAccess().
//   */
//  public function viewAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
//    return $this->access($profile, 'view', $langcode, $account);
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = array()) {
//    $account = $this->prepareUser($account);
//
//    // Create and update operations are folded into edit access for profiles.
////    return $this->access($profile, 'edit', $langcode, $account);
//
//    return parent::createAccess($entity_bundle, $account, $context);
//  }
//
//  /**
//   * Implements EntityAccessControllerInterface::updateAccess().
//   */
//  public function updateAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
//    // Create and update operations are folded into edit access for profiles.
//    return $this->access($profile, 'edit', $langcode, $account);
//  }
//
//  /**
//   * Implements EntityAccessControllerInterface::deleteAccess().
//   */
//  public function deleteAccess(EntityInterface $profile, $langcode = LANGUAGE_DEFAULT, User $account = NULL) {
//    return $this->access($profile, 'delete', $langcode, $account);
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function access(EntityInterface $entity, $operation, $langcode = Language::LANGCODE_DEFAULT, AccountInterface $account = NULL) {
//    if (!isset($account)) {
//      $account = entity_load('user', $GLOBALS['user']->uid);
//    }
//    // Check for the bypass access permission first. No need to cache this,
//    // since user_access() is cached already.
//    if (user_access('bypass profile access', $account)) {
//      return TRUE;
//    }
//    $uid = $account->id();
//    // For existing profiles, check access for the particular profile ID. When
//    // creating a new profile, check access for the profile's bundle.
//
//    // @todo:
//    $pid = 0;
////    $pid = $profile->id() ?: $profile->bundle();
//
//    if (isset($this->accessCache[$uid][$operation][$pid][$langcode])) {
//      return $this->accessCache[$uid][$operation][$pid][$langcode];
//    }
//
//    $access = NULL;
//    // Ask modules to grant or deny access.
//    foreach (module_implements('profile2_access', $operation, $profile, $account) as $module) {
//      $return = module_invoke($module, 'profile2_access', $operation, $profile, $account);
//      // If a module denies access, there is no point in asking further.
//      if ($return === FALSE) {
//        $access = FALSE;
//        break;
//      }
//      // A module may grant access, but others may still deny.
//      if ($return === TRUE) {
//        $access = TRUE;
//      }
//    }
//    // Final access is only TRUE if any module explicitly returned TRUE. If at
//    // least one returned FALSE, $access will be FALSE. If no module returned
//    // anything, $access will be NULL, which means access is denied.
//    // @see hook_profile2_access()
//    $this->accessCache[$uid][$operation][$pid][$langcode] = ($access === TRUE);
//
//    return $this->accessCache[$uid][$operation][$pid][$langcode];
//  }
//
//}
//
