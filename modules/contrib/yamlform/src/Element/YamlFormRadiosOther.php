<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for radio buttons with an other option.
 *
 * @FormElement("yamlform_radios_other")
 */
class YamlFormRadiosOther extends FormElement {

  const OTHER_OPTION = '_other_';

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processYamlFormRadiosOther'],
      ],
      '#theme_wrappers' => ['form_element'],
      '#options' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input === FALSE) {
      $default_value = isset($element['#default_value']) ? $element['#default_value'] : NULL;
      if (!$default_value) {
        return $element;
      }

      if (!isset($element['#options'][$default_value])) {
        $element['radios']['#default_value'] = self::OTHER_OPTION;
        $element['other']['#default_value'] = $default_value;
      }
      return $element;
    }
    return NULL;
  }

  /**
   * Processes a radios other form element.
   *
   * See radios form element for radios properties.
   *
   * @see \Drupal\Core\Render\Element\Radios
   */
  public static function processYamlFormRadiosOther(&$element, FormStateInterface $form_state, &$complete_form) {
    // Build radios element with selected properties.
    $properties = [
      '#options',
      '#options_display',
      '#default_value',
      '#attributes',
      '#required',
      '#ajax',
    ];
    $element['radios']['#type'] = 'radios';
    $element['radios'] += array_intersect_key($element, array_combine($properties, $properties));
    if (!isset($element['radios']['#options'][self::OTHER_OPTION])) {
      $element['radios']['#options'][self::OTHER_OPTION] = (!empty($element['#other__option_label'])) ? $element['#other__option_label'] : t('Other...');
    }
    $element['radios']['#error_no_message'] = TRUE;

    // Build other textfield.
    $element['other']['#type'] = 'textfield';
    $element['other']['#placeholder'] = t('Enter other...');
    $element['other']['#error_no_message'] = TRUE;
    foreach ($element as $key => $value) {
      if (strpos($key, '#other__') === 0) {
        $element['other'][str_replace('#other__', '#', $key)] = $value;
      }
    }
    $element['other']['#wrapper_attributes']['class'][] = 'js-yamlform-radios-other-input';
    $element['other']['#wrapper_attributes']['class'][] = 'yamlform-radios-other-input';

    // Remove options since they are being moved the radios element.
    unset($element['#options']);

    $element['#tree'] = TRUE;
    $element['#element_validate'] = [[get_called_class(), 'validateYamlFormRadiosOther']];
    $element['#attached']['library'][] = 'yamlform/yamlform.element.other';

    return $element;

  }

  /**
   * Validates a radios other element.
   */
  public static function validateYamlFormRadiosOther(&$element, FormStateInterface $form_state, &$complete_form) {
    $radios_value = $element['radios']['#value'];
    $other_value = $element['other']['#value'];
    $value = $radios_value;
    if ($radios_value == self::OTHER_OPTION) {
      $value = $other_value;
    }

    $is_empty = ($value === '' || $value === NULL);
    $has_access = (!isset($element['#access']) || $element['#access'] === TRUE);
    if ($element['#required'] && $is_empty && $has_access) {
      if (isset($element['#required_error'])) {
        $form_state->setError($element, $element['#required_error']);
      }
      elseif (isset($element['#title'])) {
        $form_state->setError($element, t('@name field is required.', ['@name' => $element['#title']]));
      }
      else {
        $form_state->setError($element);
      }
    }

    $form_state->setValueForElement($element['radios'], NULL);
    $form_state->setValueForElement($element['other'], NULL);
    $form_state->setValueForElement($element, $value);

    return $element;
  }

}
