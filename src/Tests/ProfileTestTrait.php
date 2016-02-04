<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileTestTrait.
 */

namespace Drupal\profile\Tests;

use Drupal\profile\Entity\ProfileType;

/**
 * Provides methods to create additional profiles and profile_types
 *
 * This trait is meant to be used only by test classes extending
 * \Drupal\simpletest\TestBase or Drupal\KernelTests\KernelTestBase.
 */
trait ProfileTestTrait {

  /**
   * Creates a profile type for tests.
   *
   * @param string $id
   *   The profile type machine name.
   * @param string $label
   *   The profile type human display name.
   * @param bool|FALSE $registration
   *   Boolean if profile type shows on registration form.
   * @param array $roles
   *   Array of user role machine names.
   *
   * @return \Drupal\profile\Entity\ProfileInterface
   *   Returns a profile type entity.
   */
  protected function createProfileType($id = NULL, $label = NULL, $registration = FALSE, $roles = []) {
    $id = !empty($id) ? $id : $this->randomMachineName();
    $label = !empty($label) ? $label : $this->randomMachineName();

    $type = ProfileType::create([
      'id' => $id,
      'label' => $label,
      'registration' => $registration,
      'roles' => $roles,
    ]);
    $type->save();
    $this->container->get('router.builder')->rebuild();
    return $type;
  }

}
