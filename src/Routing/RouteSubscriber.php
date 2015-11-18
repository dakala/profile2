<?php

/**
 * @file
 * Contains \Drupal\profile\Routing\RouteSubscriber.
 */

namespace Drupal\profile\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Profile routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $account ;
   *   Current drupal account.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountInterface $account) {
    $this->entityTypeManager = $entity_type_manager;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $account = $this->account->getAccount();
    foreach ($this->entityTypeManager->getStorage('profile_type')->loadMultiple() as $profile_type_id => $profile_type) {
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
  }

}
