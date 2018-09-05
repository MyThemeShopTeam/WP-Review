( function( $ ) {
	"use strict";

	$.fn.wprUserPointRating = function( options ) {
		var $wrapper, defaults;
		$wrapper = this;
		defaults = {
			rate: null
		};
		options = $.extend( {}, defaults, options );

		$wrapper.find('.review-result').each(function() {
			var $this = $(this);
			$this.closest('.review-result-wrapper').data('originalwidth', $this[0].style.width);
		});

		$wrapper.find('.review-result-wrapper').click(function(e) {
			var $this = $(this), value;
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();
			if ( $('body').hasClass('rtl') ) {
				width = ( 100 - ( ( ( e.pageX - offset ) / $this.width() ) * 100 ) ).toFixed();
				if ( width > 100 ) {
					width = 100;
				}
			}

			// snap to nearest 5
			var width_snapped = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if (width_snapped == 0)
				width_snapped = 5;

			value = width_snapped / 10;
			$this.find('.review-result').width(width_snapped + '%');
			$this.data('originalrating', ( value ) );
			$this.data('originalwidth', $this.find('.review-result')[0].style.width);

			$wrapper.addClass('wp-review-input-set');

			// set input value
			$wrapper.find('.wp-review-user-rating-val').val( value );

			if ( typeof options.rate == 'function' ) {
				options.rate.call( $wrapper, value );
			}
			// wp_review_rate( $wrapper );
		}).on('mouseenter mousemove', function(e) {
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();
			if ( $('body').hasClass('rtl') ) {
				width = ( 100 - ( ( ( e.pageX - offset ) / $this.width() ) * 100 ) ).toFixed();
				if ( width > 100 ) {
					width = 100;
				}
			}

			// snap to nearest 0.5
			var width_snapped = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if (width_snapped == 0)
				width_snapped = 5;

			$this.find('.review-result').width(width + '%');

			if ( $('body').hasClass('rtl') ) {
				$wrapper.find('.wp-review-your-rating').css('right', width + '%').find('.wp-review-your-rating-value').css('right', width_snapped + '%').text(''+(width_snapped/10)+'/10');
			} else {
				$wrapper.find('.wp-review-your-rating').css('left', width + '%').find('.wp-review-your-rating-value').css('left', width_snapped + '%').text(''+(width_snapped/10)+'/10');
			}
		}).on('mouseleave', function(e){
			var $this = $(this);
			var originalwidth = $this.data('originalwidth');
			$this.find('.review-result').width(originalwidth);
			if ( $('body').hasClass('rtl') ) {
				$wrapper.find('.wp-review-your-rating').css('right', originalwidth);
			} else {
				$wrapper.find('.wp-review-your-rating').css('left', originalwidth);
			}
			$wrapper.find('.wp-review-your-rating-value').text($this.data('originalrating')+'/10');
		});
	};

	$( document ).ready( function() {
		$('.wp-review-user-rating-point, .wp-review-comment-rating-point').each(function(index, el) {
			$( this ).wprUserPointRating({
				rate: function( value ) {
					// console.log( this, value );
					wp_review_rate( this );
				}
			})
		});

		$( '.wpr-user-features-rating[data-type="point"]' ).each( function() {
			var $wrapper = $( this ),
				rating = {},
				$accept = $wrapper.find( '.wpr-rating-accept-btn' );

			$wrapper.find( '.wp-review-user-feature-rating-point' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserPointRating({
					rate: function( value ) {
						$accept.show(); // Show accept button.
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-point:not([data-rated])' ).length ) {
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

		$( '.wpr-comment-features-rating[data-type="point"]' ).each( function() {
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

			$wrapper.find( '.wp-review-user-feature-rating-point' ).each( function() {
				var featureId = $( this ).attr( 'data-feature-id' );
				$( this ).wprUserPointRating({
					rate: function( value ) {
						var commentForm = this.closest( 'form' );
						commentForm.addClass( 'wpr-uncompleted-rating' );
						this.attr( 'data-rated', '' );
						rating[ featureId ] = value;
						$( '#commentform :submit' ).prop( 'disabled', true );

						// If all features are rated.
						if ( ! $wrapper.find( '.wp-review-user-feature-rating-point:not([data-rated])' ).length ) {
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
