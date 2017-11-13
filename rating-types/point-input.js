jQuery(document).ready(function($) {
	$('.wp-review-user-rating-point, .wp-review-comment-rating-point').each(function(index, el) {
		var $rating_wrapper = $(this);
		
		$rating_wrapper.find('.review-result').each(function() {
			var $this = $(this);
			$this.closest('.review-result-wrapper').data('originalwidth', $this[0].style.width);
		});

		$rating_wrapper.find('.review-result-wrapper').click(function(e) {
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();

			// snap to nearest 5
			var width_snapped = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if (width_snapped == 0)
				width_snapped = 5;

			$this.find('.review-result').width(width_snapped + '%');
			$this.data('originalrating', ( width_snapped / 10 ) );
			$this.data('originalwidth', $this.find('.review-result')[0].style.width);

			$rating_wrapper.addClass('wp-review-input-set');
			
			// set input value
			$rating_wrapper.find('.wp-review-user-rating-val').val( width_snapped / 10 );

			wp_review_rate( $rating_wrapper );
		}).on('mouseenter mousemove', function(e) {
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();

			// snap to nearest 0.5
			var width_snapped = Math.round(width / 5) * 5;
			
			// no 0-star ratings allowed
			if (width_snapped == 0)
				width_snapped = 5;

			$this.find('.review-result').width(width + '%');

			$rating_wrapper.find('.wp-review-your-rating').css('left', width + '%').find('.wp-review-your-rating-value').css('left', width_snapped + '%').text(''+(width_snapped/10)+'/10');

		}).on('mouseleave', function(e){
			var $this = $(this);
			var originalwidth = $this.data('originalwidth');
			$this.find('.review-result').width(originalwidth);
			$rating_wrapper.find('.wp-review-your-rating').css('left', originalwidth);
			$rating_wrapper.find('.wp-review-your-rating-value').text($this.data('originalrating')+'/10');
		});
	});
});