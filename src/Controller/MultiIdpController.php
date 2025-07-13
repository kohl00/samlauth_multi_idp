<?php

namespace Drupal\samlauth_multi_idp\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

/**
 * Returns responses for samlauth_multi_idp module routes.
 */
class MultiIdpController extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function login() {
    $idps = $this->entityTypeManager->getStorage('samlauth_idp')->loadByProperties([
      'login_link_enabled' => TRUE,
    ]);

    $content['saml_login_links'] = [
      '#type' => 'container',
    ];

    $cache_metadata = new CacheableMetadata();

    foreach ($idps as $idp) {
      $content['saml_login_links'][] = [
        '#prefix' => '<p>',
        '#suffix' => '</p>',
        '#type' => 'link',
        '#title' => $idp->get('login_link_text'),
        '#url' => Url::fromRoute('samlauth.saml_controller_login', [],
          [
          'query' => [
            'saml_idp' => $idp->id(),
          ],
        ]),
      ];

      $cache_metadata->addCacheableDependency($idp);
    }

    $cache_metadata->applyTo($content['saml_login_links']);

    $build = [
      '#theme' => 'samlauth_idp_login',
      '#content' => $content['saml_login_links'],
    ];

    return $build;
  }

}
