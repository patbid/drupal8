(function($, Drupal, drupalSettings) {
	$(document).ready(function() {
		$('a[href="http://"]').attr('target','_blank');
		$( ".block h2" ).click(function() {
  			$(this).siblings(' .content' ).slideToggle('slow');
  		});
	});
})(jQuery, Drupal, drupalSettings);