<?php

namespace Drupal\yamlform\Plugin\YamlFormElement;

use Drupal\yamlform\YamlFormSubmissionInterface;
use Drupal\yamlform\Element\YamlFormEntityTrait;

/**
 * Provides an 'entity_reference' with options trait.
 */
trait YamlFormEntityOptionsTrait {

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties() {
    $properties = parent::getDefaultProperties() + [
      'target_type' => '',
      'selection_handler' => '',
      'selection_settings' => [],
    ];
    unset($properties['options']);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, YamlFormSubmissionInterface $yamlform_submission) {
    if (!isset($element['#options'])) {
      $element['#options'] = YamlFormEntityTrait::getOptions($element['#target_type'], $element['#selection_handler'], $element['#selection_settings']);
    }
    parent::prepare($element, $yamlform_submission);
  }

  /**
   * {@inheritdoc}
   */
  protected function getElementSelectorInputsOptions(array $element) {
    if (!isset($element['#options'])) {
      $element['#options'] = YamlFormEntityTrait::getOptions($element['#target_type'], $element['#selection_handler'], $element['#selection_settings']);
    }
    return parent::getElementSelectorInputsOptions($element);
  }

}
