<?php

declare(strict_types=1);

namespace Drupal\samlauth_multi_idp\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * This event subscriber adds in support for multiple IdPs to be used with samlauth.
 *
 * It injects the appropriate idp into the login or acs response as needed.
 *
 */
final class SamlIdpSubscriber implements EventSubscriberInterface {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Kernel request event handler.
   */
  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();

    if (!$event->isMainRequest()) {
      return;
    }

    if ($request->getPathInfo() === '/saml/login') {
      $idp_id = $request->query->get('saml_idp');

      if ($idp_id) {
        $request->getSession()->set('samlauth_selected_idp', $idp_id);
      }
    }

    if ($request->getPathInfo() === '/saml/reauth') {
      $idp_id = $request->query->get('saml_idp');

      if ($idp_id) {
        $request->getSession()->set('samlauth_selected_idp', $idp_id);
      }
    }

    if ($request->getPathInfo() === '/saml/acs' && $request->isMethod('POST') && $request->request->has('SAMLResponse') && !$request->getSession()->get('samlauth_selected_idp')) {
      $xml = base64_decode($request->request->get('SAMLResponse'));
      $dom = new \DOMDocument();

      if (@$dom->loadXML($xml)) {
        $issuer_nodes = $dom->getElementsByTagName('Issuer');
        if ($issuer_nodes->length > 0) {
          $issuer = trim($issuer_nodes->item(0)->textContent);

          if ($issuer) {
            $idps = $this->entityTypeManager->getStorage('samlauth_idp')->loadByProperties([
              'idp_entity_id' => $issuer,
            ]);

            if (!empty($idps)) {
              $idp = reset($idps);
              $request->getSession()->set('samlauth_selected_idp', $idp->id());
            }
            else {
              $request->getSession()->set('samlauth_selected_idp', 'default');
            }
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest', 30],
    ];
  }

}
