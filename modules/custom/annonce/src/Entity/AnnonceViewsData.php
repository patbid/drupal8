<?php

namespace Drupal\annonce\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the Annonce entity type.
 */
class AnnonceViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['annonce']['table']['base'] = array(
      'field' => 'id',
      'title' => t('Annonce'),
      'help' => t('The annonce entity ID.'),
    );

//      $schema['annonce_history'] = array(
//          'description' => 'annonce views',
//          'fields' => array(
//              'vid' => array(
//                  'description' => 'Primary Key: Unique view ID.',
//                  'type' => 'serial',
//                  'unsigned' => TRUE,
//                  'not null' => TRUE,
//              ),
//              'aid' => array(
//                  'description' => 'annonce ID.',
//                  'type' => 'int',
//                  'unsigned' => TRUE,
//                  'not null' => TRUE,
//              ),
//              'uid' => array(
//                  'description' => 'User ID.',
//                  'type' => 'int',
//                  'unsigned' => TRUE,
//                  'not null' => TRUE,
//              ),
//              'update_time' => array(
//                  'description' => 'Timestamp of view.',
//                  'type' => 'int',
//                  'unsigned' => TRUE,
//                  'not null' => TRUE,
//              ),
//          ),

          // The outermost keys of $data are Views table names, which should usually
      // be the same as the hook_schema() table names.
      $data['annonce_history'] = array();

      // The value corresponding to key 'table' gives properties of the table
      // itself.
      $data['annonce_history']['table'] = array();

      // Within 'table', the value of 'group' (translated string) is used as a
      // prefix in Views UI for this table's fields, filters, etc. When adding
      // a field, filter, etc. you can also filter by the group.
      $data['annonce_history']['table']['group'] = t('annonce groupe');

      // Within 'table', the value of 'provider' is the module that provides schema
      // or the entity type that causes the table to exist. Setting this ensures
      // that views have the correct dependencies. This is automatically set to the
      // module that implements hook_views_data().
//      $data['annonce_history']['table']['provider'] = 'annonce';

      // Some tables are "base" tables, meaning that they can be the base tables
      // for views. Non-base tables can only be brought in via relationships in
      // views based on other tables. To define a table to be a base table, add
      // key 'base' to the 'table' array:
      $data['annonce_history']['table']['base'] = array(
          // Identifier (primary) field in this table for Views.
          'field' => 'vid',
          // Label in the UI.
          'title' => t('annonce history'),
          // Longer description in the UI. Required.
          'help' => t('Show annonces viewed by users'),
          'weight' => -10,
      );
      $data['annonce_history']['table']['join']['annonce'] = array(
          // Within the 'join' section, list one or more tables to automatically
          // join to. In this example, every time 'node_field_data' is available in
          // a view, 'example_table' will be too. The array keys here are the array
          // keys for the other tables, given in their hook_views_data()
          // implementations. If the table listed here is from another module's
          // hook_views_data() implementation, make sure your module depends on that
          // other module.
              // Primary key field in node_field_data to use in the join.
              'left_field' => 'id',
              // Foreign key field in example_table to use in the join.
              'field' => 'aid',
      );


      $data['annonce_history']['table']['join']['users_field_data'] = array(
          // 'node_field_data' above is the base we're joining to in Views.
          // 'left_table' is the table we're actually joining to, in order to get to
          // 'node_field_data'. It has to be something that Views knows how to join
          // to 'node_field_data'.
           'left_field' => 'uid',
          'field' => 'uid',
      );

      $data['annonce_history']['aid'] = array(
          'title' => t('annonce'),
          'help' => t('annonce id'),
          'field' => array(
              // ID of field handler plugin to use.
              'id' => 'numeric',
          ),
          // Define a relationship to the node_field_data table, so views whose
          // base table is example_table can add a relationship to nodes. To make a
          // relationship in the other direction, you can:
          // - Use hook_views_data_alter() -- see the function body example on that
          //   hook for details.
          // - Use the implicit join method described above.
          'relationship' => array(
              // Views name of the table to join to for the relationship.
              'base' => 'annonce',
              // Database field name in the other table to join on.
              'base field' => 'id',
              // ID of relationship handler plugin to use.
              'id' => 'standard',
              // Default label for relationship in the UI.
              'label' => t('Annonce vue'),
          ),
       );

      $data['annonce_history']['uid'] = array(
          'title' => t('User'),
          'help' => t('user id'),
          'field' => array(
              // ID of field handler plugin to use.
              'id' => 'numeric',
          ),
          // Define a relationship to the node_field_data table, so views whose
          // base table is example_table can add a relationship to nodes. To make a
          // relationship in the other direction, you can:
          // - Use hook_views_data_alter() -- see the function body example on that
          //   hook for details.
          // - Use the implicit join method described above.
          'relationship' => array(
              // Views name of the table to join to for the relationship.
              'base' => 'users_field_data',
              // Database field name in the other table to join on.
              'base field' => 'uid',
              // ID of relationship handler plugin to use.
              'id' => 'standard',
              // Default label for relationship in the UI.
              'label' => t('user vue'),
          ),
      );


      $data['annonce_history']['update_time'] = array(
          'title' => t('date de visualisation'),
          'help' => t('date de visualisation'),
          'field' => array(
              // ID of field handler plugin to use.
              'id' => 'date',
          ),
      );

      return $data;
  }

}
