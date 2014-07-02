<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileInterface.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface defining a custom block entity.
 */
interface ProfileInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  public function getType();

  public function setType($type);

  public function getOwnerId();

  public function setOwnerId($uid);

  public function getOwner();

  public function setOwner(UserInterface $account);

  public function getCreatedTime();

  public function setCreatedTime($timestamp);

  public function getChangedTime();

  public function getRevisionCreationTime();

  public function setRevisionCreationTime($timestamp);

  public function getRevisionAuthor();

  public function setRevisionAuthorId($uid);

}
