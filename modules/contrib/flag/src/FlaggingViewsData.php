<?php

namespace Drupal\flag;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the flagging entity type.
 */
class FlaggingViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Remove the 'delete flagging' link that Views provides.
    unset($data['delete_flagging']);

    // Flag link.
    $data['flagging']['link_flag'] = [
      'field' => [
        'title' => t('Flag link'),
        'help' => t('Display flag/unflag link.'),
        'id' => 'flag_link',
      ],
    ];

    // Specialized is null/is not null field.
    $data['flagging']['flagged'] = [
      'title' => t('Flagged'),
      'real field' => 'uid',
      'field' => [
        'id' => 'flag_flagged',
        'label' => t('Flagged'),
        'help' => t('A boolean field to show whether the flag is set or not.'),
      ],
      'filter' => [
        'id' => 'flag_filter',
        'label' => t('Flagged'),
        'help' => t('Filter to ensure content has or has not been flagged.'),
      ],
      'sort' => [
        'id' => 'flag_sort',
        'label' => t('Flagged'),
        'help' => t('Sort by whether entities have or have not been flagged.'),
      ],
    ];

    return $data;
  }

}
