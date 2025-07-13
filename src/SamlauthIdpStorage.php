<?php

namespace Drupal\samlauth_multi_idp;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\samlauth_multi_idp\Entity\SamlauthIdp;

/**
 * Storage handler for the SamlauthIdp entities.
 */
class SamlauthIdpStorage extends ConfigEntityStorage {

  /**
   * Prevents deletion of the default identity provider entity.
   *
   * Removes the protected entity from the deletion list,
   * adds an error message, and logs a message.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface[] $entities
   *   An array of entities to delete.
   */
  public function delete(array $entities) {

    foreach ($entities as $key => $entity) {
      if ($entity->id() === SamlAuthIdp::PROTECTED_ENTITY_ID) {
        \Drupal::messenger()->addError(t('The default identity provider cannot be deleted and was skipped.'));
        \Drupal::logger('samlauth_multi_idp')->warning('Attempted to delete the default identity provider.');
        unset($entities[$key]);
      }
    }
    parent::delete($entities);
  }
}
