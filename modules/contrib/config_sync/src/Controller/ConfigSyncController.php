<?php

namespace Drupal\config_sync\Controller;

use Drupal\config\Controller\ConfigController;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for config module routes.
 */
class ConfigSyncController extends ConfigController implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $class = parent::create($container);
    // Substitute our storage for the default one.
    $class->sourceStorage = $container->get('config_sync.merged_storage');
    return $class;
  }

  /**
   * {@inheritdoc}
   */
  public function diff($source_name, $target_name = NULL, $collection = NULL) {
    $build = parent::diff($source_name, $target_name = NULL, $collection = NULL);
    $build['back']['#url'] = Url::fromRoute('config_sync.import');
    return $build;
  }

}
