(function ($) {
	"use strict";
	$(function () {

		/**
		 *  Checkbox value switcher
		 *  Any checkbox should switch between value 1 and 0
		 */
		$('#pixlikes_form input:checkbox').each(function(i,e){
			check_checkbox_checked(e);
		});
		$('#pixlikes_form').on('click', 'input:checkbox', function(){
			check_checkbox_checked(this);
		});
		/** End Checkbox value switcher **/

	});

	/*
	 * Usefull functions
	 */

	function check_checkbox_checked( input ){ // yes the name is an ironic
		if ( $(input).attr('checked') === 'checked' ) {
			$(input).val('1');
		} else {
			$(input).val('0');
		}
	} /* End check_checkbox_checked() */

}(jQuery));