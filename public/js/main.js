/*
* Plugin Name: WP Review
* Plugin URI: http://mythemeshop.com/plugins/wp-review/
* Description: Create reviews! Choose from Stars, Percentages, Circles or Points for review scores. Supports Retina Display, WPMU & Unlimited Color Schemes.
* Author: MyThemesShop
* Author URI: http://mythemeshop.com/
*/
( function( $ ) {
	"use strict";

	$( document ).ready( function() {
		$( '[data-wp-review-tabs] .tab-title:first-child' ).addClass( 'active' );
		$( '[data-wp-review-tabs] .tab-content:first-of-type' ).fadeIn();

		$( '[data-wp-review-tabs] .tab-title button' ).on( 'click', function( ev ) {
			ev.preventDefault();
			var $btn, $tabs, href;
			$btn = $( this );
			$tabs = $btn.closest( '[data-wp-review-tabs]' );
			href = $btn.attr( 'data-href' );
			$tabs.find( '.tab-title' ).removeClass( 'active' );
			$btn.closest( '.tab-title' ).addClass( 'active' );
			$tabs.find( '.tab-content' ).hide();
			$tabs.find( href ).fadeIn();
		});

		// Show login required message.
		$( document ).on( 'click', '.wpr-login-required', function( ev ) {
			ev.preventDefault();

			var $this = $( this ),
				$message = $this.children( '.wpr-login-required-message' );

			if ( ! $message.length ) {
				$message = $( '<div class="wpr-login-required-message">' + wpreview.loginRequiredMessage + '</div>' );
				$this.append( $message );
			}

			$message.show();

			return false;
		});
	});
})( jQuery );

function wp_review_rate( $elem ) {// rating, postId, nonce ) {
	var rating = $elem.find('.wp-review-user-rating-val').val();
	var postId = $elem.find('.wp-review-user-rating-postid').val();
	var token = $elem.find('.wp-review-user-rating-nonce').val();
	var $target = $elem;

	if ( ! $target.is('.wp-review-user-rating') )
		$target = $elem.closest('.wp-review-user-rating');

	if ( rating == 0 ) {
		return '';
	}

	jQuery.ajax ({
		beforeSend: function() {
			$target.addClass('wp-review-loading');
		},
		data: { action: 'wp_review_rate', post_id: postId, nonce: token, review: rating },
		type: 'post',
		dataType: 'json',
		url: wpreview.ajaxurl,
		success: function( response ){
			$target.removeClass('wp-review-loading');
			if (typeof response.html !== 'undefined' && response.html != '') {
				$target.empty().append(response.html).addClass('has-rated').removeClass('wp-review-user-rating');
			}

			// update text total
			if (typeof response.rating_total !== 'undefined' && response.rating_total != '') {
				$target.parent().find('.wp-review-user-rating-total').text(response.rating_total);
			}
			// update rating count
			if (typeof response.rating_count !== 'undefined' && response.rating_count != '') {
				$target.parent().find('.wp-review-user-rating-counter').text(response.rating_count);
			}
			if ( response.awaiting_moderation != undefined ) {
				$target.parent().find('.user-total-wrapper .awaiting-response-wrapper').text(response.awaiting_moderation);
  			}

  			// Update cookie.
			Cookies.set( 'wpr_visitor_has_reviewed_post_' + postId, 1, {
				expires: 315360000000 // 10 years.
			});
		}
	});
}
