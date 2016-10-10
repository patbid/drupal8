<?php

namespace Drupal\yamlform\Element;

/**
 * Provides a form element for an name element.
 *
 * @FormElement("yamlform_name")
 */
class YamlFormName extends YamlFormCompositeBase {

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements($use_flexbox = FALSE) {
    $elements = [];

    if ($use_flexbox) {
      $elements['start_name_flexbox'] = ['#markup' => '<div class="yamlform-flexbox">'];
    }

    $elements['title'] = [
      '#type' => 'yamlform_select_other',
      '#title' => t('Title'),
      '#options' => 'titles',
    ] + static::setFlex($use_flexbox, 2);
    $elements['first'] = [
      '#type' => 'textfield',
      '#title' => t('First'),
    ] + static::setFlex($use_flexbox, 3);
    $elements['middle'] = [
      '#type' => 'textfield',
      '#title' => t('Middle'),
    ] + static::setFlex($use_flexbox, 2);
    $elements['last'] = [
      '#type' => 'textfield',
      '#title' => t('Last'),
    ] + static::setFlex($use_flexbox, 3);
    $elements['suffix'] = [
      '#type' => 'textfield',
      '#title' => t('Suffix'),
    ] + static::setFlex($use_flexbox, 1);
    $elements['degree'] = [
      '#type' => 'textfield',
      '#title' => t('Degree'),
    ] + static::setFlex($use_flexbox, 1);

    if ($use_flexbox) {
      $elements['end_name_flexbox'] = ['#markup' => '</div>'];
    }

    return $elements;
  }

}
