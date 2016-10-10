<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for checkboxes with an other option.
 *
 * @FormElement("yamlform_checkboxes_other")
 */
class YamlFormCheckboxesOther extends FormElement {

  const OTHER_OPTION = '_other_';

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processYamlFormCheckboxesOther'],
      ],
      '#theme_wrappers' => ['form_element'],
      '#options' => [],
      '#other__option_delimiter' => ', ',
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

      $default_value = array_combine($element['#default_value'], $element['#default_value']);
      if ($other_options = array_diff_key($default_value, $element['#options'])) {
        $element['checkboxes']['#default_value'] = $element['#default_value'] + [self::OTHER_OPTION => self::OTHER_OPTION];
        $element['other']['#default_value'] = implode($element['#other__option_delimiter'], $other_options);
      }
      return $element;
    }
    return NULL;
  }

  /**
   * Processes a checkboxes other form element.
   *
   * See checkboxes form element for checkboxes properties.
   *
   * @see \Drupal\Core\Render\Element\Checkboxes
   */
  public static function processYamlFormCheckboxesOther(&$element, FormStateInterface $form_state, &$complete_form) {
    // Build checkboxes element with selected properties.
    $properties = [
      '#options',
      '#options_display',
      '#default_value',
      '#attributes',
      '#required',
      '#ajax',
    ];
    $element['checkboxes']['#type'] = 'checkboxes';
    $element['checkboxes'] += array_intersect_key($element, array_combine($properties, $properties));
    if (!isset($element['checkboxes']['#options'][self::OTHER_OPTION])) {
      $element['checkboxes']['#options'][self::OTHER_OPTION] = (!empty($element['#other__option_label'])) ? $element['#other__option_label'] : t('Other...');
    }
    $element['checkboxes']['#error_no_message'] = TRUE;

    // Build other textfield.
    $element['other']['#type'] = 'textfield';
    $element['other']['#placeholder'] = t('Enter other...');
    $element['other']['#error_no_message'] = TRUE;
    foreach ($element as $key => $value) {
      if (strpos($key, '#other__') === 0) {
        $element['other'][str_replace('#other__', '#', $key)] = $value;
      }
    }
    $element['other']['#wrapper_attributes']['class'][] = 'js-yamlform-checkboxes-other-input';
    $element['other']['#wrapper_attributes']['class'][] = 'yamlform-checkboxes-other-input';

    // Remove options since they are being moved the checkboxes element.
    unset($element['#options']);

    $element['#tree'] = TRUE;
    if (isset($element['#element_validate'])) {
      $element['#element_validate'] = array_merge([[get_called_class(), 'validateYamlFormCheckboxesOther']], $element['#element_validate']);
    }
    else {
      $element['#element_validate'] = [[get_called_class(), 'validateYamlFormCheckboxesOther']];
    }
    $element['#attached']['library'][] = 'yamlform/yamlform.element.other';

    return $element;
  }

  /**
   * Validates a checkboxes other element.
   */
  public static function validateYamlFormCheckboxesOther(&$element, FormStateInterface $form_state, &$complete_form) {
    $checkboxes_value = $element['checkboxes']['#value'];
    $other_value = $element['other']['#value'];

    $value = $checkboxes_value;
    if (isset($checkboxes_value[self::OTHER_OPTION])) {
      unset($value[self::OTHER_OPTION]);
      if ($other_value !== '') {
        $value[$other_value] = $other_value;
      }
    }

    $is_empty = empty($value);
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

    $form_state->setValueForElement($element['checkboxes'], NULL);
    $form_state->setValueForElement($element['other'], NULL);
    $form_state->setValueForElement($element, $value);

    return $element;
  }

}
