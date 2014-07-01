<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ThemeProfileEditLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides dynamic tabs based on active themes.
 */
class ThemeProfileEditLocalTask extends DerivativeBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // @todo: is there a better way than this?
    $current_path = current_path();
    $args = explode('/', $current_path);
    if ((count($args) == 5) && ($args[0] == 'user') && ($args[2] == 'edit') && drupal_strlen($args[3])) {
      $config = \Drupal::config('profile.type.' . $args[3]);
    }

    if (!isset($config) || !$config instanceof \Drupal\Core\Config\Config) {
      return;
    }

    $this->derivatives[$config->get('id')] = $base_plugin_definition;
    $this->derivatives[$config->get('id')]['title'] = \Drupal::translation()
      ->translate('Edit @type profile', array('@type' => $config->get('label')));
    $this->derivatives[$config->get('id')]['route_parameters'] = array(
      'type' => $config->get('id'),
      'id' => $args[4]
    );
    // Clear the page cache because pages can contain tab information.
    Cache::invalidateTags(array('content' => TRUE));

    return $this->derivatives;
  }

}
