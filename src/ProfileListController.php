<?php

/**
 * @file
 * Contains \Drupal\node\NodeListBuilder.
 */

namespace Drupal\profile;

use Drupal\Component\Utility\String;
use Drupal\Core\Datetime\Date;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * List controller for profiles.
 *
 * @see \Drupal\profile\Entity\Profile
 */
class ProfileListController extends EntityListBuilder {

  /**
   * The date service.
   *
   * @var \Drupal\Core\Datetime\Date
   */
  protected $dateService;

  /**
   * Constructs a new ProfileListController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\Date $date_service
   *   The date service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, Date $date_service) {
    parent::__construct($entity_type, $storage);

    $this->dateService = $date_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array(
      'label' => $this->t('Label'),
      'type' => array(
        'data' => $this->t('Profile type'),
        'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
      ),
      'owner' => array(
        'data' => $this->t('Owner'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
      'changed' => array(
        'data' => $this->t('Updated'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
    );
    if (\Drupal::languageManager()->isMultilingual()) {
      $header['language_name'] = array(
        'data' => $this->t('Language'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      );
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\profile\ProfileInterface $entity */
    $mark = array(
      '#theme' => 'mark',
      '#mark_type' => node_mark($entity->id(), $entity->getChangedTime()),
    );
    $langcode = $entity->language()->id;
    $uri = $entity->urlInfo();
    $options = $uri->getOptions();
    $options += ($langcode != LanguageInterface::LANGCODE_NOT_SPECIFIED && isset($languages[$langcode]) ? array('language' => $languages[$langcode]) : array());
    $uri->setOptions($options);
    $row['label']['data'] = array(
      '#type' => 'link',
      '#title' => $entity->label(),
      '#suffix' => ' ' . drupal_render($mark),
    ) + $uri->toRenderArray();
    $row['type'] = $entity->getType()->id();
    $row['owner']['data'] = array(
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    );
    $row['changed'] = $this->dateService->format($entity->getChangedTime(), 'short');
    $language_manager = \Drupal::languageManager();
    if ($language_manager->isMultilingual()) {
      $row['language_name'] = $language_manager->getLanguageName($langcode);
    }
    $route_params = array('user' => $entity->getOwnerId(), 'type'=> $entity->bundle(), 'id' => $entity->id());
    $links['edit'] = array(
      'title' => t('Edit'),
      'route_name' => 'profile.account_edit_profile',
      'route_parameters' => $route_params,
    );
    $links['delete'] = array(
      'title' => t('Delete'),
      'route_name' => 'profile.account_delete_profile',
      'route_parameters' => $route_params,
    );

    $row[] = array(
      'data' => array(
          '#type' => 'operations',
          '#links' => $links,
      ),
    );

    //$row['operations']['data'] = $this->buildOperations($entity);

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
//    public function getOperations(EntityInterface $entity) {
//      $operations = parent::getOperations($entity);
//      // Place the edit operation after the operations added by field_ui.module
//      // which have the weights 15, 20, 25.
//      if (isset($operations['edit'])) {
//          $operations['edit'] = array(
//                  'title' => t('Edit'),
//                  'weight' => 30,
//              ) + $entity->urlInfo('edit-form')->toArray();
//      }
//      if (isset($operations['delete'])) {
//          $operations['delete'] = array(
//                  'title' => t('Delete'),
//                  'weight' => 35,
//              ) + $entity->urlInfo('delete-form')->toArray();
//      }
//      // Sort the operations to normalize link order.
//      uasort($operations, array(
//          'Drupal\Component\Utility\SortArray',
//          'sortByWeightElement'
//      ));
//
//      return $operations;
//  }

}
