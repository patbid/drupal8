/**
 * @file
 * Javascript behaviors for YAML form jQuery UI tooltip integration.
 *
 * jQuery UI's tooltip implement is not very responsive or adaptive to form
 * element.
 *
 * @see https://www.drupal.org/node/2207383
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.yamlFormElementTooltip = {
    attach: function (context) {
      $(context).find('.js-yamlform-element-tooltip').once('yamlform-element-tooltip').each(function () {
        var $element = $(this);
        var $description = $element.children('.description.visually-hidden');

        $element.tooltip({
          items: ':input',
          content: $description.html()
        });
      });
    }
  };

})(jQuery, Drupal);
