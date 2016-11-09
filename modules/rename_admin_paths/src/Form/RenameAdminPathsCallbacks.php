<?php
/**
* @file
* Contains \Drupal\rename_admin_paths\RenameAdminPathsCallbacks.
*/

namespace Drupal\rename_admin_paths\Form;

/**
 * RenameAdminPathsCallbacks class
 */
class RenameAdminPathsCallbacks {

  /**
   * Form element validation handler for 'name' in form_test_validate_form().
   */
  public function validatePath(&$element, &$form_state) {
    // Force path replacement values to contain only lowercase letters, numbers, and underscores.
    if (!empty($element['#value']) && !preg_match('!^[a-z0-9_]+$!i', $element['#value'])) {
      form_error($element, $form_state, t('Path replacement value must contain only lowercase letters, numbers, and underscores.'));
    }
  }

}
