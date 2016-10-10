<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Radios;

/**
 * Provides a form element for a entity radios.
 *
 * @FormElement("yamlform_entity_radios")
 */
class YamlFormEntityRadios extends Radios {

  use YamlFormEntityTrait;

  /**
   * {@inheritdoc}
   */
  public static function processRadios(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!isset($element['#options'])) {
      $element['#options'] = self::getOptions($element['#target_type'], $element['#selection_handler'], $element['#selection_settings']);
    }
    return parent::processRadios($element, $form_state, $complete_form);
  }

}
