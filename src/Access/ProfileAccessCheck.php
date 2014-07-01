<?php

/**
 * @file
 * Contains \Drupal\profile\Access\ProfileAccessCheck.
 */
namespace Drupal\profile\Access;

use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\profile\ProfileTypeInterface;

/**
 * Checks access to add, edit and delete profiles.
 */
class ProfileAccessCheck implements AccessCheckInterface {
  /**
   * A user account to check access for.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Constructs a CustomAccessCheck object.
   *
   * @param \Drupal\Core\Session\AccountInterface
   *   The user account to check access for.
   */
  public function __construct(AccountInterface $account) {
    $this->account = $account;
  }

  /**
   * Implements AccessCheckInterface::applies().
   */
  public function applies(Route $route) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function access(Route $route, Request $request) {
    $frags = explode('/', $route->getPath());
    if ((!count($frags) > 3) && (!in_array($frags[3], array(
        'add',
        'edit',
        'delete'
      )))
    ) {
      return static::DENY;
    }

    $profile_type = $request->attributes->get('type');
    if ($profile_type instanceof ProfileTypeInterface) {
      $anyPermission = sprintf("%s any %s profile", $frags[3], $profile_type->id());
      $ownPermission = sprintf("%s own %s profile", $frags[3], $profile_type->id());
      return ($this->account->hasPermission($anyPermission) || $this->account->hasPermission($ownPermission)) ?
        static::ALLOW : static::DENY;
    }

    return static::ALLOW;
  }
}