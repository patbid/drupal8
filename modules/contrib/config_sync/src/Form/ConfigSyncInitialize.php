<?php

namespace Drupal\config_sync\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\config_sync\ConfigSyncInitializerInterface;
use Drupal\config_sync\ConfigSyncListerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigSyncInitialize extends FormBase {

  /**
   * @var \Drupal\config_sync\configSyncInitializerInterface
   */
  protected $configSyncInitializer;

  /**
   * @var \Drupal\config_sync\configSyncListerInterface
   */
  protected $configSyncLister;

  /**
   * Constructs a new ConfigSync object.
   *
   * @param \Drupal\config_sync\ConfigSyncInitializerInterface $config_sync_initializer
   *   The configuration syncronizer initializer.
   * @param \Drupal\config_sync\ConfigSyncListerInterface $config_sync_lister
   *   The configuration syncronizer lister.
   */
  public function __construct(ConfigSyncInitializerInterface $config_sync_initializer, ConfigSyncListerInterface $config_sync_lister) {
    $this->configSyncInitializer = $config_sync_initializer;
    $this->configSyncLister = $config_sync_lister;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config_sync.initializer'),
      $container->get('config_sync.lister')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_sync_initialize';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $changelists = $this->configSyncLister->getExtensionChangelists();

    if (!empty($changelists)) {

      $form['retain_active_overrides'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Retain customizations'),
        '#default_value' => TRUE,
        '#description' => $this->t('If you select this option, configuration updates will be merged into the active configuration, retaining any customizations.'),
      ];

      $form['message'] = [
        '#markup' => $this->t('Use the button below to initialize data to be imported from updated modules and themes.'),
      ];

      $form['actions'] = [
        '#type' => 'actions',
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Initialize'),
      ];
    }
    else {
      $form['message'] = [
        '#markup' => $this->t('No configuration updates are available from installed modules and themes.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configSyncInitializer->initialize($form_state->getValue('retain_active_overrides'));
    $form_state->setRedirect('config_sync.import');
  }

}
