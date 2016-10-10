<?php

namespace Drupal\config_sync;

use Drupal\config_provider\Plugin\ConfigCollectorInterface;
use Drupal\config_sync\ConfigSyncListerInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageComparer;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\Extension;

/**
 * Provides methods related to listing configuration changes.
 */
class ConfigSyncLister implements ConfigSyncListerInterface {

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
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Constructs a ConfigSyncLister object.
   *
   * @param \Drupal\config_provider\Plugin\ConfigCollectorInterface $config_collector
   *   The config collector.
   * @param \Drupal\Core\Config\StorageInterface $active_storage
   *   The active storage.
   * @param \Drupal\Core\Config\StorageInterface $snapshot_extension_storage
   *   The snapshot storage for the items from the extension storage.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   */
  public function __construct(ConfigCollectorInterface $config_collector, StorageInterface $active_storage, StorageInterface $snapshot_extension_storage, ConfigManagerInterface $config_manager) {
    $this->configCollector = $config_collector;
    $this->activeStorage = $active_storage;
    $this->snapshotExtensionStorage = $snapshot_extension_storage;
    $this->configManager = $config_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensionChangelists(array $extension_names = []) {
    $changelist = [];
    // If no extensions were specified, use all installed extensions.
    if (empty($extension_names)) {
      $installed_extensions = $this->activeStorage->read('core.extension');
      foreach (array('module', 'theme') as $type) {
        if (!empty($installed_extensions[$type])) {
          $extension_names[$type] = array_keys($installed_extensions[$type]);
        }
      }
    }
    foreach ($extension_names as $type => $names) {
      foreach ($names as $name) {
        if ($extension_changelist = $this->getExtensionChangelist($type, $name)) {
          if (!isset($changelist[$type])) {
            $changelist[$type] = [];
          }
          $changelist[$type][$name] = $extension_changelist;
        }
      }
    }

    return $changelist;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensionChangelist($type, $name) {
    $pathname = $this->drupalGetFilename($type, $name);
    $extension = new Extension(\Drupal::root(), $type, $pathname);
    $extensions = [
      $name => $extension,
    ];

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
    // We're only concerned with create and update lists.
    unset($changelist['delete']);
    return array_filter($changelist);
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
