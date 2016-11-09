<?php

namespace Drupal\big_pipe_demo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a BigPipe demonstration block.
 *
 * @Block(
 *   id = "big_pipe_demo_block",
 *   admin_label = @Translation("BigPipe demonstration block"),
 * )
 */
class DemoBlock extends BlockBase implements ContainerFactoryPluginInterface, FormInterface {

  /**
   * The maximum duration, enforced to prevent the server from being attacked.
   */
  const MAX_DURATION = 2000;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a new DemoBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'default_duration' => 500,
      'html' => '<p>Hello world!</p>',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->configuration;

    $form['id'] = [
      '#type' => 'number',
      '#title' => $this->t('ID'),
      '#description' => $this->t('Identifier for this BigPipe demo block, in case multiple are shown on the same page'),
      '#default_value' => $config['id'],
      '#required' => TRUE,
      '#step' => 1,
      '#min' => 1,
      '#max' => 99,
    ];
    $form['html'] = [
      '#type' => 'textarea',
      '#default_value' => $config['html'],
      '#required' => TRUE,
    ];
    $form['default_duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Default duration (ms)'),
      '#description' => $this->t('The user will be able to override this.'),
      '#required' => TRUE,
      '#default_value' => $config['default_duration'],
      '#step' => 100,
      '#min' => 0,
      '#max' => static::MAX_DURATION,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['id'] = $form_state->getValue('id');
    $this->configuration['html'] = $form_state->getValue('html');
    $this->configuration['default_duration'] = $form_state->getValue('default_duration');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->formBuilder->getForm($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'big_pipe_demo_block';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $request = $this->requestStack->getCurrentRequest();

    $id = $this->configuration['id'];
    $duration = $this->configuration['default_duration'];

    // Let a query argument override the default duration.
    $override = $request->get('duration' . $id, FALSE);
    if ($override !== FALSE) {
      $duration = (int) $override;
    }

    // Simulate the configured or overridden duration.
    sleep(min($duration, static::MAX_DURATION) / 1000);

    $form_state->setMethod('GET');
    $form['html'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['html'],
    ];
    $form['proof_of_freshness'] = [
      '#markup' => $this->t('Generated in @duration ms at @time.', ['@time' => microtime(TRUE), '@duration' => $duration]),
    ];
    $form['override'] = [
      '#type' => 'details',
      '#title' => $this->t('Override duration'),
      '#attributes' => [
        'class' => ['container-inline'],
      ],
    ];
    $form['override']['duration' . $id] = [
      '#type' => 'number',
      '#step' => 100,
      '#min' => 0,
      '#max' => static::MAX_DURATION,
      '#title' => $this->t('Duration (ms)'),
      '#default_value' => $duration,
    ];
    $form['override']['actions'] = [
      '#type' => 'actions',
    ];
    $form['override']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reload'),
      // Prevent op from showing up in the query string.
      '#name' => '',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}
