<?php

/**
 * @file
 * Contains \Drupal\profile2\Form\ProfileDeleteForm.
 */

namespace Drupal\profile2\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for deleting a profile entity.
 */
class ProfileDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a ProfileDeleteForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct(EntityManagerInterface $entity_manager, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_manager);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete %title?', array('%title' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return $this->entity->urlInfo();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    watchdog('profile2', '@type: deleted %title.', array(
        '@type' => $this->entity->bundle(),
        '%title' => $this->entity->label()
      ));
    $profile_type_storage = $this->entityManager->getStorage('profile2_type');
    $profile_type = $profile_type_storage->load($this->entity->bundle())
      ->label();
    drupal_set_message(t('@type %title has been deleted.', array(
          '@type' => $profile_type,
          '%title' => $this->entity->label()
        )));
    Cache::invalidateTags(array('profile2' => TRUE));
    $form_state['redirect_route']['route_name'] = 'user.edit';
  }

}
