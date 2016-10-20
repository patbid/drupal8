<?php

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AutoBloc' block.
 *
 * @Block(
 *  id = "auto_bloc",
 *  admin_label = @Translation("Auto bloc"),
 * )
 */
class AutoBloc extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['auto_bloc']['#markup'] = 'Implement AutoBloc.';

    return $build;
  }

}
