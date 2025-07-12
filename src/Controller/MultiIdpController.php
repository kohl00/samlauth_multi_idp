<?php

namespace Drupal\samlauth_multi_idp\Controller;

/**
 * Returns responses for samlauth_multi_idp module routes.
 */
class SamlController extends ControllerBase {

  public function login() {
    $idps = \Drupal::entityTypeManager()->getStorage('samlauth_idp')->loadByProperties([
      'login_link_enabled' => TRUE,
    ]);

    $content['saml_login_links'] = [
      '#type' => 'container',
    ];

    foreach ($idps as $idp) {
      $content['saml_login_links'][] = [
        '#prefix' => '<p>',
        '#suffix' => '</p>',
        '#type' => 'url',
        '#title' => $idp->get('login_link_text'),
        '#url' => Url::fromRoute([
          'query' => [
            'saml_idp' => $idp->id(),
          ],
        ]),
      ];
    }

    $build = [
      '#theme' => 'samlauth_idp_login',
      '#content' => $content,
    ];

    return $build;
  }

}
