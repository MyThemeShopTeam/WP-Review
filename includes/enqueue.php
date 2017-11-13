<?php
/**
 * Custom style for the plugin.
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Enqueue style for this plugin. */
add_action( 'wp_enqueue_scripts', 'wp_review_enqueue' );

/* IE7 style for the font icon. */
add_action( 'wp_head', 'wp_review_ie7' );

/**
 * Enqueue style
 *
 * @since 1.0
 */
function wp_review_enqueue() {

	wp_register_style( 'wp_review-style', trailingslashit( WP_REVIEW_ASSETS ) . 'css/wp-review.css', array(), WP_REVIEW_PLUGIN_VERSION, 'all' );
	
	wp_register_script( 'wp_review-js', trailingslashit( WP_REVIEW_ASSETS ) . 'js/main.js', array( 'jquery' ), WP_REVIEW_PLUGIN_VERSION, true );
	wp_localize_script( 'wp_review-js', 'wpreview', array(
		'ajaxurl' => admin_url('admin-ajax.php')
	) );

	
	if ( is_singular() ) {
		wp_enqueue_style( 'wp_review-style' );
		wp_enqueue_script( 'wp_review-js' );
	}
}

/**
 * IE7 style for the font icon.
 *
 * @since 1.0
 */
function wp_review_ie7() { ?>
<!--[if IE 7]>
<link rel="stylesheet" href="<?php echo trailingslashit( WP_REVIEW_ASSETS ) . 'css/wp-review-ie7.css'; ?>">
<![endif]-->
<?php }
?>