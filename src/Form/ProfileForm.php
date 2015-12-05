<?php

/**
 * @file
 * Contains \Drupal\profile\Form\ProfileForm.
 */

namespace Drupal\profile\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\profile\Entity\ProfileType;

/**
 * Form controller for profile forms.
 */
class ProfileForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    $entity = parent::buildEntity($form, $form_state);
    if ($entity->isNew()) {
      $entity->setCreatedTime(REQUEST_TIME);
    }
    return $entity;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityForm::actions().
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $element = parent::actions($form, $form_state);
    $profile = $this->entity;

    if (\Drupal::currentUser()->hasPermission('administer profiles')) {
      // Add an "Activate" button.
      $element['activate'] = $element['submit'];
      $element['activate']['#dropbutton'] = 'save';
      if ($profile->isNew()) {
        $element['activate']['#value'] = t('Save and make active');
      }
      else {
        $element['activate']['#value'] = $profile->isActive() ? t('Save and keep active') : t('Save and make active');
      }
      $element['activate']['#weight'] = 0;
      array_unshift($element['activate']['#submit'], [$this, 'activate']);

      // Add a "Deactivate" button.
      $element['deactivate'] = $element['submit'];
      $element['deactivate']['#dropbutton'] = 'save';
      if ($profile->isNew()) {
        $element['deactivate']['#value'] = t('Save as inactive');
      }
      else {
        $element['deactivate']['#value'] = !$profile->isActive() ? t('Save and keep inactive') : t('Save and make inactive');
      }
      $element['deactivate']['#weight'] = 10;
      array_unshift($element['deactivate']['#submit'], [$this, 'deactivate']);

      // If already deactivated, the 'activate' button is primary.
      if ($profile->isActive()) {
        unset($element['deactivate']['#button_type']);
      }
      // Otherwise, the 'deactivate' button is primary and should come first.
      else {
        unset($element['deactivate']['#button_type']);
        $element['deactivate']['#weight'] = -10;
      }

      // Remove the "Save" button.
      $element['submit']['#access'] = FALSE;
    }

    $element['delete']['#access'] = $profile->access('delete');
    $element['delete']['#weight'] = 100;

    return $element;
  }

  /**
   * Form submission handler for the 'activate' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   A reference to a keyed array containing the current state of the form.
   *
   * @return \Drupal\profile\Entity\ProfileInterface
   *   The current profile entity.
   */
  public function activate(array $form, FormStateInterface $form_state) {
    $profile = $this->entity;
    $profile->setActive(TRUE);
    return $profile;
  }

  /**
   * Form submission handler for the 'deactivate' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   A reference to a keyed array containing the current state of the form.
   *
   * @return \Drupal\profile\Entity\ProfileInterface
   *   The current profile entity.
   */
  public function deactivate(array $form, FormStateInterface $form_state) {
    $profile = $this->entity;
    $profile->setActive(FALSE);
    return $profile;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $profile_type = ProfileType::load($this->entity->bundle());

    // Active profile for non administers if profile is new.
    if (!\Drupal::currentUser()->hasPermission('administer profiles') && $this->entity->isNew()) {
      $this->entity->setActive(TRUE);
    }
    switch ($this->entity->save()) {
      case SAVED_NEW:
        drupal_set_message($this->t('%label profile has been created.', ['%label' => $profile_type->label()]));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('%label profile has been updated.', ['%label' => $profile_type->label()]));
        break;
    }

    $form_state->setRedirect('entity.user.canonical', [
      'user' => $this->entity->getOwnerId(),
    ]);
  }

}
