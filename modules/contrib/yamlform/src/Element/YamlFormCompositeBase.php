<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Render\Element\CompositeFormElementTrait;
use Drupal\yamlform\Entity\YamlFormOptions as YamlFormOptionsEntity;

/**
 * Provides an base composite form element.
 */
abstract class YamlFormCompositeBase extends FormElement {

  use CompositeFormElementTrait;

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processYamlFormComposite'],
        [$class, 'processAjaxForm'],
      ],
      '#pre_render' => [
        [$class, 'preRenderCompositeFormElement'],
      ],
      '#theme_wrappers' => ['container'],
      '#required' => FALSE,
      '#flexbox' => TRUE,
    ];
  }

  /**
   * Get a renderable array of form elements.
   *
   * @param bool $use_flexbox
   *   Flag to enabled/disable Flexbox layouts.
   *
   * @return array
   *   A renderable array of form elements, containing the base properties
   *   for the composite's form elements.
   */
  public static function getCompositeElements($use_flexbox = FALSE) {
    return [];
  }

  /**
   * Get a renderable array of form elements using Flexbox layout.
   *
   * @param array $elements
   *   A renderable array of form elements.
   *
   * @return array
   *   A renderable array of form elements using Flexbox layout.
   */
  public static function getFlexboxLayout(array $elements) {
    return $elements;
  }

  /**
   * Set flex(box) class attributes.
   *
   * @param bool $use_flexbox
   *   Flag to enabled/disable Flexbox layouts.
   * @param int $flex
   *   The flex property specifies the length of the item, relative to the rest
   *   of the flexible items inside the same container.
   *
   * @return array
   *   An associative array of wrapper attributes containing yamlform-flex
   *   classes.
   */
  public static function setFlex($use_flexbox = FALSE, $flex = 1) {
    return ($use_flexbox) ? [
      '#prefix' => '<div class="yamlform-flex yamlform-flex--' . $flex . '"><div class="yamlform-flex--container">',
      '#suffix' => '</div></div>',
    ] : [];
  }

  /**
   * Set flex(box) class attributes.
   *
   * @param bool $use_flexbox
   *   Flag to enabled/disable Flexbox layouts.
   * @param int $flex
   *   The flex property specifies the length of the item, relative to the rest
   *   of the flexible items inside the same container.
   *
   * @return array
   *   An associative array of wrapper attributes containing yamlform-flex
   *   classes.
   */
  public static function setFlexbox($use_flexbox = FALSE, $flex = 1) {
    if (!$use_flexbox) {
      return [];
    }

    $properties = [
      '#prefix' => '<div class="yamlform-flexbox">',
      '#suffix' => '</div>',
    ];
    if ($flex) {
      $properties['#prefix'] .= '<div class="yamlform-flex yamlform-flex--' . $flex . '"><div class="yamlform-flex--container">';
      $properties['#suffix'] .= '</div></div>';
    }
    return $properties;
  }

  /**
   * Processes a composite form element.
   */
  public static function processYamlFormComposite(&$element, FormStateInterface $form_state, &$complete_form) {
    if (isset($element['#initialize'])) {
      return $element;
    }

    $element['#initialize'] = TRUE;
    $element['#tree'] = TRUE;
    $element['#flexbox'] = (!empty($element['#flexbox'])) ? TRUE : FALSE;
    $composite_elements = static::getCompositeElements($element['#flexbox']);
    foreach ($composite_elements as $composite_key => &$composite_element) {
      // Transfer '#{composite_key}_{property}' from main element to composite
      // element.
      foreach ($element as $property_key => $property_value) {
        if (strpos($property_key, '#' . $composite_key . '__') === 0) {
          $composite_property_key = str_replace('#' . $composite_key . '__', '#', $property_key);
          $composite_element[$composite_property_key] = $property_value;
        }
      }

      if (isset($element['#value'][$composite_key])) {
        $composite_element['#value'] = $element['#value'][$composite_key];
      }

      // Never required hidden composite elements.
      if (isset($composite_element['#access']) && $composite_element['#access'] == FALSE) {
        unset($composite_element['#required']);
      }

      if (isset($composite_element['#options'])) {
        $composite_element['#options'] = YamlFormOptionsEntity::getElementOptions($composite_element);
      }
    }

    $element += $composite_elements;
    $element['#element_validate'] = [[get_called_class(), 'validateYamlFormComposite']];

    if ($element['#flexbox']) {
      $element += ['#prefix' => '', '#suffix' => ''];
      $element['#prefix'] = $element['#prefix'] . '<div class="yamlform-flexbox">';
      $element['#suffix'] = '</div>' . $element['#suffix'];

      $element['#attached']['library'][] = 'yamlform/yamlform.element.flexbox';
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $composite_elements = static::getCompositeElements();
    $default_value = [];
    foreach ($composite_elements as $composite_key => $composite_element) {
      if (isset($composite_element['#type']) && $composite_element['#type'] != 'label') {
        $default_value[$composite_key] = '';
      }
    }

    if ($input === FALSE) {
      if (empty($element['#default_value']) || !is_array($element['#default_value'])) {
        $element['#default_value'] = [];
      }
      return $element['#default_value'] + $default_value;
    }
    return $input + $default_value;
  }

  /**
   * Validates a composite element.
   */
  public static function validateYamlFormComposite(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];

    // Validate required composite elements.
    $composite_elements = static::getCompositeElements();
    foreach ($composite_elements as $composite_key => $composite_element) {
      if (!empty($element[$composite_key]['#required']) && $value[$composite_key] == '') {
        if (isset($element[$composite_key]['#title'])) {
          $form_state->setError($element[$composite_key], t('@name field is required.', ['@name' => $element[$composite_key]['#title']]));
        }
      }
    }

    return $element;
  }

}
