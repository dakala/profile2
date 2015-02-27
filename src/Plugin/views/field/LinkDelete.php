<?php

/**
 * @file
 * Definition of Drupal\profile\Plugin\views\field\LinkDelete.
 */

namespace Drupal\profile\Plugin\views\field;


use Drupal\views\ResultRow;

/**
 * Field handler to present a link to delete a profile.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("profile_link_delete")
 */
class LinkDelete extends Link {

  /**
   * Prepares the link to delete a profile.
   *
   * @param \Drupal\Core\Entity\EntityInterface $profile
   *   The profile entity this field belongs to.
   * @param \Drupal\views\ResultRow $values
   *   The values retrieved from the view's result set.
   *
   * @return string
   *   Returns a string for the link text.
   */
  protected function renderLink($profile, ResultRow $values) {
    // Ensure user has access to delete this node.
    if (!$profile->access('delete')) {
      return;
    }

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = "user/" . $profile->getOwnerId() . "/delete/" . $profile->bundle() . "/" . $profile->id();
    $this->options['alter']['query'] = drupal_get_destination();
    $text = !empty($this->options['text']) ? $this->options['text'] : t('Delete');
    return $text;
  }

}
