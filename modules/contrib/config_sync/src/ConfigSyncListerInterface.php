<?php

namespace Drupal\config_sync;

/**
 * Provides methods related to config listing.
 */
interface ConfigSyncListerInterface {

  /**
   * Returns a change list for all installed extensions.
   *
   * @param array $extension_names
   *   Array with keys of extension types ('module', 'theme') and values arrays
   *   of extension names.
   *
   * @return array
   *   Associative array of configuration changes keyed by extension type
   *   (module or theme) in which values are arrays keyed by extension name.
   */
  public function getExtensionChangelists(array $extension_names = []);

  /**
   * Returns a change list for a given module or theme.
   *
   * @param string $type
   *   The type of extension (module or theme).
   * @param string $name
   *   The machine name of the extension.
   *
   * @return array
   *   Associative array of configuration changes keyed by the type of change
   */
  public function getExtensionChangelist($type, $name);

}
