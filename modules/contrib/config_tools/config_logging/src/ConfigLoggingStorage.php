<?php

/**
 * @file
 * Contains \Drupal\config_logging\ConfigLoggingStorage.
 */

namespace Drupal\config_logging;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\StorageInterface;

class ConfigLoggingStorage implements StorageInterface {

  const TARGET = 'target';

  const SOURCE = 'source';

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * @var string
   */
  protected $mode;

  /**
   * ConfigLoggingStorage constructor.
   */
  public function __construct(Config $config, $mode = self::TARGET) {
    $this->config = $config;
    if (!isset($this->mode) && !in_array($mode, [static::TARGET, static::SOURCE])) {
      throw new \Exception('Mode must be "target" or "source"');
    }
    $this->mode = $mode;
  }

  /**
   * {@inheritdoc}
   */
  public function exists($name) {
    return $this->config->getName() == $name;
  }

  /**
   * {@inheritdoc}
   */
  public function read($name) {
    if ($this->config->getName() != $name) {
      return FALSE;
    }
    if ($this->mode == static::TARGET) {
      return $this->config->get();
    }
    if ($this->mode == static::SOURCE) {
      return $this->config->getOriginal();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function readMultiple(array $names) {
    // TODO: Implement readMultiple() method.
  }

  /**
   * {@inheritdoc}
   */
  public function write($name, array $data) {
    // TODO: Implement write() method.
  }

  /**
   * {@inheritdoc}
   */
  public function delete($name) {
    // TODO: Implement delete() method.
  }

  /**
   * {@inheritdoc}
   */
  public function rename($name, $new_name) {
    // TODO: Implement rename() method.
  }

  /**
   * {@inheritdoc}
   */
  public function encode($data) {
    // TODO: Implement encode() method.
  }

  /**
   * {@inheritdoc}
   */
  public function decode($raw) {
    // TODO: Implement decode() method.
  }

  /**
   * {@inheritdoc}
   */
  public function listAll($prefix = '') {
    // TODO: Implement listAll() method.
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll($prefix = '') {
    // TODO: Implement deleteAll() method.
  }

  /**
   * {@inheritdoc}
   */
  public function createCollection($collection) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllCollectionNames() {
    // TODO: Implement getAllCollectionNames() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getCollectionName() {
    // TODO: Implement getCollectionName() method.
  }

}
