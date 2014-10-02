/* 
* Plugin Name: WP Review
* Plugin URI: http://mythemeshop.com/plugins/wp-review/
*/

jQuery(document).ready(function($){
	$('.review-total-star.allowed-to-rate.has-not-rated-yet a').hover(function(){
		$(this).addClass( "hovered" ).prevAll().addClass( "hovered" );
		$('#mts-review-user-rate').val($(this).attr('data-input-value'));
	},
	function(){
		$(this).removeClass( "hovered" ).prevAll().removeClass( "hovered" );
		$('#mts-review-user-rate').val('');
	});
	
	$('.review-total-star.allowed-to-rate.has-not-rated-yet a').on('click', function(){
		$('.review-total-star.allowed-to-rate .review-result-wrapper').hide();
		$('.mts-review-wait-msg').show();
		var blogID = $('#blog_id').val();
		var token = $('#token').val();
		var post_id = $('#post_id').val();
		var user_id = $('#user_id').val();		
		var review = $(this).attr('data-input-value');
		$.ajax ({
			data: {action: 'mts_review_get_review', post_id: post_id, user_id: user_id, nonce: token, review: review},
			type: 'post',
			url: ajaxurl,
			success: function( response ){								
				if( response != 'MTS_REVIEW_DB_ERROR' ){						
					response = response.split('|');
					$('#mts-user-reviews-total').html(response[0]);
					$('#mts-user-reviews-counter').html(response[1]);					
					$('.mts-review-wait-msg').hide();
					$('.review-total-star.allowed-to-rate .review-result-wrapper').show();					
					$('.review-total-star.allowed-to-rate').removeClass('has-not-rated-yet');
					$('.review-total-star.allowed-to-rate a, .review-total-star.allowed-to-rate a').off();
					$('.review-total-wrapper span.review-total-box.hidden').removeClass('hidden').show();
					var starsWidth = response[0] *20;
					$('.user-review-area .review-result').css('width', starsWidth+'%');

					$('.wp-review-comment-field.allowed-to-rate').removeClass('allowed-to-rate').find('.review-result').css('width', starsWidth+'%');
				}				
			}
		});
		
	});
	
	
});