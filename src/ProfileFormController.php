<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityInterface;

/**
 * Form controller for profile forms.
 */
class ProfileFormController extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $profile = $this->entity;

    return parent::form($form, $form_state, $profile);
  }

  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);

  }

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
    $status = $profile->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('%label profile has been updated.', array('%label' => $profile->id())));
    }
    else {
      drupal_set_message(t('%label profile has been created.', array('%label' => $profile->id())));
    }
    $form_state['redirect_route']['route_name'] = 'user.view';
  }

}
