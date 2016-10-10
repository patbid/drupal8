<?php

namespace Drupal\yamlform;

use Drupal\Core\Entity\EntityInterface;

/**
 * Helper class YAML form entity methods.
 */
/**
 * Provides an interface defining a YAML form request handler.
 */
interface YamlFormRequestInterface {

  /**
   * Get the current request's source entity.
   *
   * @param string|array $ignored_types
   *   (optional) Array of ignore entity types.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The current request's source entity.
   */
  public function getCurrentSourceEntity($ignored_types = NULL);

  /**
   * Get YAML form associated with the current request.
   *
   * @return \Drupal\yamlform\YamlFormInterface|null
   *   The current request's YAML form.
   */
  public function getCurrentYamlForm();

  /**
   * Get the YAML form and source entity for the current request.
   *
   * @return array
   *   An array containing the YAML form and source entity for the current
   *   request.
   */
  public function getYamlFormEntities();

  /**
   * Get the YAML form submission and source entity for the current request.
   *
   * @return array
   *   An array containing the YAML form and source entity for the current
   *   request.
   */
  public function getYamlFormSubmissionEntities();

  /**
   * Get the route name for a YAML form/submission and source entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $yamlform_entity
   *   A YAML form or YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission's source entity.
   * @param string $route_name
   *   The route name.
   *
   * @return string
   *   A route name prefixed with 'entity.{entity_type_id}'
   *   or just 'entity'.
   */
  public function getRouteName(EntityInterface $yamlform_entity, EntityInterface $source_entity = NULL, $route_name);

  /**
   * Get the route parameters for a YAML form/submission and source entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $yamlform_entity
   *   A YAML form or YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission's source entity.
   *
   * @return array
   *   An array of route parameters.
   */
  public function getRouteParameters(EntityInterface $yamlform_entity, EntityInterface $source_entity = NULL);

  /**
   * Get the base route name for a YAML form/submission and source entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $yamlform_entity
   *   A YAML form or YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission's source entity.
   *
   * @return string
   *   If the source entity has a YAML form attached, 'entity.{entity_type_id}'
   *   or just 'entity'.
   */
  public function getBaseRouteName(EntityInterface $yamlform_entity, EntityInterface $source_entity = NULL);

  /**
   * Check if a source entity is attached to a YAML form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $yamlform_entity
   *   A YAML form or YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission's source entity.
   *
   * @return bool
   *   TRUE if a YAML form is attached to a YAML form submission source entity.
   */
  public function isValidSourceEntity(EntityInterface $yamlform_entity, EntityInterface $source_entity = NULL);

}
