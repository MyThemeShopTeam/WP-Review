<?php
/**
 * Admin functions for this plugin.
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register admin.css file. */
add_action( 'admin_enqueue_scripts', 'wp_review_admin_style' );

add_action( 'wp_ajax_wpreview_rated', 'wp_review_rated_ajax', 10 );
add_filter( 'admin_footer_text', 'wp_review_admin_footer_text', 10 );

/**
 * Register custom style for the meta box.
 *
 * @since 1.0
 */
function wp_review_admin_style( $hook_suffix ) {
	wp_enqueue_style( 'wp-review-admin-style', WP_REVIEW_ASSETS . 'css/admin.css', array( 'wp-color-picker' ) );
	wp_enqueue_script(
		'wp-review-admin-script',
		WP_REVIEW_ASSETS . 'js/admin.js',
		array( 'wp-color-picker', 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-sortable' ),
		false,
		true
	);

	wp_enqueue_style(
		'wp-review-admin-ui-css',
		'//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css',
		false,
		null,
		false
	);

	// Load frontend css but not on the post editor screen
	if ( stripos('post.php', $hook_suffix) === false ) {
		wp_enqueue_style( 'wp_review-style', trailingslashit( WP_REVIEW_ASSETS ) . 'css/wp-review.css', array(), WP_REVIEW_PLUGIN_VERSION, 'all' );
	}
}

function wp_review_admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		
		$post_types = get_post_types( array('public' => true), 'names' );
	    $excluded_post_types = apply_filters('wp_review_excluded_post_types', array('attachment'));
	    $allowed_post_types = array_diff($post_types, $excluded_post_types);

		// Check to make sure we're on a Review Editing page
		if ( ( isset( $current_screen->id ) && strpos($current_screen->id, 'wp-review') !== false ) ||
		( isset( $current_screen->action ) && $current_screen->action == 'add' && in_array( $current_screen->id, $allowed_post_types ) ) ||
		( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && in_array( $current_screen->id, $allowed_post_types ) ) ) {
			// Change the footer text
			if ( ! get_option( 'wpreview_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>WP Review</strong> please leave us a %s rating. A huge thank you from MyThemeShop in advance!', 'woocommerce' ), '<a href="https://wordpress.org/support/view/plugin-reviews/wp-review?filter=5" target="_blank" class="wpreview-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'wp-review' ) . '"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a>' );
				$footer_text .= "
					<script type=\"text/javascript\">
					jQuery( 'a.wpreview-rating-link' ).click( function() {
						jQuery.post( ajaxurl, { action: 'wpreview_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
					</script>
				";
			} else {
				//$footer_text = __( 'Thank you for using WP Review.', 'wp-review' );
			}
		}

		return $footer_text;
}

function wp_review_rated_ajax() {
	update_option( 'wpreview_admin_footer_text_rated', '1' );
}