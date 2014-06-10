<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileInterface.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a custom block entity.
 */
interface ProfileInterface extends ContentEntityInterface, EntityChangedInterface {

}
