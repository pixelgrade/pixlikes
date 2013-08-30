(function ($) {
	"use strict";
	$(function () {

		/**
		 * If prevent caching is on, we update all likes number on page load
		 */

		if ( locals.load_likes_with_ajax ) {
			$('.pixlikes-box').each( function(){
				var likebox = this,
					post_id = $(likebox).data('id');
				// reload likes number
				ajax_pixlikes('get', post_id, function( response ){
					var result = JSON.parse(response);
					if ( response.success ) {
						$(likebox).text(response.likes_number);
					}
				});
			});
		}
		/**
		 * On each click check if the user can like
		 */

		$('.pixlikes-box').on('click', function(e){
			e.preventDefault();
			var likebox = this,
				post_id = $(likebox).data('id');

			// if there is no post to like or the user already voted we should return
			if ( typeof post_id === 'undefined' || !getCookie("pixlikes_"+post_id) ) return;

			ajax_pixlikes('increment', post_id, function( response ){ //so, if data is retrieved, store it in result

				var result = JSON.parse(response);

				if ( response.success ) {
					$(likebox).text(response.likes_number);
					$(likebox).trigger('like_succeed', response.msg);
				} /*else {
				 $(likebox).trigger('cannot_like');
				 }*/
			});
		});

		// exemple to do something when the like action succeed
		$('.pixlikes-box').on('like_succeed', function(e, msg){
			$('.site-branding').fadeOut(300);
			$('.site-branding').fadeIn(300);
			$('.site-branding').fadeOut(300);
			$('.site-branding').fadeIn(300);
			$('.site-branding').fadeOut(300);
			$('.site-branding').fadeIn(300);
		});

		// play with javascript when the user cannot like again
//		$('.pixlikes-box').on('cannot_like', function(e, msg){
//			$('.project-title').fadeOut(300);
//			$('.project-title').fadeIn(300);
//			$('.project-title').fadeOut(300);
//			$('.project-title').fadeIn(300);
//			$('.project-title').fadeOut(300);
//			$('.project-title').fadeIn(300);
//		});

		/**
		 * Utility functions
		 */

		function ajax_pixlikes( type, post_id, on_success ){

			if (on_success && typeof(on_success) === "function") {

				jQuery.ajax({
					type: "post",url: locals.ajax_url,data: { action: 'pixlikes', _ajax_nonce: locals.ajax_nounce, type: type, post_id: post_id},
	//				beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
	//				complete: function() { jQuery("#loading").hide("fast");}, //stop showing loading when the process is complete
					success: on_success()
				}); //close jQuery.ajax(

			}
		}

		function getCookie(c_name)
		{
			var c_value = document.cookie;
			var c_start = c_value.indexOf(" " + c_name + "=");
			if (c_start == -1)
			{
				c_start = c_value.indexOf(c_name + "=");
			}
			if (c_start == -1)
			{
				c_value = null;
			}
			else
			{
				c_start = c_value.indexOf("=", c_start) + 1;
				var c_end = c_value.indexOf(";", c_start);
				if (c_end == -1)
				{
					c_end = c_value.length;
				}
				c_value = unescape(c_value.substring(c_start,c_end));
			}
			return c_value;
		}

	});
}(jQuery));