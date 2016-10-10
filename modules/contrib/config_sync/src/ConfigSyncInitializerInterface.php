<?php

namespace Drupal\config_sync;

/**
 * Provides methods for updating site configuration from extensions.
 */
interface ConfigSyncInitializerInterface {

  /**
   * Initializes the merge storage with available configuration updates.
   *
   * @param bool $retain_active_overrides
   *   Whether to retain configuration customizations in the active
   *   configuration storage. Defaults to TRUE.
   * @param array $extension_names
   *   Array with keys of extension types ('module', 'theme') and values arrays
   *   of extension names.
   */
  public function initialize($retain_active_overrides = TRUE, array $extension_names = []);

}
