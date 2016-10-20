<?php

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 *
 * @Block(
 * 	id = "my_session_bloc",
 * 	admin_label = @Translation("Sessions actives")
 * )
 */
class hellosessions extends BlockBase {
  public function build() {
      $db = \Drupal::database();
      $nb = $db->select('sessions','s')
	  ->fields('s', array('uid'))
	  ->countQuery()
	  ->execute()
	  ->fetchField();

	return array('#markup' => $this->t('Il y a actuellement <strong>@nb</strong> sessions actives',
	  array('@nb' => $nb)));
  }

  protected function blockAccess(AccountInterface $account) {
      if ($account->hasPermission('access hello')) {
        return AccessResult::allowed();
      }
      else {
          return AccessResult::forbidden();
      }
  }
}