( function( $, wpreview ) {
	"use strict";

	$.fn.wprUserStarRating = function( options ) {
		var $wrapper, defaults;
		$wrapper = this;
		defaults = {
			rate: null
		};
		options = $.extend( {}, defaults, options );

		$wrapper.find( '.review-result-wrapper > span' ).click( function( event ) {
			var stars = $( this ).data( 'input-value' );
			$wrapper.find( '.review-result').css( 'width', '' + ( 20 * stars ) + '%');
			$wrapper.find( '.wp-review-user-rating-val' ).val( stars );
			if ( typeof options.rate == 'function' ) {
				options.rate.call( $wrapper, stars );
			}
		});
	};

	$( document ).ready( function() {
		$( '.wp-review-user-rating-star, .wp-review-comment-rating-star' ).each( function() {
			$( this ).wprUserStarRating({
				rate: function( value ) {
					wp_review_rate( this );
				}
			})
		});
	});
})( jQuery, wpreview );
