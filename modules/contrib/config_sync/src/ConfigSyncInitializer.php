<?php

namespace Drupal\config_sync;

use Drupal\config_provider\Plugin\ConfigCollectorInterface;
use Drupal\config_sync\ConfigSyncInitializerInterface;
use Drupal\config_sync\ConfigSyncMerger;
use Drupal\config_sync\ConfigSyncSnapshotterInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageComparer;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\Extension;

/**
 * Returns responses for config module routes.
 */
class ConfigSyncInitializer implements ConfigSyncInitializerInterface {

  /**
   * The configuration collector.
   *
   * @var \Drupal\config_provider\Plugin\ConfigCollectorInterface
   */
  protected $configCollector;

  /**
   * The config sync snapshotter.
   *
   * @var \Drupal\config_sync\ConfigSyncSnapshotterInterface
   */
  protected $configSyncSnapshotter;

  /**
   * The active configuration storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $activeStorage;

  /**
   * The extension snapshot storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $snapshotExtensionStorage;

  /**
   * The merged storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $mergedStorage;

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Constructs a ConfigSyncInitializer object.
   *
   * @param \Drupal\config_provider\Plugin\ConfigCollectorInterface $config_collector
   *   The config collector.
   * @param \Drupal\config_sync\ConfigSyncSnapshotterInterface $config_sync_snapshotter
   *   The config snapshotter.
   * @param \Drupal\Core\Config\StorageInterface $target_storage
   *   The active storage.
   * @param \Drupal\Core\Config\StorageInterface $source_storage
   *   The extension snapshot storage.
   * @param \Drupal\Core\Config\StorageInterface $source_storage
   *   The merged storage.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   */
  public function __construct(ConfigCollectorInterface $config_collector, ConfigSyncSnapshotterInterface $config_sync_snapshotter, StorageInterface $active_storage, StorageInterface $snapshot_extension_storage, StorageInterface $merged_storage, ConfigManagerInterface $config_manager) {
    $this->configCollector = $config_collector;
    $this->configSyncSnapshotter = $config_sync_snapshotter;
    $this->activeStorage = $active_storage;
    $this->snapshotExtensionStorage = $snapshot_extension_storage;
    $this->mergedStorage = $merged_storage;
    $this->configManager = $config_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function initialize($retain_active_overrides = TRUE, array $extension_names = []) {
    // Convert incoming extension names into Extension objects.
    $extensions = [];
    foreach ($extension_names as $type => $names) {
      foreach ($names as $name) {
        $pathname = $this->drupalGetFilename($type, $name);
        $extension = new Extension(\Drupal::root(), $type, $pathname);
        $extensions[$name] = $extension;
      }
    }

    $this->seedMergeStorage();
    $active_config_items = $this->configManager->getConfigFactory()->listAll();
    /* @var \Drupal\config_provider\InMemoryStorage $installable_config */
    $installable_config = $this->configCollector->getInstallableConfig($extensions);
    // Set up a storage comparer.
    $storage_comparer = new StorageComparer(
      $installable_config,
      $this->snapshotExtensionStorage,
      $this->configManager
    );
    $storage_comparer->createChangelist();
    $changelist = $storage_comparer->getChangelist();

    // Process changes.
    if (!empty($changelist['create'])) {
      // Don't attempt to create items that already exist.
      $config_to_create = array_diff($changelist['create'], $active_config_items);
      // To create, we simply save the new item to the merge storage.
      foreach ($config_to_create as $item_name) {
        $this->mergedStorage->write($item_name, $installable_config->read($item_name));
      }
    }
    // Process update changes.
    if (!empty($changelist['update'])) {
      // Don't attempt to update items that don't exist.
      $config_to_update = array_intersect($changelist['update'], $active_config_items);
      $config_sync_merger = new ConfigSyncMerger();
      // To update, we merge the value into that of the active storage.
      foreach ($config_to_update as $item_name) {
        $current = $installable_config->read($item_name);
        if ($retain_active_overrides) {
          $previous = $this->snapshotExtensionStorage->read($item_name);
          $active = $this->configManager->getConfigFactory()->get($item_name)->getRawData();
          $merged_value = $config_sync_merger->mergeConfigItemStates($previous, $current, $active);
        }
        else {
          $merged_value = $current;
        }
        $this->mergedStorage->write($item_name, $merged_value);
      }
    }
  }

  /**
   * Seeds the merge storage with the current active configuration.
   *
   * @see ConfigController::downloadExport()
   */
  protected function seedMergeStorage() {
    // Clear out any existing data.
    $this->mergedStorage->deleteAll();

    // First, export all configuration from the active storage.
    // Get raw configuration data without overrides.
    foreach ($this->configManager->getConfigFactory()->listAll() as $name) {
      $this->mergedStorage->write($name, $this->configManager->getConfigFactory()->get($name)->getRawData());
    }
    // Get all override data from the remaining collections.
    foreach ($this->activeStorage->getAllCollectionNames() as $collection) {
      $collection_storage = $this->activeStorage->createCollection($collection);
      $merged_collection_storage = $this->mergedStorage->createCollection($collection);
      $merged_collection_storage->deleteAll();
      foreach ($collection_storage->listAll() as $name) {
        $merged_collection_storage->write($name, $collection_storage->read($name));
      }
    }
  }

  /**
   * Wraps the function drupal_get_filename().
   *
   * @param $type
   *   The type of the item; one of 'core', 'profile', 'module', 'theme', or
   *   'theme_engine'.
   * @param $name
   *   The name of the item for which the filename is requested. Ignored for
   *   $type 'core'.
   * @param $filename
   *   (Optional) The filename of the item if it is to be set explicitly rather
   *   than by consulting the database.
   *
   * @return
   *   The filename of the requested item or NULL if the item is not found.
   */
  protected function drupalGetFilename($type, $name, $filename = NULL) {
    return drupal_get_filename($type, $name, $filename);
  }

}
