<?php

/**
 * @file
 * Contains \Drupal\profile2\Entity\Profile.
 */

namespace Drupal\profile2\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;
use Drupal\profile2\ProfileInterface;
use Drupal\user\UserInterface;

/**
 * Defines the profile entity class.
 *
 * @ContentEntityType(
 *   id = "profile2",
 *   label = @Translation("Profile"),
 *   bundle_label = @Translation("Profile"),
 *   controllers = {
 *     "access" = "Drupal\profile2\ProfileAccessController",
 *     "form" = {
 *       "default" = "Drupal\profile2\ProfileFormController",
 *       "add" = "Drupal\profile2\ProfileFormController",
 *       "edit" = "Drupal\profile2\ProfileFormController",
 *       "delete" = "Drupal\profile2\Form\ProfileDeleteForm",
 *     },
 *   },
 *   bundle_entity_type = "profile2_type",
 *   admin_permission = "administer profiles",
 *   base_table = "profile",
 *   links = {
 *     "canonical" = "profile2.edit",
 *     "delete-form" = "profile2.delete",
 *     "edit-form" = "profile2.edit",
 *     "admin-form" = "profile2.type_edit"
 *   },
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "bundle" = "type",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Profile extends ContentEntityBase implements ProfileInterface {


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['pid'] = FieldDefinition::create('integer')
      ->setLabel(t('Profile ID'))
      ->setDescription(t('The profile ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The profile UUID.'))
      ->setReadOnly(TRUE);

    $fields['type'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Profile type'))
      ->setDescription(t('The profile type.'))
      ->setSetting('target_type', 'profile2_type')
      ->setSetting('max_length', EntityTypeInterface::BUNDLE_MAX_LENGTH);

    $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the user associated with the profile.'))
      ->setSetting('target_type', 'user')
      ->setSetting('default_value', 0);

    $fields['langcode'] = FieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The profile language code.'));

    $fields['label'] = FieldDefinition::create('string')
      ->setLabel(t('Profile description'))
      ->setDescription(t('A brief description of your profile.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = FieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the profile was created.'));

    $fields['changed'] = FieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the profile was last edited.'));

    return $fields;
  }

  /**
   * Overrides Entity::id().
   */
  public function id() {
    return $this->get('pid')->value;
  }

  /**
   * Overrides Entity::label().
   */
  public function label() {
    return $this->get('label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->set('type', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
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
  public function setLabel($label) {
    $this->set('label', $label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreated() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreated($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

}
