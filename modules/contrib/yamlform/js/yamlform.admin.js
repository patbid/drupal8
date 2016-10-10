/**
 * @file
 * Javascript behaviors for YAML form admin.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Automatically submit YAML form filter form autocomplete match.
   */
  Drupal.behaviors.yamlFormFilterAutocomplete = {
    attach: function (context) {
      $('.yamlform-filter-form input.form-autocomplete', context).once()
        .each(function () {
          // If input value is an autocomplete match, reset
          // the input to its default value.
          if (/\(([^)]+)\)$/.test(this.value)) {
            this.value = this.defaultValue;
          }

          // From: http://stackoverflow.com/questions/5366068/jquery-ui-autocomplete-submit-onclick-result
          $(this).bind('autocompleteselect', function (event, ui) {
            if(ui.item){
              $(this).val(ui.item.value);
              this.form.submit();
            }
          });
        });
    }
  };

  /**
   * Allow table rows to be hyperlinked.
   */
  Drupal.behaviors.yamlFormTableRowHref = {
    attach: function (context) {
      // Only attache the click event handler to the entire table and determine
      // which row triggers the event.
      $('.yamlform-results__table', context).once().click(function (event) {
        if (event.target.tagName == 'A' || event.target.tagName == 'BUTTON') {
          return true;
        }

        var $tr = $(event.target).parents('tr[data-yamlform-href]');
        if (!$tr.length) {
          return true;
        }

        window.location = $tr.attr('data-yamlform-href');
        return false;
      });
    }
  };

})(jQuery, Drupal);
