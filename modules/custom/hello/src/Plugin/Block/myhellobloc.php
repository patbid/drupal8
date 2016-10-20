<?php

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 * 	id = "my_hello_bloc",
 * 	admin_label = @Translation("Bonjour!")
 * )
 */
class myhellobloc extends BlockBase {
  public function build() {
	$name = \Drupal::currentUser()->getAccountName();
	$dat =  \Drupal::service('date.formatter')->format(time(), 'html_time');
	return array('#markup' => $this->t('Bienvenue <strong>@name</strong>. Il est @dat.<br><br>', array('@name' => $name, '@dat' => $dat)),
	  			 '#cache' => array('max-age' => '1000'));
  }
}