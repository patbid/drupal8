<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Select;

/**
 * Provides a form element for a entity select menu.
 *
 * @FormElement("yamlform_entity_select")
 */
class YamlFormEntitySelect extends Select {

  use YamlFormEntityTrait;

  /**
   * {@inheritdoc}
   */
  public static function processSelect(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!isset($element['#options'])) {
      $element['#options'] = self::getOptions($element['#target_type'], $element['#selection_handler'], $element['#selection_settings']);
    }
    $element = parent::processSelect($element, $form_state, $complete_form);
    return $element;
  }

}
