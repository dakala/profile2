<?php

/**
 * @file
 * Contains \Drupal\profile2\Plugin\Derivative\ThemeProfileEditLocalTask.
 */

namespace Drupal\profile2\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;

/**
 * Provides dynamic tabs based on active themes.
 */
class ThemeProfileEditLocalTask extends DerivativeBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $current_path = current_path();
    $args = explode('/', $current_path);
    if ((count($args) == 5) && ($args[0] == 'user') && ($args[2] == 'edit') && drupal_strlen($args[3])) {
      $config = \Drupal::config('profile2.type.' . $args[3]);
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

    return $this->derivatives;
  }

}
