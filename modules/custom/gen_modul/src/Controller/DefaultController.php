<?php

namespace Drupal\gen_modul\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 *
 * @package Drupal\gen_modul\Controller
 */
class DefaultController extends ControllerBase {
  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function content($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: content with parameter(s): $name'),
    ];
  }

}
