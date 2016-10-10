<?php

namespace Drupal\yamlform\Element;

/**
 * Provides a form element for an address element.
 *
 * @FormElement("yamlform_address")
 */
class YamlFormAddress extends YamlFormCompositeBase {

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements($use_flexbox = FALSE) {
    $elements = [];

    $elements['address'] = [
      '#type' => 'textfield',
      '#title' => t('Address'),
    ] + static::setFlexbox($use_flexbox);

    $elements['address_2'] = [
      '#type' => 'textfield',
      '#title' => t('Address 2'),
    ] + static::setFlexbox($use_flexbox);

    if ($use_flexbox) {
      $elements['start_city_state_flexbox'] = ['#markup' => '<div class="yamlform-flexbox">'];
    }
    $elements['city'] = [
      '#type' => 'textfield',
      '#title' => t('City/Town'),
    ] + static::setFlex($use_flexbox);
    $elements['state_province'] = [
      '#type' => 'select',
      '#title' => t('State/Province'),
      '#options' => 'state_province_names',
    ] + static::setFlex($use_flexbox);
    if ($use_flexbox) {
      $elements['end_city_state_flexbox'] = ['#markup' => '</div>'];
    }

    if ($use_flexbox) {
      $elements['start_postal_country_flexbox'] = ['#markup' => '<div class="yamlform-flexbox">'];
    }
    $elements['postal_code'] = [
      '#type' => 'textfield',
      '#title' => t('Zip/Postal Code'),
    ] + static::setFlex($use_flexbox);
    $elements['country'] = [
      '#type' => 'select',
      '#title' => t('Country'),
      '#options' => 'country_names',
    ] + static::setFlex($use_flexbox);
    if ($use_flexbox) {
      $elements['end_postal_country_flexbox'] = ['#markup' => '</div>'];
    }

    return $elements;
  }

}
