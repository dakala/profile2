<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileTabTest.
 */

namespace Drupal\profile\Tests;

use Drupal\profile\Entity\Profile;
use Drupal\profile\Entity\ProfileType;
use Drupal\user\Entity\User;
use Drupal\system\Tests\Menu\LocalTasksTest;

/**
 * Tests tab functionality of profiles.
 *
 * @group profile
 */
class ProfileTabTest extends LocalTasksTest {

  public static $modules = ['profile', 'field_ui', 'text', 'block'];

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
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access user profiles',
      'administer profiles',
      'administer profile types',
      'bypass profile access',
      'access administration pages'
    ]);
  }

  /**
   * Tests tabs in profile UI.
   */
  public function testProfileTabs() {
    $types_data = [
      'profile_type_0' => ['label' => $this->randomMachineName()],
      'profile_type_1' => ['label' => $this->randomMachineName()],
    ];

    /** @var ProfileType[] $types */
    $types = [];
    foreach ($types_data as $id => $values) {
      $types[$id] = ProfileType::create(['id' => $id] + $values);
      $types[$id]->save();
    }
    $this->container->get('router.builder')->rebuild();

    $this->user1 = User::create([
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user1->save();
    $this->user2 = User::create([
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ]);
    $this->user2->save();

    // Create new profiles.
    $profile1 = Profile::create($expected = [
      'type' => $types['profile_type_0']->id(),
      'uid' => $this->user1->id(),
    ]);
    $profile1->save();
    $profile2 = Profile::create($expected = [
      'type' => $types['profile_type_1']->id(),
      'uid' => $this->user2->id(),
    ]);
    $profile2->save();

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config');
    $this->clickLink('User profiles');
    $this->assertResponse(200);
    $this->assertUrl('admin/config/people/profiles');

    $this->assertLink($profile1->label());
    $this->assertLinkByHref($profile2->toUrl('canonical')->toString());

    $tasks = [
      ['entity.profile.collection', []],
      ['entity.profile_type.collection', []],
    ];

    $this->assertLocalTasks($tasks, 0);
  }

}
