<?php

/**
 * @file
 * Contains \Drupal\profile2\Plugin\Derivative\ThemeLocalTask.
 */

namespace Drupal\profile2\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;
use Drupal\field\FieldInstanceConfigInterface;
use Drupal\Component\Utility\Unicode;
use Drupal\user\UserInterface;

/**
 * Provides dynamic tabs based on active themes.
 */
class ThemeLocalTask extends DerivativeBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach (\Drupal::configFactory()
               ->listAll('profile2.type.') as $config_name) {
      $config = \Drupal::config($config_name);
      // Do not expose profile types that do not have any fields attached yet.
      $instances = array_filter(\Drupal::entityManager()
        ->getFieldDefinitions('profile2', $config->get('id')), function ($field_definition) {
        return $field_definition instanceof FieldInstanceConfigInterface;
      });

      if (!$instances) {
        continue;
      }

      // Expose profile types that users may create - either they have 0 of non-multiple or multiple.
      if ($config->get('multiple') === FALSE) {
        $user = \Drupal::request()->attributes->get('user');
        if ($user instanceof UserInterface) {
          $profiles = entity_load_multiple_by_properties('profile2', array(
            'uid' => $user->id(),
            'type' => $config->get('id'),
          ));
          if (isset($profiles)) {
            continue;
          }
        }
      }

      $this->derivatives[$config_name] = $base_plugin_definition;
      $this->derivatives[$config_name]['title'] = \Drupal::translation()
        ->translate('Add @type profile', array('@type' => Unicode::strtolower($config->get('label'))));
      $this->derivatives[$config_name]['route_parameters'] = array('type' => $config->get('id'));
    }

    return $this->derivatives;
  }


}
