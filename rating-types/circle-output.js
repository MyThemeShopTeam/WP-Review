jQuery(document).ready(function($) {
	if ($('.wp-review-circle-rating').length) {
		$('.wp-review-circle-rating').each(function(index, el) {
			// Mega Menu compatibility
			if ( ! $(this).closest('.wpmm-posts').length )
				$(this).knob();
		});
		$('.review-wrapper .wp-review-circle-rating').each(function() {
			var $this = $(this);
			$this.css('font-size', parseInt($this.css('font-size'))*1.4+'px').data('initial_value', $this.val()).val('0').trigger('change');
		});
	}

	// AJAX content
	$( document ).ajaxComplete(function(event, xhr, settings) {
		$('.wp-review-circle-rating').each(function(index, el) {
			if ( ! $(this).closest('.wpmm-posts').length )
				$(this).knob();
		});
	});

	// Mega Menu compatibility
	if (typeof wpmm != 'undefined') {
		$('.menu-item-' + wpmm.css_class + '-taxonomy a').mouseenter(function(event) {
			$('.wpmm-visible .wp-review-circle-rating').knob();
		});
		$( document ).ajaxComplete(function(event, xhr, settings) {
			if (settings.data && settings.data.indexOf('action=get_megamenu') > -1 && settings.data.indexOf('wpreview_support') == -1) {
		  		$('.wpmm-visible .wp-review-circle-rating').knob();
		  	}
		});
	}
});
