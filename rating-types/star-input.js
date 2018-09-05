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

		$( '.wpr-user-features-rating[data-type="star"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( '.wp-review-user-feature-rating-star' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserStarRating({
					rate: function( value ) {
						$accept.show(); // Show accept button.
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;
						$( '#commentform :submit' ).prop( 'disabled', true );

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-star:not([data-rated])' ).length ) {
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

		$( '.wpr-comment-features-rating[data-type="star"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( 'input[name="wp-review-user-rating-val"]' ).remove();
			$wrapper.find( 'input[name="wp-review-comment-feature-rating"]' ).remove();

			function submit() {
				var rating = $wrapper.data( 'rating' ),
					total = 0,
					count = 0;
				for ( var i in rating ) {
					total += parseFloat( rating[ i ] );
					count++;
				}
				$wrapper.find( 'input[name="wp-review-user-rating-val"]' ).remove();
				$wrapper.find( 'input[name="wp-review-comment-feature-rating"]' ).remove();
				$( '<input type="hidden" name="wp-review-user-rating-val">' ).val( total / count ).appendTo( $wrapper );
				$( '<input type="hidden" name="wp-review-comment-feature-rating">' ).val( JSON.stringify( rating ) ).appendTo( $wrapper );
			}

			$wrapper.find( '.wp-review-user-feature-rating-star' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserStarRating({
					rate: function( value ) {
						var commentForm = this.closest( 'form' );
						commentForm.addClass( 'wpr-uncompleted-rating' );
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-star:not([data-rated])' ).length ) {
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
