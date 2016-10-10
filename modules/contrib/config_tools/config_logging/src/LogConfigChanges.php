<?php

/**
 * @file
 * Contains \Drupal\config_logging\LogConfigChanges.
 */

namespace Drupal\config_logging;

use Drupal\Component\Diff\DiffFormatter;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogConfigChanges implements EventSubscriberInterface {

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * The logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $loggerChannel;

  /**
   * LogConfigChanges constructor.
   */
  public function __construct(ConfigManagerInterface $config_manager, LoggerChannelFactoryInterface $logger_factory) {
    $this->configManager = $config_manager;
    $this->loggerChannel = $logger_factory->get('config_logging');
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[ConfigEvents::SAVE][] = array('diffConfig', 10);
    $events[ConfigEvents::DELETE][] = array('diffConfig', 10);
    return $events;
  }

  /**
   * Generates diff of changes to a config object.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The event.
   *
   * @return void
   */
  public function diffConfig(ConfigCrudEvent $event) {
    $target_storage = new ConfigLoggingStorage($event->getConfig());
    $source_storage = new ConfigLoggingStorage($event->getConfig(), ConfigLoggingStorage::SOURCE);
    $diff = $this->configManager->diff($target_storage, $source_storage, $event->getConfig()->getName());
    // @todo make the diff formatter swappable.
    // \Drupal::service('diff.formatter')->format() does not conform to the
    // return expectations of the parent class. It should return a string but
    // returns an array instead, so we can't use it.
    /** @var \Drupal\Component\Diff\DiffFormatter $diffFormatter */
    $diffFormatter = new DiffFormatter();
    foreach ($diff->getEdits() as $edit) {
      if ($edit->type != 'copy') {
        $this->loggerChannel->info($diffFormatter->format($diff));
      }
    }
  }

}
