<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\Render\Element\FormElement;
use Drupal\yamlform\Utility\YamlFormOptionsHelper;

/**
 * Provides a form element for a select menu with an other option.
 *
 * See #empty_option and #empty_value for an explanation of various settings for
 * a select element, including behavior if #required is TRUE or FALSE.
 *
 * @FormElement("yamlform_select_other")
 */
class YamlFormSelectOther extends FormElement {

  const OTHER_OPTION = '_other_';

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#process' => [
        [$class, 'processYamlFormSelectOther'],
        [$class, 'processAjaxForm'],
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

      if (isset($element['#multiple']) && $element['#multiple']) {
        if (is_array($default_value)) {
          $flattened_options = OptGroup::flattenOptions($element['#options']);
          if ($other_options = array_diff_key(array_combine($default_value, $default_value), $flattened_options)) {
            $element['select']['#default_value'] = $default_value + [self::OTHER_OPTION => self::OTHER_OPTION];
            $element['other']['#default_value'] = implode($element['#other__option_delimiter'], $other_options);
          }
          return $element;
        }
      }
      elseif (!YamlFormOptionsHelper::hasOption($default_value, $element['#options'])) {
        $element['select']['#default_value'] = self::OTHER_OPTION;
        $element['other']['#default_value'] = $default_value;
        return $element;
      }
      else {
        return $element;
      }
    }
    return NULL;
  }

  /**
   * Processes a select other list form element.
   *
   * See select list form element for select list properties.
   *
   * @see \Drupal\Core\Render\Element\Select
   */
  public static function processYamlFormSelectOther(&$element, FormStateInterface $form_state, &$complete_form) {
    // Build select element with selected properties.
    $properties = [
      '#title',
      '#options',
      '#default_value',
      '#multiple',
      '#attributes',
      '#empty_value',
      '#empty_option',
      '#title_display',
      '#description_display',
      '#required',
    ];
    $element['select']['#type'] = 'select';
    $element['select'] += array_intersect_key($element, array_combine($properties, $properties));
    if (!isset($element['select']['#options'][self::OTHER_OPTION])) {
      $element['select']['#options'][self::OTHER_OPTION] = (!empty($element['#other__option_label'])) ? $element['#other__option_label'] : t('Other...');
    }
    $element['select']['#error_no_message'] = TRUE;

    // Build other textfield.
    $element['other']['#type'] = 'textfield';
    $element['other']['#placeholder'] = t('Enter other...');
    $element['other']['#error_no_message'] = TRUE;
    foreach ($element as $key => $value) {
      if (strpos($key, '#other__') === 0) {
        $element['other'][str_replace('#other__', '#', $key)] = $value;
      }
    }
    $element['other']['#wrapper_attributes']['class'][] = 'js-yamlform-select-other-input';
    $element['other']['#wrapper_attributes']['class'][] = 'yamlform-select-other-input';

    // Remove title and options since they are being moved the select element.
    unset($element['#title'], $element['#options']);

    $element['#tree'] = TRUE;
    if (isset($element['#element_validate'])) {
      $element['#element_validate'] = array_merge([[get_called_class(), 'validateYamlFormSelectOther']], $element['#element_validate']);
    }
    else {
      $element['#element_validate'] = [[get_called_class(), 'validateYamlFormSelectOther']];
    }
    $element['#attached']['library'][] = 'yamlform/yamlform.element.other';

    return $element;

  }

  /**
   * Validates a select other element.
   */
  public static function validateYamlFormSelectOther(&$element, FormStateInterface $form_state, &$complete_form) {
    $select_value = $element['select']['#value'];
    $other_value = $element['other']['#value'];

    if (isset($element['#multiple']) && $element['#multiple']) {
      $value = $select_value;
      if (isset($select_value[self::OTHER_OPTION])) {
        unset($value[self::OTHER_OPTION]);
        if ($other_value !== '') {
          $value[$other_value] = $other_value;
        }
      }
      $is_empty = (empty($value)) ? TRUE : FALSE;
    }
    else {
      $value = $select_value;
      if ($select_value == self::OTHER_OPTION) {
        $value = $other_value;
      }
      $is_empty = ($value === '' || $value === NULL) ? TRUE : FALSE;
    }

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

    $form_state->setValueForElement($element['select'], NULL);
    $form_state->setValueForElement($element['other'], NULL);
    $form_state->setValueForElement($element, $value);
    return $element;
  }

}
