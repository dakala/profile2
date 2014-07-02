<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileFormController.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\profile\Entity\ProfileType;

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
      $entity->setCreatedTime(REQUEST_TIME);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $profile_type = ProfileType::load($this->entity->bundle());
    switch ($this->entity->save()) {
      case SAVED_NEW:
        drupal_set_message(t('%label profile has been created.', array('%label' => $profile_type->label())));
        break;
      case SAVED_UPDATED:
        drupal_set_message(t('%label profile has been updated.', array('%label' => $profile_type->label())));
        break;
    }

    $form_state['redirect_route'] = array(
      'route_name' => 'user.view',
      'route_parameters' => array(
        'user' => $this->entity->getOwnerId(),
      ),
    );
  }

}
