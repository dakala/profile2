<?php

/**
 * @file
 * Contains \Drupal\profile2\Plugin\Derivative\ThemeLocalTask.
 */

namespace Drupal\profile2\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;
use Drupal\Component\Utility\Unicode;
use Drupal\user\UserInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides dynamic tabs based on active themes.
 */
class ThemeProfileAddLocalTask extends DerivativeBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $configs = array();
    foreach (\Drupal::configFactory()
               ->listAll('profile2.type.') as $config_name) {
      $config = \Drupal::config($config_name);
      $profileType = entity_load('profile2_type', $config->get('id'));
      // No fields yet.
      if ($profileType->hasFieldInstances() !== TRUE) {
        continue;
      }
      else {
        // Expose profile types that users may create - either they have 0 of non-multiple or multiple.
        if ($config->get('multiple') === FALSE) {
          $user = \Drupal::request()->attributes->get('user');
          if ($user instanceof UserInterface) {
            $profiles = entity_load_multiple_by_properties('profile2', array(
              'uid' => $user->id(),
              'type' => $config->get('id'),
            ));
            // Single profile, none yet.
            if (empty($profiles)) {
              $configs[] = $config;
            }
          }
        }
        else {
          // Multiple profiles allowed.
          $configs[] = $config;
        }
      }
    }

    if (count($configs)) {
      foreach ($configs as $config) {
        $this->derivatives[$config->get('id')] = $base_plugin_definition;
        $this->derivatives[$config->get('id')]['title'] = \Drupal::translation()
          ->translate('Add @type profile', array('@type' => Unicode::strtolower($config->get('label'))));
        $this->derivatives[$config->get('id')]['route_parameters'] = array('type' => $config->get('id'));
      }
    }
    // Clear the page cache because pages can contain tab information.
    Cache::invalidateTags(array('content' => TRUE));

    return $this->derivatives;
  }

}
