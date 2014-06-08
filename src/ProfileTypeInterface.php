<?php

/**
 * @file
 * Contains \Drupal\profile2\Entity\ProfileTypeInterface.
 */

namespace Drupal\profile2;

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
}
