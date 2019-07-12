( function( $ ) {
	"use strict";

	function dismiss( later ) {
		$.post(
			ajaxurl,
			{
				action: 'wpr_dismiss_review_notice',
				later: later
			},
			function( response ) {
				$( '#wpr-review-notice' ).remove();
			}
		)
	}

	$( document ).ready( function() {
		$( '.wpr-review-notice-btn-dismiss, #wpr-review-notice .notice-dismiss' ).on( 'click', function() {
			dismiss();
		});
		$( '.wpr-review-notice-btn-later' ).on( 'click', function() {
			dismiss( true );
		});
	});
})( jQuery );
