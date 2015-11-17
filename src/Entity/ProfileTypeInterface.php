<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\ProfileTypeInterface.
 */

namespace Drupal\profile\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a profile type entity.
 */
interface ProfileTypeInterface extends ConfigEntityInterface {

  /**
   * Returns the label of the profile type.
   */
  public function getLabel();

  /**
   * Return the registration form flag.
   *
   * For allowing creation of profile type at user registration.
   */
  public function getRegistration();

  /**
   * Returns the profile type's weight.
   *
   * @return int
   *   The weight.
   */
  public function getWeight();

  /**
   * Sets the profile type's weight.
   *
   * @param int $weight
   *   The profile type's weight.
   *
   * @return $this
   */
  public function setWeight($weight);

}
