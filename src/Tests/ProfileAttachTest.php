<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileAttachTest.
 */

namespace Drupal\profile\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Tests attaching of profile entity forms to other forms.
 *
 * @group profile
 */
class ProfileAttachTest extends WebTestBase {

  public static $modules = array('profile', 'text');

  /**
   * The entity type to test against
   */
  protected $profile_type;

  /**
   * The entity type to test against
   */
  protected $profile_field;

  protected $instance;

  function setUp() {
    parent::setUp();

    $this->profile_type = entity_create('profile_type', array(
      'id' => 'test',
      'label' => 'Test profile',
      'weight' => 0,
      'registration' => TRUE,
    ));
    $this->profile_type->save();

    $this->profile_field = array(
      'field_name' => 'profile_fullname',
      'type' => 'text',
      'entity_type' => 'profile',
      'cardinality' => 1,
      'translatable' => FALSE,
    );
    $this->profile_field = FieldStorageConfig::create($this->profile_field);
    $this->profile_field->save();

    $this->instance = array(
      'entity_type' => $this->profile_field->get('entity_type'),
      'field_name' => $this->profile_field->get('field_name'),
      'bundle' => $this->profile_type->id(),
      'label' => 'Full name',
      'required' => TRUE,
      'widget' => array(
        'type' => 'text_textfield',
      ),
    );
    $this->instance = FieldConfig::create($this->instance);
    $this->instance->save();

    $display = entity_get_display('profile', 'test', 'default')
      ->setComponent($this->profile_field->get('field_name'), array(
        'type' => 'text_default',
      ));
    $display->save();

    $form = entity_get_form_display('profile', 'test', 'default')
      ->setComponent($this->profile_field->get('field_name'), array(
        'type' => 'string_textfield',
      ));
    $form->save();
    $this->checkPermissions(array(), TRUE);
  }

  /**
   * Test user registration integration.
   */
  function testUserRegisterForm() {
    $id = $this->profile_type->id();
    $field_name = $this->profile_field->get('field_name');

    $config = $this->config('user.settings');
    // Don't require email verification and allow registration by site visitors
    // without administrator approval.
    $config
      ->set('verify_mail', FALSE)
      ->set('register', USER_REGISTER_VISITORS)
      ->save();

    user_role_grant_permissions(DRUPAL_AUTHENTICATED_RID, array('view own test profile'));

    // Verify that the additional profile field is attached and required.
    $name = $this->randomMachineName();
    $pass_raw = $this->randomMachineName();
    $edit = array(
      'name' => $name,
      'mail' => $this->randomMachineName() . '@example.com',
      'pass[pass1]' => $pass_raw,
      'pass[pass2]' => $pass_raw,
    );
    $this->drupalPostForm('user/register', $edit, t('Create new account'));
    $this->assertRaw(format_string('@name field is required.', array('@name' => $this->instance->label)));

    // Verify that we can register.
    $edit["entity_" . $id . "[$field_name][0][value]"] = $this->randomMachineName();
    $this->drupalPostForm(NULL, $edit, t('Create new account'));
    $this->assertText(format_string('Registration successful. You are now logged in.'));

    $new_user = user_load_by_name($name);
    $this->assertTrue($new_user->isActive(), 'New account is active after registration.');

    // Verify that a new profile was created for the new user ID.
    $profiles = entity_load_multiple_by_properties('profile', array(
      'uid' => $new_user->id(),
      'type' => $this->profile_type->id(),
    ));
    $profile = reset($profiles);
    $this->assertEqual($profile->get($field_name)->value, $edit["entity_" . $id . "[$field_name][0][value]"], 'Field value found in loaded profile.');

    // Verify that the profile field value appears on the user account page.
    $this->drupalGet('user');
    $this->assertText($edit["entity_" . $id . "[$field_name][0][value]"], 'Field value found on user account page.');
  }

}
