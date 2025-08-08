<?php

namespace Drupal\samlauth_multi_idp\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\samlauth_multi_idp\MultiIdpSamlService;
use Drupal\samlauth_multi_idp\SamlauthIdpInterface;
use Symfony\Component\HttpFoundation\Response;

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

  /**
   * The SAML service.
   *
   * @var \Drupal\samlauth_multi_idp\MultiIdpSamlService
   */
  protected $samlService;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, MultiIdpSamlService $saml_service) {
    $this->entityTypeManager = $entity_type_manager;
    $this->samlService = $saml_service;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('samlauth.saml')
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

  /**
   * Returns SAML metadata for a specific IdP configuration.
   */
  public function metadata(SamlauthIdpInterface $samlauth_idp) {
    $request = \Drupal::request();
    $allow_invalid = $request->query->get('check', '1') === '0';
    $validity = $request->query->get('validity');
    $cache_duration = $request->query->get('cache');

    $metadata = $this->samlService->getMetadata($validity, $cache_duration, $allow_invalid, $samlauth_idp);
    return new Response($metadata, 200, ['Content-Type' => 'text/xml']);
  }

}
