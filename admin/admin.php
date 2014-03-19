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

/**
 * Register custom style for the meta box.
 *
 * @since 1.0
 */
function wp_review_admin_style( $hook_suffix ) {
	wp_enqueue_style( 'wp-review-admin-style', WP_REVIEW_ASSETS . 'css/admin.css' );
	wp_enqueue_script( 'wp-review-admin-script', WP_REVIEW_ASSETS . 'js/admin.js', array( 'wp-color-picker', 'jquery' ), false, true );

	/* Load the color picker dependencies style. */
	wp_enqueue_style( 'wp-color-picker' );
}
?>