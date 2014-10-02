/* 
* Plugin Name: WP Review
* Plugin URI: http://mythemeshop.com/plugins/wp-review/
*/

jQuery(document).ready(function($) {

	/**
	 * Repeatable field
	 */
	$('#add-row').on('click', function(e) {
		e.preventDefault();
		var row = $('.empty-row.screen-reader-text').clone(true);
		var input = row.find('input');
		if (typeof input.data('name') !== 'undefined' && input.data('name')) input.prop('name', input.data('name'));
		row.removeClass('empty-row screen-reader-text');
		row.insertBefore('#wp-review-item tbody>tr:last');
		row.find("[name='wp_review_item_star[]']").addClass('review-star');
		$.review_total();
	});

	$('.remove-row').on('click', function(e) {
		e.preventDefault();
		$(this).parents('tr').remove();
		$.review_total();
	});

	/**
	 * Toggle meta box
	 */
	$('#wp-review-metabox-item').hide();
	$('#wp-review-metabox-heading').hide();
	$('#wp-review-metabox-desc').hide();
	$('#wp-review-metabox-userReview').hide();
    $('#wp_review_shortcode_hint_field').hide();
    $('#wp_review_id_hint').hide();
    
	$('#wp_review_type').on( 'change', function() {

		$('#wp-review-metabox-item').toggle( $(this).val() != '' );
		$('#wp-review-metabox-heading').toggle( $(this).val() != '' );
		$('#wp-review-metabox-desc').toggle( $(this).val() != '' );
		$('#wp-review-metabox-userReview').toggle( $(this).val() != '' );	
        $('#wp_review_id_hint').toggle( $(this).val() != '' );

		if ( $(this).val() == 'point' ) {
			$('.dynamic-text').text('Points (1-10)');
		}

		else if ( $(this).val() == 'star' ) {
			$('.dynamic-text').text('Star (1-5)');
		}

		else if ( $(this).val() == 'percentage' ) {
			$('.dynamic-text').text('Percentage (1-100)');
		}
		
		$.validate_review_value();

	});

	$('#wp_review_custom_colors').change(function(e) {
		if ( $(this).is(':checked') ) {
			$('.wp-review-color-options').show();
		} else {
			$('.wp-review-color-options').hide();
		}
	});
	$('#wp_review_custom_location').change(function(e) {
		if ( $(this).is(':checked') ) {
			$('.wp-review-location-options').show();
		} else {
			$('.wp-review-location-options').hide();
		}
	});
	$('#wp_review_custom_width').change(function(e) {
		if ( $(this).is(':checked') ) {
			$('.wp-review-width-options').show();
		} else {
			$('.wp-review-width-options').hide();
		}
	});

	$('#wp-wp_review_desc-wrap').toggle(! $('#wp_review_hide_desc').is(':checked'));
	$('#wp_review_hide_desc').change(function() {
		$('#wp-wp_review_desc-wrap, #wp-review-desc-title').toggle(!$(this).is(':checked'));
	});

	if ( $('#wp_review_type option:selected').val() === 'star' ) {
		$('#wp-review-metabox-item').show();
		$('#wp-review-metabox-heading').show();
		$('#wp-review-metabox-desc').show();
		$('#wp-review-metabox-userReview').show();
	}

	if ( $('#wp_review_type option:selected').val() === 'point' ) {
		$('.dynamic-text').text('Points (1-10)');
		$('#wp-review-metabox-item').show();
		$('#wp-review-metabox-heading').show();
		$('#wp-review-metabox-desc').show();
		$('#wp-review-metabox-userReview').show();
	}

	if ( $('#wp_review_type option:selected').val() === 'percentage' ) {
		$('.dynamic-text').text('Percentage (1-100)');
		$('#wp-review-metabox-item').show();
		$('#wp-review-metabox-heading').show();
		$('#wp-review-metabox-desc').show();
		$('#wp-review-metabox-userReview').show();
	}
    
    if ( $('#wp_review_type option:selected').val() != '' ) {
        $('#wp_review_id_hint').show();
    }
    
    $('#wp_review_location').on('change', function() {
        $('#wp_review_shortcode_hint_field').toggle($(this).val() == 'custom');
    });
    $('#wp_review_shortcode_hint').click(function() {
        $(this).select();
    });
    if ($('#wp_review_location').val() == 'custom') {
        $('#wp_review_shortcode_hint_field').show();
    }

	/**
	 * Review total
	 */
	$.extend({
		
		review_total: function(){
			$('.review-star').on( 'change', function () {
				
				var sum   = 0,
					value = 0,
					input = $('.review-star').length;
					
				$('.review-star').each(function () {
					value = Number($(this).val());
					if (!isNaN(value)) sum += value / input;
				});
				
				$('.wp-review-total').val( Math.round(sum * 10) / 10 );
				
				$.validate_review_value();
				
			});
			
		},
		
		validate_review_value: function(){
			
			var type = $('#wp_review_type'),
				fields = $('input.review-star'),
				minval = 0,
				maxval = 999;
			
			if ( type.val() == 'point' ) {
				minval = 1;
				maxval = 10;
			} else if ( type.val() == 'star' ) {
				minval = 1;
				maxval = 5;
			} else if ( type.val() == 'percentage' ) {
				minval = 1;
				maxval = 100;
			}
			
			fields.each(function () {
				var value = Number($(this).val());
				if ( (value) && (value<minval || value>maxval) )
					$(this).addClass('review-value-incorrect');
				else
					$(this).removeClass('review-value-incorrect');
			});
	
		},
		
	});
	
	$.review_total();
	//$.validate_review_value();
	//$('.review-star').trigger('change');

	/**
	 * Color picker setup
	 */
	$('.wp-review-color').wpColorPicker();

	$('.wp-review-theme-defaults-msg .close-notice').click(function() {
		$('.wp-review-theme-defaults-msg').remove();
	});
	$('.wp-review-theme-defaults-msg a.button').click(function() {
		return confirm('Are you sure? This may override the current settings.');
	});

});