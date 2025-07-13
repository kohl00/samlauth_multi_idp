<?php

namespace Drupal\samlauth_multi_idp\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\samlauth_multi_idp\Entity\SamlauthIdp;

/**
 * Provides a delete form for SamlauthIdp entities that prevents deleting the default entity.
 */
class SamlauthIdpDeleteForm extends EntityDeleteForm {

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->entity;

    if ($entity->id() === SamlauthIdp::PROTECTED_ENTITY_ID) {
      $this->messenger()->addError($this->t('The default identity provider cannot be deleted.'));
      // Redirect back without deleting.
      $form_state->setRedirect('entity.samlauth_idp.collection');
      return;
    }

    parent::submitForm($form, $form_state);
  }
}
