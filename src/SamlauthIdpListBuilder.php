<?php

declare(strict_types=1);

namespace Drupal\samlauth_multi_idp;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of identity providers.
 */
final class SamlauthIdpListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\samlauth_multi_idp\SamlauthIdpInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row = $row + parent::buildRow($entity);

    $row['operations']['data']['#links']['metadata'] = [
      'title' => $this->t('Metadata'),
      'url' => Url::fromRoute('samlauth_multi_idp.saml_metadata', ['samlauth_idp' => $entity->id()]),
    ];

    return $row;
  }

}
