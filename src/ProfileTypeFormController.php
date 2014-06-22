<?php

/**
 * @file
 * Contains \Drupal\profile2\ProfileTypeFormController.
 */

namespace Drupal\profile2;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityForm;

/**
 * Form controller for profile type forms.
 */
class ProfileTypeFormController extends EntityForm {

  /**
   * {@inheritdoc}
   */
  function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);
    $type = $this->entity;

    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The human-readable name of this profile type.'),
      '#required' => TRUE,
      '#size' => 30,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => 'profile2_type_load',
      ),
    );
    $form['registration'] = array(
      '#type' => 'checkbox',
      '#title' => t('Include in user registration form'),
      '#default_value' => $type->registration,
    );
    $form['multiple'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow multiple profiles'),
      '#default_value' => $type->multiple,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    if (\Drupal::moduleHandler()
        ->moduleExists('field_ui') && $this->getEntity($form_state)->isNew()
    ) {
      $actions['save_continue'] = $actions['submit'];
      $actions['save_continue']['#value'] = t('Save and manage fields');
      $actions['save_continue']['#submit'][] = array(
        $this,
        'redirectToFieldUI'
      );
    }
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $type = $this->entity;
    $status = $type->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('%label profile type has been updated.', array('%label' => $type->label())));
    }
    else {
      drupal_set_message(t('%label profile type has been created.', array('%label' => $type->label())));
    }
    $form_state['redirect_route']['route_name'] = 'profile2.overview_types';
  }

  /**
   * Form submission handler to redirect to Manage fields page of Field UI.
   */
  public function redirectToFieldUI(array $form, array &$form_state) {
    // $form_state['redirect_route'] = '<front>';

    $form_state['redirect_route'] = array(
     'route_name' => 'field_ui.overview_profile2',
     'route_parameters' => array(
       'profile2_type' => $this->entity->id(),
     ),
    );
 }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect_route'] = array(
      'route_name' => 'profile2.type_delete',
      'route_parameters' => array(
        'profile2_type' => $this->entity->id(),
      ),
    );
  }

}
