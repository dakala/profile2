<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileHtmlRouteProvider.
 */

namespace Drupal\profile;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides HTML routes for the profile entity type.
 */
class ProfileHtmlRouteProvider extends DefaultHtmlRouteProvider {

  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    foreach ($this->entityManager->getStorage('profile_type')->loadMultiple() as $profile_type_id => $profile_type) {
      $route = new Route(
        "/user/{user}/edit/user_profile_form/{profile_type}",
        ['_controller' => '\Drupal\profile\Controller\ProfileController::userProfileForm'],
        ['_profile_access_check' =>  'add'],
        ['parameters' => [
          'user' => ['type' => 'entity:user'],
          'profile_type' => ['type' => 'entity:profile_type'],
          $profile_type_id => ['type' => 'profile:' . $profile_type_id],
        ]]
      );
      $collection->add("entity.profile.type.$profile_type_id.user_profile_form", $route);
    }

    return $collection;
  }

}
