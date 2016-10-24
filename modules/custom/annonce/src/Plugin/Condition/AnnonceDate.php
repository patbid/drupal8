<?php

namespace Drupal\annonce\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;

/**
 * Provides a 'whatever' condition to enable a condition based in module selected status.
 *
 * @Condition(
 *   id = "annonce_date",
 *   label = @Translation("annonce_date"),
 * )
 *
 */
class AnnonceDate extends ConditionPluginBase implements ContainerFactoryPluginInterface {
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition
        );
    }

    /**
     * Creates a new ExampleCondition instance.
     *
     * @param array $configuration
     *   The plugin configuration, i.e. an array with configuration values keyed
     *   by configuration option name. The special key 'context' may be used to
     *   initialize the defined contexts by setting it to an array of context
     *   values keyed by context names.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

        $form['date_deb'] = array(
            '#type' => 'date',
            '#title' => $this->t('Date début'),
            '#default_value' => $this->configuration['date_deb'],
        );


        $form['date_fin'] = array(
            '#type' => 'date',
            '#title' => $this->t('Date fin'),
            '#default_value' => $this->configuration['date_fin'],
            );

        $form['negate']['#access']= FALSE;
        return parent::buildConfigurationForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
        $this->configuration['date_deb'] = $form_state->getValue('date_deb');
        $this->configuration['date_fin'] = $form_state->getValue('date_fin');
        parent::submitConfigurationForm($form, $form_state);
    }

    public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
        $begin_date_value = $form_state->getValue('date_deb');
        $end_date_value = $form_state->getValue('date_fin');

        if (($begin_date_value == "") OR ($begin_date_value > $end_date_value)){
            $form_state->setErrorByName('date_deb', t('Error ! Begin date must be anterious to end date.'));
            $form_state->setErrorByName('date_fin');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return array('test' => array()) + parent::defaultConfiguration();
    }

    /**
     * Evaluates the condition and returns TRUE or FALSE accordingly.
     *
     * @return bool
     *   TRUE if the condition has been met, FALSE otherwise.
     */
    public function evaluate() {
        $begin_date = $this->configuration['date_deb'];
        $end_date = $this->configuration['date_fin'];

        // No date
        if((!(isset($begin_date)) || $begin_date == "") && ((!(isset($end_date))) || $end_date == "")){
            return TRUE;
        }
        // Only begin date
        elseif(isset($begin_date) && $begin_date != "" && strtotime($begin_date) <= time() && ((!(isset($end_date))) || $end_date == "")) {
            return TRUE;
        }
        // Only end date
        elseif(isset($end_date) && $end_date != "" && strtotime($end_date) >= time() && ((!(isset($begin_date))) || $begin_date == "")) {
            return TRUE;
        }
        // Begin and end dates
        elseif(isset($begin_date) && $begin_date != "" && isset($end_date) && $end_date != "" && strtotime($begin_date) <= time() && strtotime($end_date) >= time()) {
            return TRUE;
        }
        else{
            return FALSE;
        }

        return FALSE;
    }

    /**
     * Provides a human readable summary of the condition's configuration.
     */
    public function summary()
    {

        return t('date de visualisation');
    }

}
