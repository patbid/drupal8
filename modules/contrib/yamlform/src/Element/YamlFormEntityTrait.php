<?php

namespace Drupal\yamlform\Element;

use Drupal\Core\Form\OptGroup;

/**
 * Trait for entity reference elements.
 */
trait YamlFormEntityTrait {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $info['#target_type'] = NULL;
    $info['#selection_handler'] = 'default';
    $info['#selection_settings'] = [];
    return $info;
  }

  /**
   * Get entity references as options.
   *
   * @param string $target_type
   *   The ID of the target entity type.
   * @param string $selection_handler
   *   The plugin ID of the entity reference selection handler.
   * @param array $selection_settings
   *   An array of settings that will be passed to the selection handler.
   *
   * @return array
   *   An associative array of entity key and labels as options.
   *   (e.g. array(1 => 'Node Title (1)').
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Thrown when the current user doesn't have access to the specified entity.
   *
   * @see \Drupal\system\Controller\EntityAutocompleteController
   */
  public static function getOptions($target_type, $selection_handler, $selection_settings) {
    $options = [
      'target_type' => $target_type,
      'handler' => $selection_handler,
      'handler_settings' => $selection_settings,
    ];

    /** @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface $selection_manager */
    $selection_manager = \Drupal::service('plugin.manager.entity_reference_selection');
    $handler = $selection_manager->getInstance($options);
    return OptGroup::flattenOptions($handler->getReferenceableEntities());
  }

}
