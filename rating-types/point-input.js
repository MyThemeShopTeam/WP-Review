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
	});
})( jQuery );
