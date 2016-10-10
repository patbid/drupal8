<?php

namespace Drupal\layout_plugin_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\layout_plugin\Plugin\Layout\LayoutPluginManager;

/**
 * Class LayoutPluginUiController.
 *
 * @package Drupal\layout_plugin_ui\Controller
 */
class LayoutPluginUiController extends ControllerBase {

  /**
   * Drupal\layout_plugin\Plugin\Layout\LayoutPluginManager definition.
   *
   * @var Drupal\layout_plugin\Plugin\Layout\LayoutPluginManager
   */
  protected $plugin_manager_layout_plugin;

  /**
   * {@inheritdoc}
   */
  public function __construct(LayoutPluginManager $plugin_manager_layout_plugin) {
    $this->plugin_manager_layout_plugin = $plugin_manager_layout_plugin;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.layout_plugin')
    );
  }

  public function content() {
    return [
      '#theme' => 'layout_plugin_ui',
      '#layouts' => $this->plugin_manager_layout_plugin->getDefinitions(),
    ];
  }

}
