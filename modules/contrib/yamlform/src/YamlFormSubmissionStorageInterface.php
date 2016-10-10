<?php

namespace Drupal\yamlform;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an interface for YAML form submission classes.
 */
interface YamlFormSubmissionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Return status for saving of YAML forb submission when saving results is disabled.
   */
  const SAVED_DISABLED = 0;

  /**
   * Get YAML form submission entity field definitions.
   *
   * The helper method is generally used for exporting results.
   *
   * @see \Drupal\yamlform\Element\YamlFormExcludedColumns
   * @see \Drupal\yamlform\Controller\YamlFormResultsExportController
   *
   * @return array
   *   An associative array of field definition key by field name containing
   *   title, name, and datatype.
   */
  public function getFieldDefinitions();

  /**
   * Delete all YAML form submissions.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   (optional) The YAML form to delete the submissions from.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param int $limit
   *   (optional) Number of submissions to be deleted.
   * @param int $max_sid
   *   (optional) Maximum YAML form submission id.
   *
   * @return int
   *   The number of YAML form submissions deleted.
   */
  public function deleteAll(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, $limit = NULL, $max_sid = NULL);

  /**
   * Get YAML form submission draft.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   A user account.
   *
   * @return \Drupal\yamlform\YamlFormSubmissionInterface
   *   A YAML form submission.
   */
  public function loadDraft(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get the total number of submissions.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   (optional) A YAML form. If set the total number of submissions for the
   *   YAML form will be returned.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   (optional) A user account.
   *
   * @return int
   *   Total number of submissions.
   */
  public function getTotal(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get the maximum sid.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   (optional) A YAML form. If set the total number of submissions for the
   *   YAML form will be returned.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   (optional) A user account.
   *
   * @return int
   *   Total number of submissions.
   */
  public function getMaxSubmissionId(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get a YAML form's first submission.
   *
   * @param \Drupal\yamlform\YamlFormInterface $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\yamlform\YamlFormSubmissionInterface|null
   *   The YAML form's first submission.
   */
  public function getFirstSubmission(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get a YAML form's last submission.
   *
   * @param \Drupal\yamlform\YamlFormInterface $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\yamlform\YamlFormSubmissionInterface|null
   *   The YAML form's last submission.
   */
  public function getLastSubmission(YamlFormInterface $yamlform, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get a YAML form submission's previous sibling.
   *
   * @param \Drupal\yamlform\YamlFormSubmissionInterface $yamlform_submission
   *   A YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\yamlform\YamlFormSubmissionInterface|null
   *   The YAML form submission's previous sibling.
   */
  public function getPreviousSubmission(YamlFormSubmissionInterface $yamlform_submission, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get a YAML form submission's next sibling.
   *
   * @param \Drupal\yamlform\YamlFormSubmissionInterface $yamlform_submission
   *   A YAML form submission.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   (optional) A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\yamlform\YamlFormSubmissionInterface|null
   *   The YAML form submission's next sibling.
   */
  public function getNextSubmission(YamlFormSubmissionInterface $yamlform_submission, EntityInterface $source_entity = NULL, AccountInterface $account = NULL);

  /**
   * Get YAML form submission source entity types.
   *
   * @param \Drupal\yamlform\YamlFormInterface $yamlform
   *   A YAML form.
   *
   * @return array
   *   An array of entity types that the YAML form has been submitted from.
   */
  public function getSourceEntityTypes(YamlFormInterface $yamlform);

  /**
   * Get customized submission columns used to display custom table.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   A user account.
   *
   * @return array|mixed
   *   An associative array of columns keyed by name.
   */
  public function getCustomColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE);

  /**
   * Get default submission columns used to display results.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   A user account.
   *
   * @return array|mixed
   *   An associative array of columns keyed by name.
   */
  public function getDefaultColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE);

  /**
   * Get submission columns used to display results table.
   *
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission source entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   A user account.
   *
   * @return array|mixed
   *   An associative array of columns keyed by name.
   */
  public function getColumns(YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL, AccountInterface $account = NULL, $include_elements = TRUE);

  /**
   * Get customize setting.
   *
   * @param string $name
   *   Custom settings name.
   * @param mixed $default
   *   Custom settings default value.
   * @param \Drupal\yamlform\YamlFormInterface|null $yamlform
   *   A YAML form.
   * @param \Drupal\Core\Entity\EntityInterface|null $source_entity
   *   A YAML form submission source entity.
   *
   * @return mixed
   *   Custom setting.
   */
  public function getCustomSetting($name, $default, YamlFormInterface $yamlform = NULL, EntityInterface $source_entity = NULL);

  /**
   * Invoke a YAML form submission's form's handlers method.
   *
   * @param string $method
   *   The YAML form handler method to be invoked.
   * @param \Drupal\yamlform\YamlFormSubmissionInterface $yamlform_submission
   *   A YAML form submission.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference.
   */
  public function invokeYamlFormHandlers($method, YamlFormSubmissionInterface $yamlform_submission, &$context1 = NULL, &$context2 = NULL);

  /**
   * Invoke a YAML form submission's form's elements method.
   *
   * @param string $method
   *   The YAML form element method to be invoked.
   * @param \Drupal\yamlform\YamlFormSubmissionInterface $yamlform_submission
   *   A YAML form submission.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference.
   */
  public function invokeYamlFormElements($method, YamlFormSubmissionInterface $yamlform_submission, &$context1 = NULL, &$context2 = NULL);

}
