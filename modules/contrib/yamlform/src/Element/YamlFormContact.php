<?php

namespace Drupal\yamlform\Element;

/**
 * Provides a form element for a contact element.
 *
 * @FormElement("yamlform_contact")
 */
class YamlFormContact extends YamlFormAddress {

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements($use_flexbox = FALSE) {
    $elements = [];

    if ($use_flexbox) {
      $elements['start_name_company_flexbox'] = ['#markup' => '<div class="yamlform-flexbox">'];
    }
    $elements['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
    ] + static::setFlex($use_flexbox);
    $elements['company'] = [
      '#type' => 'textfield',
      '#title' => t('Company'),
    ] + static::setFlex($use_flexbox);
    if ($use_flexbox) {
      $elements['end_name_company_flexbox'] = ['#markup' => '</div>'];
    }

    if ($use_flexbox) {
      $elements['start_email_phone_flexbox'] = ['#markup' => '<div class="yamlform-flexbox">'];
    }
    $elements['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
    ] + static::setFlex($use_flexbox);
    $elements['phone'] = [
      '#type' => 'tel',
      '#title' => t('Phone'),
    ] + static::setFlex($use_flexbox);
    if ($use_flexbox) {
      $elements['end_email_phone_flexbox'] = ['#markup' => '</div>'];
    }

    $elements += parent::getCompositeElements($use_flexbox);

    return $elements;
  }

}
