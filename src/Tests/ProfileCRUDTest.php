<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileCRUDTest.
 */

namespace Drupal\profile\Tests;

use Drupal\simpletest\KernelTestBase;

/**
 * Tests basic CRUD functionality of profiles.
 */
class ProfileCRUDTest extends KernelTestBase {

  public static $modules = array('system', 'field', 'field_sql_storage', 'user', 'profile');

  public static function getInfo() {
    return array(
      'name' => 'Profile CRUD operations',
      'description' => 'Tests basic CRUD functionality of profiles.',
      'group' => 'profile',
    );
  }

  function setUp() {
    parent::setUp();
    $this->installSchema('system', 'url_alias');
    $this->installSchema('system', 'sequences');
    $this->enableModules(array('field', 'user', 'profile'));
  }

  /**
   * Tests CRUD operations.
   */
  function testCRUD() {
    $types_data = array(
      0 => array('label' => $this->randomName()),
      1 => array('label' => $this->randomName()),
    );
    foreach ($types_data as $id => $values) {
      $types[$id] = entity_create('profile_type', array('id' => $id) + $values);
      $types[$id]->save();
    }
    $this->user1 = entity_create('user', array(
      'name' => $this->randomName(),
      'mail' => $this->randomName() . '@example.com',
    ));
    $this->user1->save();
    $this->user2 = entity_create('user', array(
      'name' => $this->randomName(),
      'mail' => $this->randomName() . '@example.com',
    ));
    $this->user2->save();

    // Create a new profile.
    $profile = entity_create('profile', $expected = array(
      'type' => $types[0]->id(),
      'uid' => $this->user1->id(),
    ));
    $this->assertIdentical($profile->id(), NULL);
    $this->assertTrue($profile->uuid());
    $this->assertIdentical($profile->type, $expected['type']);
    $this->assertIdentical($profile->label(), $types[0]->label());
    $this->assertIdentical($profile->uid, $this->user1->id());
    $this->assertIdentical($profile->created, REQUEST_TIME);
    $this->assertIdentical($profile->changed, NULL);

    // Save the profile.
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $this->assertTrue($profile->id());
    $this->assertIdentical($profile->changed, REQUEST_TIME);

    // List profiles for the user and verify that the new profile appears.
    $list = entity_load_multiple_by_properties('profile', array(
      'uid' => $this->user1->uid,
    ));
    $this->assertEqual($list, array(
      $profile->id() => $profile,
    ));

    // Reload and update the profile.
    $profile = entity_load('profile', $profile->id());
    $profile->changed -= 1000;
    $original = clone $profile;
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_UPDATED);
    $this->assertIdentical($profile->id(), $original->id());
    $this->assertEqual($profile->created, REQUEST_TIME);
    $this->assertEqual($original->changed, REQUEST_TIME - 1000);
    $this->assertEqual($profile->changed, REQUEST_TIME);

    // Create a second profile.
    $user1_profile1 = $profile;
    $profile = entity_create('profile', array(
      'type' => $types[1]->id(),
      'uid' => $this->user1->id(),
    ));
    $status = $profile->save();
    $this->assertIdentical($status, SAVED_NEW);
    $user1_profile = $profile;

    // List profiles for the user and verify that both profiles appear.
    $list = entity_load_multiple_by_properties('profile', array(
      'uid' => $this->user1->uid,
    ));
    $this->assertEqual($list, array(
      $user1_profile1->id() => $user1_profile1,
      $user1_profile->id() => $user1_profile,
    ));

    // Delete the second profile and verify that the first still exists.
    $user1_profile->delete();
    $this->assertFalse(entity_load('profile', $user1_profile->id()));
    $list = entity_load_multiple_by_properties('profile', array(
      'uid' => $this->user1->uid,
    ));
    $this->assertEqual($list, array(
      $user1_profile1->id() => $user1_profile1,
    ));

    // Create a new second profile.
    $user1_profile = entity_create('profile', array(
      'type' => $types[1]->id(),
      'uid' => $this->user1->id(),
    ));
    $status = $user1_profile->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Create a profile for the second user.
    $user2_profile1 = entity_create('profile', array(
      'type' => $types[0]->id(),
      'uid' => $this->user2->id(),
    ));
    $status = $user2_profile1->save();
    $this->assertIdentical($status, SAVED_NEW);

    // Delete the first user and verify that all of its profiles are deleted.
    $this->user1->delete();
    $this->assertFalse(entity_load('user', $this->user1->id()));
    $list = entity_load_multiple_by_properties('profile', array(
      'uid' => $this->user1->uid,
    ));
    $this->assertEqual($list, array());

    // List profiles for the second user and verify that they still exist.
    $list = entity_load_multiple_by_properties('profile', array(
      'uid' => $this->user2->uid,
    ));
    $this->assertEqual($list, array(
      $user2_profile1->id() => $user2_profile1,
    ));

    // @todo Rename a profile type; verify that existing profiles are updated.
  }

}
