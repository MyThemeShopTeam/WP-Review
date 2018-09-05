( function( $ ) {
	"use strict";

	$.fn.wprUserCircleRating = function( options ) {
		var $wrapper, defaults;
		$wrapper = this;
		defaults = {
			rate: null
		};
		options = $.extend( {}, defaults, options );

		$wrapper.find( '.wp-review-circle-rating-user' ).knob({
			release: function( value ) {
				if ( typeof options.rate == 'function' ) {
					options.rate.call( $wrapper, Math.round( value ) );
				}
				$wrapper.addClass( 'wp-review-input-set' );
			},
			change: function( value ) {
				if ( typeof options.change == 'function' ) {
					options.change.call( $wrapper, Math.round( value ) );
				}
			}
		});

		$wrapper.find( '.wp-review-circle-rating-send' ).click( function( event ) {
			event.preventDefault();
			if ( typeof options.send == 'function' ) {
				options.send.call( $wrapper );
			}
		});
	};

	$( document ).ready( function() {
		$( '.wp-review-user-rating-circle, .wp-review-comment-rating-circle' ).each( function() {
			var $wrapper = $( this ),
				$accept = $wrapper.find( '.wpr-rating-accept-btn' ),
				isVisitor = $wrapper.hasClass( 'wp-review-user-rating-circle' );

			if ( isVisitor ) {
				$wrapper.closest( '.visitors-review-area' ).after( $accept );
				$accept.on( 'click', function( ev ) {
					ev.preventDefault();
					wp_review_rate( $wrapper );
				})
			}

			$wrapper.wprUserCircleRating({
				rate: function( value ) {
					if ( isVisitor ) {
						$accept.show();
					}
				},
				send: function() {
					wp_review_rate( this );
				}
			});
		});

		$( '.wpr-user-features-rating[data-type="circle"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( '.wp-review-user-feature-rating-circle' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserCircleRating({
					rate: function( value ) {
						$accept.show(); // Show accept button.
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-circle:not([data-rated])' ).length ) {
							$accept.prop( 'disabled', false ); // Enable accept button.
							$wrapper.data( 'rating', rating );
						}
					}
				});
			});

			$accept.on( 'click', function() {
				wpreview.featuresRating( $wrapper );
			});
		});

		$( '.wpr-comment-features-rating[data-type="circle"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( 'input[name="wp-review-user-rating-val"].wp-review-circle-rating-user' ).removeAttr( 'name' );
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

			$wrapper.find( '.wp-review-user-feature-rating-circle' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserCircleRating({
					rate: function( value ) {
						var commentForm = this.closest( 'form' );
						commentForm.addClass( 'wpr-uncompleted-rating' );
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;
						$( '#commentform :submit' ).prop( 'disabled', true );

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-circle:not([data-rated])' ).length ) {
							$wrapper.data( 'rating', rating );
							$( '#commentform :submit' ).prop( 'disabled', false );
							submit();
							commentForm.removeClass( 'wpr-uncompleted-rating' );
						}
					}
				});
			});
		});
	});
})( jQuery );

