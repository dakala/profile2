<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ProfileAddLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Utility\Unicode;
use Drupal\field\FieldInstanceConfigInterface;
use Drupal\user\UserInterface;

/**
 * Provides dynamic routes to add profiles.
 */
class ProfileAddLocalTask extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $user = \Drupal::request()->attributes->get('user');
    if (!$user instanceof UserInterface) {
      return;
    }

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
        $display = FALSE;
        // Expose profile types that users may create - either they have 0 of non-multiple or multiple.
        if ($config->get('multiple') === FALSE) {
          $profiles = \Drupal::entityManager()
            ->getStorage('profile')
            ->loadByProperties(array(
              'uid' => $user->id(),
              'type' => $config->get('id'),
            ));
          // Single profile, none yet.
          if (!count($profiles)) {
            $display = TRUE;
          }
        }
        else {
          // Multiple profiles allowed.
          $display = TRUE;
        }

        if ($display) {
          $id = $config->get('id');
          $this->derivatives[$id] = $base_plugin_definition;
          $this->derivatives[$id]['title'] = \Drupal::translation()
            ->translate('Add @type profile', array('@type' => Unicode::strtolower($config->get('label'))));
          $this->derivatives[$id]['route_parameters'] = array(
            'user' => $user->id(),
            'type' => $id
          );
        }
      }
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
