<?php

/**
 * @file
 * Contains \Drupal\profile2\Entity\Profile.
 */

namespace Drupal\profile2\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Entity;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\profile2\ProfileInterface;

/**
 * Defines the profile entity class.
 *
 * @ContentEntityType(
 *   id = "profile2",
 *   label = @Translation("Profile"),
 *   bundle_label = @Translation("Profile type"),
 *   controllers = {
 *     "access" = "Drupal\profile2\ProfileAccessController",
 *     "form" = {
 *       "default" = "Drupal\profile2\ProfileFormController",
 *       "add" = "Drupal\profile2\ProfileFormController",
 *       "edit" = "Drupal\profile2\ProfileFormController",
 *       "delete" = "Drupal\profile2\Form\ProfileDeleteForm",
 *     },
 *   },
 *   admin_permission = "administer profiles",
 *   base_table = "profile",
 *   links = {
 *     "canonical" = "profile2.edit",
 *     "delete-form" = "profile2.delete",
 *     "edit-form" = "profile2.edit",
 *     "admin-form" = "profile2.type_edit"
 *   },
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "bundle" = "type",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "profile2"
 * )
 */
class Profile extends ContentEntityBase implements ProfileInterface {

  /**
   * The profile id.
   *
   * @var integer
   */
  public $pid;

  /**
   * The profile UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The name of the profile type.
   *
   * @var string
   */
  public $type;

  /**
   * The language code of the profile.
   *
   * @var
   */
  public $langcode;

  /**
   * The profile label.
   *
   * @var string
   */
  public $label;

  /**
   * The user id of the profile owner.
   *
   * @var integer
   */
  public $uid;

  /**
   * The Unix timestamp when the profile was created.
   *
   * @var integer
   */
  public $created;

  /**
   * The Unix timestamp when the profile was most recently saved.
   *
   * @var integer
   */
  public $changed;

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
    return isset($this->pid) ? $this->pid : NULL;
  }

  /**
   * Overrides Entity::bundle().
   */
  public function bundle() {
    return $this->type;
  }

  /**
   * Overrides Entity::label().
   */
  public function label($langcode = NULL) {
    // If this profile has a custom label, use it. Otherwise, use the label of
    // the profile type.
    if (isset($this->label) && $this->label !== '') {
      return $this->label;
    }
    else {
      return entity_load('profile2_type', $this->type)->label($langcode);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

}
