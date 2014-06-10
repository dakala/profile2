<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityForm;

/**
 * Form controller for profile forms.
 */
class ProfileFormController extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    $entity = parent::buildEntity($form, $form_state);
    if ($entity->isNew()) {
      $entity->setCreated(REQUEST_TIME);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $profile = $this->entity;
    $profile_type = entity_load('profile2_type', $profile->getType());
    switch ($profile->save()) {
      case SAVED_NEW:
        drupal_set_message(t('%label profile has been created.', array('%label' => $profile_type->label())));
        break;
      case SAVED_UPDATED:
        drupal_set_message(t('%label profile has been updated.', array('%label' => $profile_type->label())));
        // @todo:
        Cache::invalidateTags(array('content' => TRUE));
        break;
    }

    $form_state['redirect_route'] = array(
      'route_name' => 'user.view',
      'route_parameters' => array(
        'user' => $profile->getOwnerId(),
      ),
    );
  }

}
