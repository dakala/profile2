<?php

/**
 * @file
 * Contains \Drupal\aggregator\FeedViewBuilder.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for profile entities.
 */
class ProfileViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getBuildDefaults(EntityInterface $entity, $view_mode, $langcode) {
    $defaults = parent::getBuildDefaults($entity, $view_mode, $langcode);
    return $defaults;
  }

}
