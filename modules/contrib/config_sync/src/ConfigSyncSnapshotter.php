<?php

namespace Drupal\config_sync;

use Drupal\config_provider\InMemoryStorage;
use Drupal\config_provider\Plugin\ConfigCollectorInterface;
use Drupal\config_sync\ConfigSyncSnapshotterInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageComparer;
use Drupal\Core\Config\StorageInterface;

/**
 * The ConfigSyncSnapshotter provides helper functions for taking snapshots of
 * extension-provided configuration.
 */
class ConfigSyncSnapshotter implements ConfigSyncSnapshotterInterface {

  /**
   * The configuration collector.
   *
   * @var \Drupal\config_provider\Plugin\ConfigCollectorInterface
   */
  protected $configCollector;

  /**
   * The active configuration storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $activeStorage;

  /**
   * The snapshot config storage for values from the extension storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $snapshotExtensionStorage;

  /**
   * The snapshot config storage for values from the active storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $snapshotActiveStorage;

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Constructs a ConfigSyncSnapshotter object.
   *
   * @param \Drupal\config_provider\Plugin\ConfigCollectorInterface $config_collector
   *   The config collector.
   * @param \Drupal\Core\Config\StorageInterface $active_storage
   *   The active storage.
   * @param \Drupal\Core\Config\StorageInterface $snapshot_extension_storage
   *   The snapshot storage for the items from the extension storage.
   * @param \Drupal\Core\Config\StorageInterface $snapshot_active_storage
   *   The snapshot storage for the items from the active storage.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   */
  public function __construct(ConfigCollectorInterface $config_collector, StorageInterface $active_storage, StorageInterface $snapshot_extension_storage, StorageInterface $snapshot_active_storage, ConfigManagerInterface $config_manager) {
    $this->configCollector = $config_collector;
    $this->activeStorage = $active_storage;
    $this->snapshotExtensionStorage = $snapshot_extension_storage;
    $this->snapshotActiveStorage = $snapshot_active_storage;
    $this->configManager = $config_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function refreshSnapshot() {
    /* @var \Drupal\config_provider\InMemoryStorage $installable_config */
    $installable_config = $this->configCollector->getInstallableConfig();
    // Set up a storage comparer.
    $storage_comparer = new StorageComparer(
      $installable_config,
      $this->snapshotExtensionStorage,
      $this->configManager
    );
    $storage_comparer->createChangelist();
    // Only add new items.
    $changelist = $storage_comparer->getChangelist('create');
    foreach ($changelist as $item_name) {
      $this->createItemSnapshot($installable_config, $item_name);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteSnapshot() {
    // Do not delete values from ::snapshotActiveStorage because they represent
    // the item as originally installed.
    $this->snapshotExtensionStorage->deleteAll();
  }

  /**
   * Creates a snapshot of a given configuration item as provided by an
   * extension.
   *
   * @param InMemoryStorage $config_storage
   *   An extension's configuration file storage.
   * @param string $item_name
   *   The name of the configuration item.
   */
  protected function createItemSnapshot(InMemoryStorage $config_storage, $item_name) {
    // Snapshot the configuration item as provided by the extension.
    if ($provided_data = $config_storage->read($item_name)) {
      $this->snapshotExtensionStorage->write($item_name, $provided_data);
    }

    // Snapshot the configuration item as installed in the active storage if
    // a snapshot doesn't already exist. The snapshot represents the original
    // installed state.
    if (($active_data = $this->activeStorage->read($item_name)) && !$this->snapshotActiveStorage->read($item_name)) {
      $this->snapshotActiveStorage->write($item_name, $active_data);
    }
  }

}
