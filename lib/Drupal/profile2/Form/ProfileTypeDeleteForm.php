<?php

/**
 * @file
 * Contains \Drupal\profile2\Form\CustomBlockTypeDeleteForm.
 */

namespace Drupal\profile2\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for deleting a Profile type entity.
 */
class ProfileTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * The query factory to create entity queries.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  public $queryFactory;

  /**
   * Constructs a query factory object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query object.
   */
  public function __construct(QueryFactory $query_factory) {
    $this->queryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %label?', array('%label' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'profile2.overview_types',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $blocks = $this->queryFactory->get('profile2')->condition('type', $this->entity->id())->execute();
    if (!empty($blocks)) {
      $caption = '<p>' . format_plural(count($blocks), '%label is used by 1 profile on your site. You can not remove this profile type until you have removed all of the %label profiles.', '%label is used by @count profiles on your site. You may not remove %label until you have removed all of the %label profiles.', array('%label' => $this->entity->label())) . '</p>';
      $form['description'] = array('#markup' => $caption);
      return $form;
    }
    else {
      return parent::buildForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    $form_state['redirect_route']['route_name'] = 'profile2.overview_types';
    drupal_set_message(t('Profile type %label has been deleted.', array('%label' => $this->entity->label())));
    watchdog('profile2', 'Profile type %label has been deleted.', array('%label' => $this->entity->label()), WATCHDOG_NOTICE);
  }

}
