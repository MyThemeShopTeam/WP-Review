( function( $ ) {
	"use strict";

	$.fn.wprUserPercentageRating = function( options ) {
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
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();
			if ( $('body').hasClass('rtl') ) {
				width = ( 100 - ( ( ( e.pageX - offset ) / $this.width() ) * 100 ) ).toFixed();
			}


			// snap to nearest 5
			// width = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if ( width <= 0 ) {
  				width = 1;
			}
			if ( width > 100 ) {
				width = 100;
			}

			$this.find('.review-result').width(width + '%');
			$this.data('originalrating', width );
			$this.data('originalwidth', $this.find('.review-result')[0].style.width);

			$wrapper.addClass('wp-review-input-set');

			// set input value
			$wrapper.find('.wp-review-user-rating-val').val( width );

			if ( typeof options.rate == 'function' ) {
				options.rate.call( $wrapper, parseFloat( width ) );
			}
		}).on('mouseenter mousemove', function(e) {
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();
			if ( $('body').hasClass('rtl') ) {
				width = ( 100 - ( ( ( e.pageX - offset ) / $this.width() ) * 100 ) ).toFixed();
			}

			// snap to nearest 5
			// width = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if ( width <= 0 ) {
  				width = 1;
			}
			if ( width > 100 ) {
				width = 100;
			}

			$this.find('.review-result').width(width + '%');

			if ( $('body').hasClass('rtl') ) {
				$wrapper.find('.wp-review-your-rating').css('right', width + '%').find('.wp-review-your-rating-value').text(''+width+'%');
			} else {
				$wrapper.find('.wp-review-your-rating').css('left', width + '%').find('.wp-review-your-rating-value').text(''+width+'%');
			}

		}).on('mouseleave', function(e){
			var $this = $(this);
			$this.find('.review-result').width($this.data('originalwidth'));
			$wrapper.find('.wp-review-your-rating-value').text($this.data('originalrating')+'%');
		});
	};

	$( document ).ready( function() {
		$('.wp-review-user-rating-percentage, .wp-review-comment-rating-percentage').each( function() {
			$( this ).wprUserPercentageRating({
				rate: function( value ) {
					// console.log( this, value );
					wp_review_rate( this );
				}
			})
		});
	});
})( jQuery );
