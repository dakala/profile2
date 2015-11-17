<?php

/**
 * @file
 * Contains \Drupal\profile\ProfileAccessControlHandler.
 */

namespace Drupal\profile;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the access control handler for the profile entity type.
 *
 * @see \Drupal\profile\Entity\Profile
 */
class ProfileAccessControlHandler extends EntityAccessControlHandler implements EntityHandlerInterface {

  /**
   * Constructs a NodeAccessControlHandler object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    parent::__construct($entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type
    );
  }

  /**
   * {@inheritdoc}
   */
  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = [], $return_as_object = FALSE) {
    $account = $this->prepareUser($account);

    if ($account->hasPermission('bypass profile access')) {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    $result = parent::createAccess($entity_bundle, $account, $context, TRUE)->cachePerPermissions();
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $account = $this->prepareUser($account);

    $user_page = \Drupal::request()->attributes->get('user');

    // Some times, operation edit is called update.
    // Use edit in any case.
    if ($operation == 'update') {
      $operation = 'edit';
    }

    if ($account->hasPermission('bypass profile access')) {
      return AccessResult::allowed()->cachePerPermissions();
    }
    elseif (
      (
        $operation == 'add'
        && (
          (
            $user_page->id() == $account->id()
            && $account->hasPermission($operation . ' own ' . $entity->id() . ' profile')
          )
          || $account->hasPermission($operation . ' any ' . $entity->id() . ' profile')
        )
      ) || (
        $operation != 'add'
        && (
          (
            $entity->getOwnerId() == $account->id()
            && $account->hasPermission($operation . ' own ' . $entity->getType() . ' profile')
          )
          || $account->hasPermission($operation . ' any ' . $entity->getType() . ' profile')
        )
      )
    ){
      return AccessResult::allowed()->cachePerPermissions();
    }
    else {
      return AccessResult::forbidden()->cachePerPermissions();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, [
      'add any ' . $entity_bundle . ' profile',
      'add own ' . $entity_bundle . ' profile',
    ], 'OR');
  }

}
