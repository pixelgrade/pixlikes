(function ($) {
	"use strict";
	$(function () {

		/* Ensure groups visibility */
		$('.switch input[type=checkbox]').each(function(){

			if ( $(this).data('show_group') ) {

				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}

				toggleGroup( $(this).data('show_group'), show);
			}
		});

		$('.switch ').on('change', 'input[type=checkbox]', function(){
			if ( $(this).data('show_group') ) {
				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}
				toggleGroup( $(this).data('show_group'), show);
			}
		});
	});


	var toggleGroup = function( name, show ){
		var $group = $( '#' + name );

		if ( show ) {
			$group.show();
		} else {
			$group.hide();
		}
	};

	/*
	 * Usefull functions
	 */

	function check_checkbox_checked( input ){ // yes the name is an ironic
		if ( $(input).attr('checked') === 'checked' ) {
			$(input).siblings('input:hidden').val('on');
		} else {
			$(input).siblings('input:hidden').val('off');
		}
	} /* End check_checkbox_checked() */

	$.fn.check_for_extended_options = function() {
		var extended_options = $(this).siblings('fieldset.group');
		if ( $(this).data('show-next') ) {
			if ( extended_options.data('extended') === true) {
				extended_options
					.data('extended', false)
					.css('height', '0');
			} else if ( (typeof extended_options.data('extended') === 'undefined' && $(this).attr('checked') === 'checked' ) || extended_options.data('extended') === false ) {
				extended_options
					.data('extended', true)
					.css('height', 'auto');
			}
		}
	};

}(jQuery));