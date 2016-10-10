<?php

namespace Drupal\config_sync;

/**
 * The ConfigSyncSnapshotter provides helper functions for taking snapshots of
 * extension-provided configuration.
 */
interface ConfigSyncSnapshotterInterface {

  /**
   * Takes a snapshot of configuration from all enabled modules and themes.
   *
   * Only items not already in the snapshot storage are added.
   */
  public function refreshSnapshot();

  /**
   * Deletes all records from the snapshot.
   */
  public function deleteSnapshot();

}
