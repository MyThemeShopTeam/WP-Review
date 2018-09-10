<?php
/**
 * Loads widgets.
 *
 * @package WP_Review
 */

/**
 * Registers widgets.
 */
function wpreview_register_widget() {
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-tab-widget.php' );

	register_widget( 'WP_Review_Tab_Widget' );
}
add_action( 'widgets_init', 'wpreview_register_widget' );
