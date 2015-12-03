<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileBaseTest.
 */

namespace Drupal\profile\Tests;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Session\AccountInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\profile\Entity\ProfileType;
use Drupal\simpletest\WebTestBase;

/**
 * Tests profile access handling.
 */
abstract class ProfileTestBase extends WebTestBase {

  public static $modules = ['profile', 'field_ui', 'text', 'block'];

  /**
   * @var \Drupal\profile\Entity\ProfileType
   */
  protected $type;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   */
  protected $display;

  /**
   * @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form
   */
  protected $form;

  /**
   * @var \Drupal\field\FieldConfigInterface
   */
  protected $field;

  /**
   * @var \Drupal\user\Entity\User
   */
  protected $admin_user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalPlaceBlock('page_title_block');

    $this->type = ProfileType::create([
      'id' => 'test',
      'label' => 'Test profile',
      'registration' => TRUE,
    ]);
    $this->type->save();
    $id = $this->type->id();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'profile_fullname',
      'entity_type' => 'profile',
      'type' => 'text',
    ]);
    $field_storage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $this->type->id(),
      'label' => 'Full name',
    ]);
    $this->field->save();


    // Configure the default display.
    $this->display = EntityViewDisplay::load("profile.{$this->type->id()}.default");
    if (!$this->display) {
      $this->display = EntityViewDisplay::create([
        'targetEntityType' => 'profile',
        'bundle' => $this->type->id(),
        'mode' => 'default',
        'status' => TRUE,
      ]);
      $this->display->save();
    }
    $this->display
      ->setComponent($this->field->getName(), ['type' => 'string',])
      ->save();

    // Configure rhe default form.
    $this->form = EntityFormDisplay::load("profile.{$this->type->id()}.default");
    if (!$this->form) {
      $this->form = EntityFormDisplay::create([
        'targetEntityType' => 'profile',
        'bundle' => $this->type->id(),
        'mode' => 'default',
        'status' => TRUE,
      ]);
      $this->form->save();
    }
    $this->form
      ->setComponent($this->field->getName(), [
        'type' => 'string_textfield',
      ])->save();

    $this->checkPermissions([]);

    user_role_grant_permissions(AccountInterface::AUTHENTICATED_ROLE, ['access user profiles']);
    $this->admin_user = $this->drupalCreateUser([
      'administer profile types',
      "view any $id profile",
      "add any $id profile",
      "edit any $id profile",
      "delete any $id profile",
    ]);
  }
}
