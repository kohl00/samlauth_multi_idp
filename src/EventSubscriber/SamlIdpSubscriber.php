<?php

declare(strict_types=1);

namespace Drupal\samlauth_multi_idp\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Add description for this subscriber.
 */
final class SamlIdpSubscriber implements EventSubscriberInterface {

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
