<?php


namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelloController extends ControllerBase {
  public function content($param, $autre) {
	$name = $this->currentUser()->getAccountName();
    if ($param == ''){
      return array('#markup' => $this->t('Vous êtes sur la page Hello.<br>Votre nom utilisateur est <strong>@name</strong>
								<br>(@autre)', array('@name' => $name, '@param' => $param, '@autre' => $autre)));
	}
	else {
	  return array('#markup' => $this->t('Vous êtes sur la page Hello.<br>Votre nom utilisateur est <strong>@name</strong>
										<br>Mon param est <strong>@param</strong><br>(@autre)', array('@name' => $name, '@param' => $param, '@autre' => $autre)));
	}
  }
}
