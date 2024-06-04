<?php

namespace Drupal\sl_main\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\ClientInterface;

/**
 * Validates whether a certain state transition is allowed.
 */
class CountryManager {

  use StringTranslationTrait;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Creates an EntityViewController object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager, MessengerInterface $messenger) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function initCountries(): void {

    $response = $this->httpClient->request('GET', 'http://country.io/names.json');
    if ($response->getStatusCode() !== 200) {
      $this->messenger->addMessage($this->t('Failed to load the countries.'));
      return;
    }

    $countries = json_decode($response->getBody(), TRUE);

    if (empty($countries)) {
      $this->messenger->addMessage($this->t('Failed to load the countries.'));
      return;
    }

    $i = 0;
    foreach ($countries as $country) {
      $i++;
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->create([
        'vid' => 'countries',
        'name' => $country,
      ]);
      $term->save();
    }

    $this->messenger->addMessage(t('@count countries are created.', ['@count' => $i]));

  }

  /**
   * {@inheritdoc}
   */
  public function removeCountries() {
    $countries = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['vid' => 'countries']);
    if (!empty($countries)) {
      $this->entityTypeManager->getStorage('taxonomy_term')->delete($countries);
    }
  }

}
