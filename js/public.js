(function ($) {
	"use strict";
	$(function () {

		/**
		 * If prevent caching is on we update all likes number on page load
		 */
		$(document).ready(function(){
			if ( locals.load_likes_with_ajax == true ) {
				$('.pixlikes-box').each( function(){
					var likebox = this,
						post_id = $(likebox).data('id');

					// reload likes number
					jQuery.ajax({
						type: "post",url: locals.ajax_url,data: { action: 'pixlikes', _ajax_nonce: locals.ajax_nounce, type: 'get', post_id: post_id},
						//beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
						//complete: function() { jQuery("#loading").hide("fast");}, //stop showing loading when the process is complete
						success: function( response ){
							var result = JSON.parse(response);
							if ( result.success ) {
								$(likebox).find('.likes-count').text(result.likes_number);
							}
						}
					});
				});
			}
		});

		$.support.touch = 'ontouchend' in document;

		var touch = $.support.touch ? true : false;

		if ( locals.like_on_action == 'click' || touch) {
			/**
			 * On each click check if the user can like
			 */
			$(document).on('click', '.pixlikes-box.likeable .like-link', function(e){

				e.preventDefault();
				var likebox = $(this).parent('.pixlikes-box');
				likebox.addClass('animate');
				like_this( likebox );
			});

		} else if ( locals.like_on_action == 'hover' ) {
			var delay_timer;

			$(document).on('mouseenter', '.pixlikes-box.likeable .like-link', function(){
				var likebox = $(this).parent('.pixlikes-box');

				var $iElem = $(likebox).find('i');
				likebox.addClass('animate');

				delay_timer = setTimeout(function() {
					like_this( likebox );
				}, locals.hover_time);

			}).on('mouseleave', '.pixlikes-box.likeable .like-link', function(){

					clearTimeout(delay_timer);

					var likebox = $(this).parent('.pixlikes-box');
					var $iElem = $(likebox).find('i');
					likebox.removeClass('animate')
				});
		}


		var like_this = function( likebox ){
			var post_id = $(likebox).data('id');
			// if there is no post to like or the user already voted we should return
			if ( typeof post_id === 'undefined' || ( !locals.free_votes && getCookie("pixlikes_"+post_id) ) ) return;
			$(likebox).addClass('complete liked').removeClass('likeable animate');
			jQuery.ajax({
				type: "post",url: locals.ajax_url,data: { action: 'pixlikes', _ajax_nonce: locals.ajax_nounce, type: 'increment', post_id: post_id},
				//beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
//				complete: function() {}, //stop showing loading when the process is complete
				success: function( response ){ //so, if data is retrieved, store it in result
					var result = JSON.parse(response);
					if ( result.success ) {
						$(likebox).find('.likes-count').text( result.likes_number );
						$(likebox).trigger('like_succeed', result.msg);
					}
				}
			});

			setTimeout(function() {
				$(likebox).removeClass('complete');
			}, 2000);

		};

		/**
		 * Utility functions
		 */
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