<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ThemeLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;
use Drupal\Component\Utility\Unicode;
use Drupal\user\UserInterface;
use Drupal\Core\Cache\Cache;
use Drupal\profile\Entity\ProfileType;
use Drupal\field\FieldInstanceConfigInterface;

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
               ->listAll('profile.type.') as $config_name) {
      $config = \Drupal::config($config_name);

      $instances = array_filter(\Drupal::entityManager()
        ->getFieldDefinitions('profile', $config->get('id')), function ($field_definition) {
        return $field_definition instanceof FieldInstanceConfigInterface;
      });

      // No fields yet.
      if (!count($instances)) {
        continue;
      }
      else {
        // Expose profile types that users may create - either they have 0 of non-multiple or multiple.
        if ($config->get('multiple') === FALSE) {
          $user = \Drupal::request()->attributes->get('user');
          if ($user instanceof UserInterface) {
            $profiles = \Drupal::entityManager()
              ->getStorage('profile')
              ->loadByProperties(array(
                'uid' => $user->id(),
                'type' => $config->get('id'),
              ));
            // Single profile, none yet.
            if (!count($profiles)) {
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
        $id = $config->get('id');
        $this->derivatives[$id] = $base_plugin_definition;
        $this->derivatives[$id]['title'] = \Drupal::translation()
          ->translate('Add @type profile', array('@type' => Unicode::strtolower($config->get('label'))));
        $this->derivatives[$id]['route_parameters'] = array('type' => $id);
      }
    }
    // Clear the page cache because pages can contain tab information.
    Cache::invalidateTags(array('content' => TRUE));

    return $this->derivatives;
  }

}
