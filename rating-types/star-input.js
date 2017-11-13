jQuery(document).ready(function($) {
	$('.wp-review-user-rating-star, .wp-review-comment-rating-star').each(function(index, el) {
		var $rating_wrapper = $(this);
		
		$rating_wrapper.find('.review-result-wrapper > span').click(function(event) {
			var stars = $(this).data('input-value');
			$rating_wrapper.find('.review-result').css('width', '' + ( 20 * stars ) + '%');
			$rating_wrapper.find('.wp-review-user-rating-val').val( stars );

			wp_review_rate( $rating_wrapper );
		});
	});
});