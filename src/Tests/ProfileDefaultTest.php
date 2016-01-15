<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 1/15/16
 * Time: 9:41 AM
 */

namespace Drupal\profile\Tests;


/**
 * Tests basic CRUD functionality of profiles.
 *
 * @group profile
 */
use Drupal\profile\Entity\Profile;
use Drupal\user\Entity\User;

class ProfileDefaultTest extends ProfileTestBase {
  /**
   * Testing demo user 1.
   *
   * @var \Drupal\user\UserInterface
   */
  public $user1;

  /**
   * Testing demo user 2.
   *
   * @var \Drupal\user\UserInterface;
   */
  public $user2;

  /**
   * Profile entity storage.
   *
   * @var \Drupal\profile\ProfileStorageInterface
   */
  public $profileStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access user profiles',
      'administer profiles',
      'administer profile types',
      'bypass profile access',
      'access administration pages',
    ]);

    $this->user1 = User::create([
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user1->save();
    $this->user1->save();
    $this->user2 = User::create([
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user2->save();
  }

  /**
   * Tests default profile functionality.
   */
  public function testDefaultProfile() {
    $profile_type = $this->createProfileType('test_defaults', 'test_defaults');

    // Create new profiles.
    $profile1 = Profile::create($expected = [
      'type' => $profile_type->id(),
      'uid' => $this->user1->id(),
    ]);
    $profile1->setActive(TRUE);
    $profile1->save();
    $profile2 = Profile::create($expected = [
      'type' => $profile_type->id(),
      'uid' => $this->user1->id(),
    ]);
    $profile2->setActive(TRUE);
    $profile2->setDefault(TRUE);
    $profile2->save();

    $this->assertFalse($profile1->isDefault());
    $this->assertTrue($profile2->isDefault());

    $profile1->setDefault(TRUE)->save();

    $this->assertFalse(Profile::load($profile2->id())->isDefault());
    $this->assertTrue(Profile::load($profile1->id())->isDefault());
  }

  /**
   * Tests loading default from storage handler.
   */
  public function testLoadDefaultProfile() {
    $profile_type = $this->createProfileType('test_defaults', 'test_defaults');

    // Create new profiles.
    $profile1 = Profile::create($expected = [
      'type' => $profile_type->id(),
      'uid' => $this->user1->id(),
    ]);
    $profile1->setActive(TRUE);
    $profile1->save();
    $profile2 = Profile::create($expected = [
      'type' => $profile_type->id(),
      'uid' => $this->user1->id(),
    ]);
    $profile2->setActive(TRUE);
    $profile2->setDefault(TRUE);
    $profile2->save();

    /** @var \Drupal\profile\ProfileStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('profile');

    $default_profile = $storage->loadDefaultByUser($this->user1, $profile_type->id());
    $this->assertEqual($profile2->id(), $default_profile->id());
  }
}
