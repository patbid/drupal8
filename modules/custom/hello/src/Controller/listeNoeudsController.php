<?php


namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

class listeNoeudsController extends ControllerBase {
  public function content($noeud) {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
	$ids = \Drupal::entityQuery('node')->condition('type',$noeud)->pager('15')->execute();
	$entities = $storage->loadMultiple($ids);
	$tabtitles = array();
	foreach ($entities as $v) {
	  $tabtitles[] = $v->toLink();
	}
	$list = array('#theme' => 'item_list', '#items' => $tabtitles, '#list_type' => 'ol');
      $pager = array('#type' => 'pager');
	return array($list, $pager);
  }
}
