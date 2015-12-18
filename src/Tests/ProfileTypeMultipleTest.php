<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileTypeMultipleTest.
 */

namespace Drupal\profile\Tests;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\Cache;

/**
 * Tests multiple enabled profile types.
 *
 * @group profile
 */
class ProfileTypeMultipleTest extends ProfileTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->type->setMultiple(TRUE);
    $this->type->save();
    $this->container->get('router.builder')->rebuild();
  }

  /**
   * Tests the flow of a profile type that has multiple enabled.
   */
  public function testMultipleProfileType() {
    $web_user1 = $this->drupalCreateUser(
      ["add own {$this->type->id()} profile", "edit own {$this->type->id()} profile"]
    );
    $this->drupalLogin($web_user1);
    $value = $this->randomMachineName();

    $edit = [
      "{$this->field->getName()}[0][value]" => $value,
    ];
    $this->drupalPostForm("user/{$web_user1->id()}/{$this->type->id()}", $edit, t('Save'));
    $this->assertRaw(new FormattableMarkup('%type profile has been created.', [
      '%type' => $this->type->label(),
    ]));

    $this->drupalGet("user/{$web_user1->id()}/{$this->type->id()}");
    $this->assertLinkByHref("user/{$web_user1->id()}/{$this->type->id()}/add");
    $this->assertText($value);

    $value2 = $this->randomMachineName();
    $edit = [
      "{$this->field->getName()}[0][value]" => $value2,
    ];
    $this->drupalPostForm("user/{$web_user1->id()}/{$this->type->id()}/add", $edit, t('Save'));
    $this->assertRaw(new FormattableMarkup('%type profile has been created.', [
      '%type' => $this->type->label(),
    ]));

    Cache::invalidateTags(['profile_view']);

    $this->drupalGet("user/{$web_user1->id()}/{$this->type->id()}");
    $this->assertText($value2);
  }
}
