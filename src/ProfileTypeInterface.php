<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\ProfileTypeInterface.
 */

namespace Drupal\profile;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a profile type entity.
 */
interface ProfileTypeInterface extends ConfigEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function id();

  /**
   * {@inheritdoc}
   */
  public function getLabel();

  /**
   * {@inheritdoc}
   */
  public function getRegistration();

}
