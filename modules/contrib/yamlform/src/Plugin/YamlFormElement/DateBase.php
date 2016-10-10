<?php

namespace Drupal\yamlform\Plugin\YamlFormElement;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Form\FormStateInterface;
use Drupal\yamlform\YamlFormElementBase;
use Drupal\yamlform\YamlFormSubmissionInterface;

/**
 * Provides a base 'date' class.
 */
abstract class DateBase extends YamlFormElementBase {

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, YamlFormSubmissionInterface $yamlform_submission) {
    // Don't used 'datetime_wrapper', instead use 'form_element' wrapper.
    // @see \Drupal\Core\Datetime\Element\Datelist
    // @see \Drupal\yamlform\Plugin\YamlFormElement\DateTime
    $element['#theme_wrappers'] = ['form_element'];

    // Must manually process #states.
    // @see drupal_process_states().
    if (isset($element['#states'])) {
      $element['#attached']['library'][] = 'core/drupal.states';
      $element['#wrapper_attributes']['data-drupal-states'] = Json::encode($element['#states']);
    }
    parent::prepare($element, $yamlform_submission);
  }

  /**
   * {@inheritdoc}
   */
  public function formatText(array &$element, $value, array $options = []) {
    $timestamp = strtotime($value);
    if (empty($timestamp)) {
      return $value;
    }

    $format = $this->getFormat($element);
    if (empty($format)) {
      switch ($element['#type']) {
        case 'datelist':
          $format = (isset($element['#date_part_order']) && !in_array($element['#date_part_order'], 'hour')) ? 'html_date' : 'html_datetime';
          break;

        default:
          $format = 'html_' . $element['#type'];
          break;
      }
      return \Drupal::service('date.formatter')->format($timestamp, $format);
    }
    elseif (DateFormat::load($format)) {
      return \Drupal::service('date.formatter')->format($timestamp, $format);
    }
    else {
      return date($format, $timestamp);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat(array $element) {
    if (isset($element['#format'])) {
      return $element['#format'];
    }
    else {
      return parent::getFormat($element);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultFormat() {
    return 'fallback';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormats() {
    $formats = parent::getFormats();
    $date_formats = DateFormat::loadMultiple();
    foreach ($date_formats as $date_format) {
      $formats[$date_format->id()] = $date_format->label();
    }
    return $formats;
  }

  /**
   * Form API callback. Convert DrupalDateTime array and object to ISO datetime.
   */
  public static function validate(array &$element, FormStateInterface $form_state) {
    $name = $element['#name'];
    $value = $form_state->getValue($name);
    /** @var \Drupal\Core\Datetime\DrupalDateTime $datetime */
    if ($datetime = $value['object']) {
      $form_state->setValue($name, $datetime->format('c') ?: '');
    }
    else {
      $form_state->setValue($name, '');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Allow custom date formats to be entered.
    $form['display']['format']['#type'] = 'yamlform_select_other';
    $form['display']['format']['#other__option_label'] = $this->t('Custom date format...');
    $form['display']['format']['#other__description'] = $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.');

    return $form;
  }

}
