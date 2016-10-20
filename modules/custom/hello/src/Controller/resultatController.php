<?php


namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

class resultatController extends ControllerBase {
    public function content($value1,$value2,$op,$result) {
        $content = "Votre résultat: ".$value1." ".$op." ".$value2." = <strong>".$result."</strong>";
        return array("#markup" => $content);
    }
}
