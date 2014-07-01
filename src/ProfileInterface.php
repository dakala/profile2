<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileInterface.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a custom block entity.
 */
interface ProfileInterface extends ContentEntityInterface, EntityChangedInterface {

}
