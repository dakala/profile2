<?php

/**
 * @file
 * Contains \Drupal\profile2\Entity\ProfileType.
 */


namespace Drupal\profile2\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\profile2\ProfileTypeInterface;
use Drupal\field\FieldInstanceConfigInterface;

/**
 * Defines the profile type entity class.
 *
 * @ConfigEntityType(
 *   id = "profile2_type",
 *   label = @Translation("Profile type"),
 *   controllers = {
 *     "form" = {
 *       "default" = "Drupal\profile2\ProfileTypeFormController",
 *       "add" = "Drupal\profile2\ProfileTypeFormController",
 *       "edit" = "Drupal\profile2\ProfileTypeFormController",
 *       "delete" = "Drupal\profile2\Form\ProfileTypeDeleteForm"
 *     },
 *     "list_builder" = "Drupal\profile2\ProfileTypeListController"
 *   },
 *   admin_permission = "administer profile types",
 *   config_prefix = "type",
 *   bundle_of = "profile2",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "profile2.type_add",
 *     "delete-form" = "profile2.type_delete",
 *     "edit-form" = "profile2.type_edit",
 *     "admin-form" = "profile2.type_edit",
 *   }
 * )
 */
class ProfileType extends ConfigEntityBase implements ProfileTypeInterface {

  /**
   * The primary identifier of the profile type.
   *
   * @var integer
   */
  public $id;

  /**
   * The universally unique identifier of the profile type.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the profile type.
   *
   * @var string
   */
  public $label;

  /**
   * Whether the profile type is shown during registration.
   *
   * @var boolean
   */
  public $registration = FALSE;

  /**
   * Whether the profile type allows multiple profiles.
   *
   * @var boolean
   */
  public $multiple = FALSE;

  /**
   * The weight of the profile type compared to others.
   *
   * @var integer
   */
  public $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->get('label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistration() {
    return $this->get('registration')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFieldInstances() {
    return count($this->getFieldInstances()) ? TRUE : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldInstances() {
    return array_filter(\Drupal::entityManager()
      ->getFieldDefinitions('profile2', $this->id()), function ($field_definition) {
      return $field_definition instanceof FieldInstanceConfigInterface;
    });
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

//    if (!$update) {
//      field_attach_create_bundle('profile2', $entity->id());
//    }
//    elseif ($entity->original->id() != $entity->id()) {
//      field_attach_rename_bundle('profile2', $entity->original->id(), $entity->id());
//    }
  }

  /**
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    parent::preDelete($storage, $entities);

//    // Delete all profiles of this type.
//    if ($profiles = entity_load_multiple_by_properties('profile2', array('type' => array_keys($entities)))) {
//      entity_get_controller('profile2')->delete($profiles);
//    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    foreach ($entities as $entity) {
//      field_attach_delete_bundle('profile2', $entity->id());
    }
  }


}
