<?php

/**
 * @file
 * Contains \Drupal\profile\Entity\Profile.
 */

namespace Drupal\profile\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\profile\ProfileInterface;
use Drupal\profile\ProfileTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the profile entity class.
 *
 * @ContentEntityType(
 *   id = "profile",
 *   label = @Translation("Profile"),
 *   bundle_label = @Translation("Profile"),
 *   controllers = {
 *     "view_builder" = "Drupal\profile\ProfileViewBuilder",
 *     "form" = {
 *       "default" = "Drupal\profile\ProfileFormController",
 *       "add" = "Drupal\profile\ProfileFormController",
 *       "edit" = "Drupal\profile\ProfileFormController",
 *       "delete" = "Drupal\profile\Form\ProfileDeleteForm",
 *     },
 *   },
 *   bundle_entity_type = "profile_type",
 *   admin_permission = "administer profiles",
 *   base_table = "profile",
 *   revision_table = "profile_revision",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *  links = {
 *    "canonical" = "profile.overview_types",
 *    "admin-form" = "profile.type_edit"
 *   },
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

    $fields['vid'] = FieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The profile revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['type'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Profile type'))
      ->setDescription(t('The profile type.'))
      ->setSetting('target_type', 'profile_type')
      ->setSetting('max_length', EntityTypeInterface::BUNDLE_MAX_LENGTH);

    $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the user associated with the profile.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setTranslatable(TRUE);

    $fields['langcode'] = FieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The profile language code.'))
      ->setRevisionable(TRUE);

    $fields['created'] = FieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the profile was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['changed'] = FieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the profile was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Overrides Entity::id().
   */
  public function id() {
    return $this->get('pid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $profile_type = ProfileType::load($this->bundle());
    return t('@type profile of user @uid', array('@type' => $profile_type->label(), '@uid' => $this->getOwnerId()));
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
    return $this->get('uid')->target_id;
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
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

}
