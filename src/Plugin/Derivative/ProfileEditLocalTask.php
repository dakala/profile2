<?php

/**
 * @file
 * Contains \Drupal\profile\Plugin\Derivative\ProfileEditLocalTask.
 */

namespace Drupal\profile\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\Config;

/**
 * Provides dynamic routes to edit profiles.
 */
class ProfileEditLocalTask extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    if (\Drupal::routeMatch()->getRouteName() == 'profile.account_edit_profile') {
      $params = \Drupal::routeMatch()->getParameters()->all();
      if (isset($params['type']) && $params['id']) {
        $config = \Drupal::config('profile.type.' . $params['type']);
        if ($config instanceof Config) {
          $this->derivatives[$config->get('id')] = $base_plugin_definition;
          $this->derivatives[$config->get('id')]['title'] = \Drupal::translation()
            ->translate('Edit @type profile', array('@type' => $config->get('label')));
          $this->derivatives[$config->get('id')]['route_parameters'] = array(
            'type' => $config->get('id'),
            'id' => $params['id']
          );
        }
      }
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
