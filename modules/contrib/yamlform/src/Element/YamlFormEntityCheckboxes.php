<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Checkboxes;

/**
 * Provides a form element for a entity checkboxes.
 *
 * @FormElement("yamlform_entity_checkboxes")
 */
class YamlFormEntityCheckboxes extends Checkboxes {

  use YamlFormEntityTrait;

  /**
   * {@inheritdoc}
   */
  public static function processCheckboxes(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!isset($element['#options'])) {
      $element['#options'] = self::getOptions($element['#target_type'], $element['#selection_handler'], $element['#selection_settings']);
    }
    return parent::processCheckboxes($element, $form_state, $complete_form);
  }

}
