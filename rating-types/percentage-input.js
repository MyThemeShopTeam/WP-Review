jQuery(document).ready(function($) {
	$('.wp-review-user-rating-percentage, .wp-review-comment-rating-percentage').each(function(index, el) {
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
			// width = Math.round(width / 5) * 5;

			// no 0-star ratings allowed
			if (width == 0)
				width = 1;

			$this.find('.review-result').width(width + '%');
			$this.data('originalrating', width );
			$this.data('originalwidth', $this.find('.review-result')[0].style.width);

			$rating_wrapper.addClass('wp-review-input-set');
			
			// set input value
			$rating_wrapper.find('.wp-review-user-rating-val').val( width );
			
			wp_review_rate( $rating_wrapper );
		}).on('mouseenter mousemove', function(e) {
			var $this = $(this);
			var offset = $this.offset().left;
			var width = ( ( ( e.pageX - offset ) / $this.width() ) * 100 ).toFixed();

			// snap to nearest 5
			// width = Math.round(width / 5) * 5;
			
			// no 0-star ratings allowed
			if (width == 0)
				width = 1;

			$this.find('.review-result').width(width + '%');

			$rating_wrapper.find('.wp-review-your-rating').css('left', width + '%').find('.wp-review-your-rating-value').text(''+width+'%');

		}).on('mouseleave', function(e){
			var $this = $(this);
			$this.find('.review-result').width($this.data('originalwidth'));
			$rating_wrapper.find('.wp-review-your-rating-value').text($this.data('originalrating')+'%');
		});
	});
});