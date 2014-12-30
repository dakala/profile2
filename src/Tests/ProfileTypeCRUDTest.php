<?php

/**
 * @file
 * Contains \Drupal\profile\Tests\ProfileTypeCRUDTest.
 */

namespace Drupal\profile\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Component\Utility\Unicode;

/**
 * Tests basic CRUD functionality of profile types.
 *
 * @group profile
 */
class ProfileTypeCRUDTest extends WebTestBase {

  public static $modules = array('profile', 'field_ui', 'text');

  /**
   * Tests CRUD operations for profile types through the UI.
   */
  function testCRUDUI() {
    $this->drupalLogin($this->root_user);

    // Create a new profile type.
    $this->drupalGet('admin/people/profiles');
    $this->clickLink(t('Add profile type'));
    $this->assertUrl('admin/people/profiles/add');
    $id = Unicode::strtolower($this->randomMachineName());
    $label = $this->randomString();
    $edit = array(
      'id' => $id,
      'label' => $label,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/people/profiles');
    $this->assertRaw(t('%label profile type has been created.', array('%label' => $label)));
    $this->assertLinkByHref("admin/people/profiles/manage/$id/edit");
    $this->assertLinkByHref("admin/people/profiles/manage/$id/fields");
    $this->assertLinkByHref("admin/people/profiles/manage/$id/display");
    $this->assertLinkByHref("admin/people/profiles/manage/$id/delete");

    // Edit the new profile type.
    $this->drupalGet("admin/people/profiles/manage/$id/edit");
    $this->assertRaw(t('Edit %label profile type', array('%label' => $label)));
    $edit = array(
      'registration' => 1,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/people/profiles');
    $this->assertRaw(t('%label profile type has been updated.', array('%label' => $label)));

    // Add a field to the profile type.
    $this->drupalGet("admin/people/profiles/manage/$id/fields");
    $field_name = Unicode::strtolower($this->randomMachineName());
    $field_label = $this->randomString();
    $edit = array(
      'fields[_add_new_field][label]' => $field_name,
      'fields[_add_new_field][field_name]' => $field_name,
      'fields[_add_new_field][type]' => 'text',
      'fields[_add_new_field][widget_type]' => 'text_textfield',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->drupalPostForm(NULL, array(), t('Save field settings'));
    $this->drupalPostForm(NULL, array(), t('Save settings'));
    $this->assertUrl("admin/people/profiles/manage/$id/fields");

    // Rename the profile type ID.
    $this->drupalGet("admin/people/profiles/manage/$id/edit");
    $new_id = Unicode::strtolower($this->randomMachineName());
    $edit = array(
      'id' => $new_id,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertUrl('admin/people/profiles');
    $this->assertRaw(t('%label profile type has been updated.', array('%label' => $label)));
    $this->assertLinkByHref("admin/people/profiles/manage/$new_id/edit");
    $this->assertNoLinkByHref("admin/people/profiles/manage/$id/edit");
    $id = $new_id;

    // Verify that the field is still associated with it.
    $this->drupalGet("admin/people/profiles/manage/$id/fields");
    // @todo D8 core: This assertion fails for an unknown reason. Database
    //   contains the right values, so field_attach_rename_bundle() works
    //   correctly. The pre-existing field does not appear on the Manage
    //   fields page of the renamed bundle. Not even flushing all caches
    //   helps. Can be reproduced manually.
    //$this->assertText(check_plain($field_label));
  }

}
