( function( $, wpreview ) {
	"use strict";

	$.fn.wprUserThumbsRating = function( options ) {
		var $wrapper, defaults;
		$wrapper = this;
		defaults = {
			rate: null
		};
		options = $.extend( {}, defaults, options );

		$wrapper.find( '.wpr-thumbs-up-button' ).click( function( ev ) {
			ev.preventDefault();
			$wrapper.find( '.wpr-thumbs-up-icon' ).addClass( 'active' );
			$wrapper.find( '.wpr-thumbs-down-icon' ).removeClass( 'active' );
			$wrapper.find( '.wp-review-user-rating-val' ).val( 100 );
			if ( typeof options.rate == 'function' ) {
				options.rate.call( $wrapper, 100 );
			}
		});

		$wrapper.find( '.wpr-thumbs-down-button' ).click( function( ev ) {
			ev.preventDefault();
			$wrapper.find( '.wpr-thumbs-up-icon' ).removeClass( 'active' );
			$wrapper.find( '.wpr-thumbs-down-icon' ).addClass( 'active' );
			$wrapper.find( '.wp-review-user-rating-val' ).val( -1 );
			if ( typeof options.rate == 'function' ) {
				options.rate.call( $wrapper, -1 );
			}
		});
	};

	$( document ).ready( function() {
		$( '.wp-review-user-rating-thumbs, .wp-review-comment-rating-thumbs' ).each( function() {
			$( this ).wprUserThumbsRating({
				rate: function( value ) {
					wp_review_rate( this );
				}
			})
		});

		$( '.wpr-user-features-rating[data-type="thumbs"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );
				console.log( $accept );

			$wrapper.find( '.wp-review-user-feature-rating-thumbs' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserThumbsRating({
					rate: function( value ) {
						$accept.show(); // Show accept button.
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;
						$( '#commentform :submit' ).prop( 'disabled', true );

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-thumbs:not([data-rated])' ).length ) {
							$accept.prop( 'disabled', false ); // Enable accept button.
							$wrapper.data( 'rating', rating );
							$( '#commentform :submit' ).prop( 'disabled', false );
						}
					}
				});
			});

			$accept.on( 'click', function() {
				wpreview.featuresRating( $wrapper );
			});
		});

		$( '.wpr-comment-features-rating[data-type="thumbs"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( 'input[name="wp-review-user-rating-val"]' ).remove();
			$wrapper.find( 'input[name="wp-review-comment-feature-rating"]' ).remove();

			function submit() {
				var rating = $wrapper.data( 'rating' ),
					total = 0,
					count = 0,
					value = 0;
				for ( var i in rating ) {
					if ( rating[ i ] < 0 ) {
						total += parseFloat( rating[ i ] );
					}
					count++;
				}
				if ( total == 0 ) {
					total = -1;
				}
				$wrapper.find( 'input[name="wp-review-user-rating-val"]' ).remove();
				$wrapper.find( 'input[name="wp-review-comment-feature-rating"]' ).remove();
				$( '<input type="hidden" name="wp-review-user-rating-val">' ).val( total / count ).appendTo( $wrapper );
				$( '<input type="hidden" name="wp-review-comment-feature-rating">' ).val( JSON.stringify( rating ) ).appendTo( $wrapper );
			}

			$wrapper.find( '.wp-review-user-feature-rating-thumbs' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserThumbsRating({
					rate: function( value ) {
						var commentForm = this.closest( 'form' );
						commentForm.addClass( 'wpr-uncompleted-rating' );
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-thumbs:not([data-rated])' ).length ) {
							$wrapper.data( 'rating', rating );
							submit();
							commentForm.removeClass( 'wpr-uncompleted-rating' );
						}
					}
				});
			});
		});
	});
})( jQuery, wpreview );
