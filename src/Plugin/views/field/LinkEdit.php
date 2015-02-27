<?php

/**
 * @file
 * Definition of Drupal\profile\Plugin\views\field\LinkEdit.
 */

namespace Drupal\profile\Plugin\views\field;


use Drupal\views\ResultRow;

/**
 * Field handler to present a link profile edit.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("profile_link_edit")
 */
class LinkEdit extends Link {

  /**
   * Prepares the link to the profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   The profile entity this field belongs to.
   * @param ResultRow $values
   *   The values retrieved from the view's result set.
   *
   * @return string
   *   Returns a string for the link text.
   */
  protected function renderLink($profile, ResultRow $values) {
    // Ensure user has access to edit this profile.
    if (!$profile->access('update')) {
      return;
    }

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = "user/" . $profile->getOwnerId() . "/edit/" . $profile->bundle() . "/" . $profile->id();
    $this->options['alter']['query'] = drupal_get_destination();
    $text = !empty($this->options['text']) ? $this->options['text'] : t('Edit');
    return $text;
  }

}
