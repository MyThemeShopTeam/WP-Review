/*
* Plugin Name: WP Review
* Plugin URI: https://wordpress.org/plugins/wp-review/
* Description: Create reviews! Choose from Stars, Percentages or Points for review scores. Supports Retina Display, WPMU & Unlimited Color Schemes.
* Author: MyThemesShop
* Author URI: http://mythemeshop.com/
*/
jQuery(document).ready(function($) {
	/**
	 * Review total
	 */
	var current_rating_max = 100;
	var current_rating_decimals = 0;
	$.extend({
		
		review_total: function(){
				var sum   = 0,
					value = 0,
					input = $('.review-star').length;
					
				$('.review-star').each(function () {
					value = Number($(this).val());
					if (!isNaN(value)) sum += value / input;
				});
				
				$('.wp-review-total').val( sum.toFixed( current_rating_decimals ) );
				
				$.validate_review_value( current_rating_max );
		},
		
		validate_review_value: function( max ){
			
			var type = $('#wp_review_type'),
				fields = $('input.review-star'),
				minval = 0,
				maxval = 999;
			
			maxval = max;
			
			fields.each(function () {
				var value = Number($(this).val());
				if ( (value) && (value<minval || value>maxval) )
					$(this).addClass('review-value-incorrect');
				else
					$(this).removeClass('review-value-incorrect');
			});
	
		},
		
	});

	/**
	 * Repeatable field
	 */
	$('.add-row').on('click', function(e) {
		e.preventDefault();
		var $target = $($(this).data('target'));
		var row = $target.find('.empty-row').clone(true);
		var input = row.find('input');
		if (typeof input.data('name') !== 'undefined' && input.data('name')) input.prop('name', input.data('name'));
		input.filter('[name="wp_review_item_star[]"]').addClass('review-star');
		row.removeClass('empty-row screen-reader-text');
		row.insertBefore($target.find('tbody>tr:last'));
		row.find(".focus-on-add").focus();
		$.review_total();
	});

	$('.remove-row').on('click', function(e) {
		e.preventDefault();
		$(this).closest('tr').remove();
		$.review_total();
	});
	$('#wp-review-item').on('change', '.review-star', function(event) {
		$.review_total();
	});

	$('#wp-review-metabox-item tbody').sortable({ handle: '.handle', revert: 100, containment: '#wp-review-metabox-item' });
	$('#wp-review-metabox-reviewLinks tbody').sortable({ handle: '.handle', revert: 100, containment: '#wp-review-metabox-reviewLinks' });

	/**
	 * Toggle meta box
	 */
	$('#wp-review-metabox-item').hide();
	$('#wp-review-metabox-heading').hide();
	$('#wp-review-metabox-desc').hide();
	$('#wp-review-metabox-userReview').hide();
    $('#wp_review_shortcode_hint_field').hide();
    $('#wp_review_id_hint').hide();
    $('#wp_review_schema_group').hide();
    $('#wp_review_heading_group').hide();
    $('#wp-review-metabox-reviewLinks').hide();

    var ratings_initiated = false;
	$('#wp_review_type').on( 'change', function() {
		var selected_val = $(this).val();
		var $selected_option = $(this).find('option:selected');
		$('#wp-review-metabox-item').toggle( selected_val != '' );
		$('#wp-review-metabox-heading').toggle( selected_val != '' );
		$('#wp-review-metabox-desc').toggle( selected_val != '' );
		$('#wp-review-metabox-userReview').toggle( selected_val != '' );	
        $('#wp_review_id_hint').toggle( selected_val != '' );
        $('#wp_review_schema_group').toggle( selected_val != '' );
        $('#wp-review-metabox-reviewLinks').toggle( selected_val != '' );
        $('#wp_review_heading_group').toggle( selected_val != '' );

		// Build dynamic text
		var max = $selected_option.data('max');
		var decimals = $selected_option.data('decimals');
		var val_text = $selected_option.text() + ' (1 - ' + max + ')';
		$('.dynamic-text').text(val_text);

		current_rating_max = max;
		current_rating_decimals = decimals;
		$.validate_review_value( max );
		if (ratings_initiated) {
			$.review_total();
		}
		ratings_initiated = true;
	}).change();

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
	$('#wp_review_custom_author').change(function(e) {
		if ( $(this).is(':checked') ) {
			$('.wp-review-author-options').show();
		} else {
			$('.wp-review-author-options').hide();
		}
	});
	$('#wp_review_show_on_thumbnails').change(function(e) {
		if ( $(this).is(':checked') ) {
			$('.wp-review-thumbnail-options').show();
		} else {
			$('.wp-review-thumbnail-options').hide();
		}
	});

	$('#wp-wp_review_desc-wrap').toggle(! $('#wp_review_hide_desc').is(':checked'));
	$('#wp_review_hide_desc').change(function() {
		$('#wp-wp_review_desc-wrap, #wp-review-desc-title').toggle(!$(this).is(':checked'));
	});

    $('#wp_review_location').on('change', function() {
        $('#wp_review_shortcode_hint_field').toggle($(this).val() == 'custom');
    });
    $('#wp_review_shortcode_hint').click(function() {
        $(this).select();
    });
    if ($('#wp_review_location').val() == 'custom') {
        $('#wp_review_shortcode_hint_field').show();
    }
    $('#wp_review_width').on('change', function() {
    	var value = parseInt($(this).val());
    	if (value < 100) {
    		$('.wp-review-align-options').show();
    	} else {
    		$('.wp-review-align-options').hide();
    	}
    	$("#wp-review-width-slider").slider("value", parseInt(value));
    });
    $("#wp-review-width-slider").slider({
	    range: "min",
	    value: $('#wp_review_width').val(),
	    step: 1,
	    min: 1,
	    max: 100,
	    slide: function(event, ui) {
	        $("#wp_review_width").val(ui.value).trigger('change');
	    }
	});

	$('.wp-review-userReview-options').change(function(event) {
		$('#wp-review-through-comment-option').toggle(!!parseInt($(this).val()));
	});

	
	
	//$.review_total();
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
	/*
	var $wrapper = $('.review-result-wrapper');
	$wrapper.on('mouseenter', 'a, i', function(e){
		var $this = $(this);
		$this.closest('.review-result-wrapper').find('.review-result').width(parseInt($this.data('value'))*20 + '%');
	});
	$wrapper.on('click', 'a, i', function(e){
		var $this = $(this);
		var $wrapper = $this.closest('.review-result-wrapper');
		var val = $this.data('value');
		$wrapper.find('.review-result').data('value', val);
		$wrapper.prev().val(val);
	});
	$wrapper.on('mouseleave', function(e){
		var $result = $(this).find('.review-result');
		$result.width(parseInt($result.data('value'))*20 + '%');
	});
*/
	$('[name=wp_review_userReview]').change(function(e){
		var val = $(this).val();
		var $type = $('#wp_review_comment_rating_type');
		if(ratingPermissions.commentOnly === val || ratingPermissions.both === val){
			$type.show();
		} else {
			$type.hide();
		}
	});


	$('td.wp_review_comment_rating, #wp-review-comment-rating').each(function(){
		var $features = $(this).find('.wp-review-rating-feature');
		var maxWidth = 0;
		$features.each(function(){
			var width = $(this).outerWidth();
			if ( width > maxWidth ) maxWidth = width;
		});
		$features.width( maxWidth + 10 );
	});

	/*$('.wrap.wp-review .nav-tab-wrapper .nav-tab').click(function(event) {
		event.preventDefault();
		var $this = $(this);
		window.location.hash = $this.data('tab');
		$this.addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
		$('#wp-review-settings-tab-contents').find('.settings-tab-'+$this.data('tab')).show().siblings().hide();
	});*/
	var hash = window.location.hash.substr(1);
	if (hash == '') hash = $('#wp_review_last_tab').val();
	if ($('#wp-review-settings-tab-contents').find('.settings-tab-'+hash).length) {
		$('.wrap.wp-review .nav-tab-wrapper .nav-tab').filter('[data-tab='+hash+']').addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
		$('#wp-review-settings-tab-contents').find('.settings-tab-'+hash).show().siblings().hide();
	}
	$(window).on('hashchange', function() {
		var hash = window.location.hash.substr(1);
		if ($('#wp-review-settings-tab-contents').find('.settings-tab-'+hash).length) {
			$('.wrap.wp-review .nav-tab-wrapper .nav-tab').filter('[data-tab='+hash+']').addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
			$('#wp-review-settings-tab-contents').find('.settings-tab-'+hash).show().siblings().hide();
			$('#wp_review_last_tab').val(hash);
		}
	});
	if ($('#wp-review-migrate-log').length) {
		var $migrate_log = $('#wp-review-migrate-log');
		var migrate_started = false;
		var rows_left = parseInt($('#migrate-items-num').text());
		var migrated_rows = $('#start-migrate').data('start');
		var migrate_finished = false;
		var updatelog = function( text ) {
			$migrate_log.css('display', 'block').val(function(index, old) { return text + "\n" + old });
		}
		var ajax_migrate = function( startindex ) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: { action: 'wp_review_migrate_ratings', start: startindex },
			})
			.done(function( data ) {
				$('#migrate-items-num').text(data.rowsleft);
				updatelog( 'Imported ratings: ' + (startindex + 1) + ' - ' + data.lastrow + '...' );
				if ( ! data.finished )
					ajax_migrate( data.lastrow );
				else
					updatelog('Import complete.');
			});
			
		}
		$('#start-migrate').click(function(event) {
			event.preventDefault();
			if (migrate_started)
				return false;

			migrate_started = true;
			updatelog('Import started, please wait...');

			ajax_migrate(migrated_rows);
		});

	}

});