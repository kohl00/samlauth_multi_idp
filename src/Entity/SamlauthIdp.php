<?php

declare(strict_types=1);

namespace Drupal\samlauth_multi_idp\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\samlauth_multi_idp\SamlauthIdpInterface;

/**
 * Defines the identity provider entity type.
 *
 * @ConfigEntityType(
 *   id = "samlauth_idp",
 *   storage = "Drupal\samlauth_multi_idp\SamlauthIdpStorage",
 *   label = @Translation("Identity Provider"),
 *   label_collection = @Translation("Identity Providers"),
 *   label_singular = @Translation("identity provider"),
 *   label_plural = @Translation("identity providers"),
 *   label_count = @PluralTranslation(
 *     singular = "@count identity provider",
 *     plural = "@count identity providers",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\samlauth_multi_idp\SamlauthIdpListBuilder",
 *     "form" = {
 *       "add" = "Drupal\samlauth_multi_idp\Form\SamlauthIdpForm",
 *       "edit" = "Drupal\samlauth_multi_idp\Form\SamlauthIdpForm",
 *       "delete" = "Drupal\samlauth_multi_idp\Form\SamlauthIdpDeleteForm",
 *     },
 *   },
 *   config_prefix = "samlauth_idp",
 *   admin_permission = "administer samlauth_idp",
 *   links = {
 *     "collection" = "/admin/config/people/saml/idpidp",
 *     "add-form" = "/admin/config/people/saml/idp/add",
 *     "edit-form" = "/admin/config/people/saml/idp/{samlauth_idp}",
 *     "delete-form" = "/admin/config/people/saml/idp/{samlauth_idp}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "idp_entity_id",
 *     "idp_single_sign_on_service",
 *     "idp_single_log_out_service",
 *     "idp_change_password_service",
 *     "idp_certs",
 *     "idp_cert_encryption",
 *     "login_link_text",
 *     "login_link_enabled"
 *   },
 * )
 */
final class SamlauthIdp extends ConfigEntityBase implements SamlauthIdpInterface {

  /**
   * ID of the protected entity that cannot be deleted.
   */
  const PROTECTED_ENTITY_ID = 'default';

  /**
   * The identity provider ID.
   */
  protected string $id;

  /**
   * The label for the identity providers.
   */
  protected string $label;

  /**
   * The example description.
   */
  protected string $idp_entity_id;

  /**
   * The single sign on service URL.
   */
  protected string $idp_single_sign_on_service;

  /**
   * The single log out service URL.
   */
  protected string $idp_single_log_out_service;

  /**
   * The change password service URL.
   */
  protected string $idp_change_password_service;

  /**
   * The certificates for the identity provider.
   */
  protected array $idp_certs;

  /**
   * The encryption type to use.
   */
  protected string $idp_cert_encryption;

  /**
   * The status (boolean) of the login link.
   */
  protected ?bool $login_link_enabled;

  /**
   * The link text to use on the /user/login page.
   */
  protected ?string $login_link_text;

  /**
   * Prevent the default entity from being able to be deleted.
   *
   * @param $operation
   * @param AccountInterface|NULL $account
   * @param $return_as_object
   * @return mixed
   */
  public function access($operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($operation === 'delete' && $this->id() === self::PROTECTED_ENTITY_ID) {
      $result = AccessResult::forbidden()->addCacheableDependency($this);
      return $return_as_object ? $result : $result->isAllowed();
    }

    return parent::access($operation, $account, $return_as_object);
  }

  /**
   * @return mixed
   */
  public function getOperations() {
    $operations = parent::getOperations();

    if ($this->id() === self::PROTECTED_ENTITY_ID) {
      unset($operations['delete']);
    }

    return $operations;
  }
}
