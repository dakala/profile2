<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileAccessTest.
 */

namespace Drupal\profile\Tests;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Session\AccountInterface;

/**
 * Tests profile access handling.
 *
 * @group profile
 */
class ProfileAccessTest extends ProfileTestBase {

  /**
   * Tests administrative-only profiles.
   */
  function testAdminOnlyProfiles() {
    $id = $this->type->id();
    $field_name = $this->field->getName();

    // Create a test user account.
    $web_user = $this->drupalCreateUser(['access user profiles']);
    $uid = $web_user->id();
    $value = $this->randomMachineName();

    // Administratively enter profile field values for the new account.
    $this->drupalLogin($this->admin_user);

    // @todo #2617278, #2599010. Need our UI.
    /*
    $edit = [
      "{$field_name}[0][value]" => $value,
    ];
    $this->drupalPostForm("user/$uid/edit/profile/$id", $edit, t('Save'));

    $profile = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByUser($web_user, $this->type->id());

    $profile_id = $profile->id();

    // Verify that the administrator can see the profile.
    $this->drupalGet("user/$uid");
    $this->assertText($this->type->label());
    $this->assertText($value);
    $this->drupalLogout();

    // Verify that the user can not access, create or edit the profile.
    $this->drupalLogin($web_user);
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/profile/$id/$profile_id");
    $this->assertResponse(403);

    // Check edit link isn't displayed.
    $this->assertNoLinkByHref("user/$uid/edit/profile/$id/$profile_id");
    // Check delete link isn't displayed.
    $this->assertNoLinkByHref("user/$uid/delete/profile/$id/$profile_id");


    // Allow users to edit own profiles.
    user_role_grant_permissions(AccountInterface::AUTHENTICATED_ROLE, ["edit own $id profile"]);

    // Verify that the user is able to edit the own profile.
    $value = $this->randomMachineName();
    $edit = [
      "{$field_name}[0][value]" => $value,
    ];
    $this->drupalPostForm("user/$uid/edit/profile/$id/$profile_id", $edit, t('Save'));
    $this->assertText(new FormattableMarkup('profile has been updated.', []));


    // Verify that the own profile is still not visible on the account page.
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);

    // Allow users to view own profiles.
    user_role_grant_permissions(AccountInterface::AUTHENTICATED_ROLE, ["view own $id profile"]);

    // Verify that the own profile is visible on the account page.
    $this->drupalGet("user/$uid");
    $this->assertText($this->type->label());
    $this->assertText($value);

    // Allow users to delete own profiles.
    user_role_grant_permissions(AccountInterface::AUTHENTICATED_ROLE, ["delete own $id profile"]);

    // Verify that the user can delete the own profile.
    $this->drupalGet("user/$uid/edit/profile/$id/$profile_id");
    $this->clickLink(t('Delete'));
    $this->drupalPostForm(NULL, [], t('Delete'));
    $this->assertRaw(new FormattableMarkup('@label profile deleted.', ['@label' => $this->type->label()]));
    $this->assertUrl("user/$uid");

    // Verify that the profile is gone.
    $this->drupalGet("user/$uid");
    $this->assertNoText($this->type->label());
    $this->assertNoText($value);
    $this->drupalGet("user/$uid/edit/profile/$id");
    $this->assertNoText($value);
    */
  }

}
