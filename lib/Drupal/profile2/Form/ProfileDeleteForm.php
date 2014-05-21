<?php

/**
 * @file
 * Contains \Drupal\block\Form\CustomBlockDeleteForm.
 */

namespace Drupal\profile2\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;

/**
 * Provides a confirmation form for deleting a profile entity.
 */
class ProfileDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'block.admin_display',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $instances = $this->entity->getInstances();

    $form['message'] = array(
      '#markup' => format_plural(count($instances), 'This will also remove 1 profile instance.', 'This will also remove @count profile instances.'),
      '#access' => !empty($instances),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Profile %label has been deleted.', array('%label' => $this->entity->label())));
    watchdog('profile2', 'Profile %label has been deleted.', array('%label' => $this->entity->label()), WATCHDOG_NOTICE);
    $form_state['redirect_route']['route_name'] = 'profile.list';
  }

}
