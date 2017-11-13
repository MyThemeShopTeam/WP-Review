<?php
/**
 * Plugin Name: WP Review
 * Plugin URI: http://mythemeshop.com/plugins/wp-review/
 * Description: Create reviews! Choose from stars, percentages or points for review scores. Supports Retina Display, WPMU and Unlimited Color Schemes.
 * Version: 4.0.10
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// WP Review Pro activated?
if ( ! defined( 'MTS_WP_REVIEW_DB_TABLE' )) {
	
	/* Plugin version */
	define( 'WP_REVIEW_PLUGIN_VERSION', '4.0.6' );
	
	/* Sets the custom db table name. */
	define( 'MTS_WP_REVIEW_DB_TABLE', 'mts_wp_reviews' );
		
	/* When plugin is activated */
	register_activation_hook( __FILE__, 'wp_review_activation' );
	add_action('admin_init', 'wp_review_settings_redirect');

	/* Defines constants used by the plugin. */
	add_action( 'plugins_loaded', 'wp_review_constants', 1 );

	/* Internationalize the text strings used. */
	add_action( 'plugins_loaded', 'wp_review_i18n', 2 );

	/* Loads libraries. */
	add_action( 'plugins_loaded', 'wp_review_includes_libraries', 3 );

	/**
	 * Defines constants.
	 *
	 * @since 1.0
	 */
	function wp_review_constants() {

		/* Sets the path to the plugin directory. */
		define( 'WP_REVIEW_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		/* Sets the path to the plugin directory URI. */
		define( 'WP_REVIEW_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		/* Sets the path to the `admin` directory. */
		define( 'WP_REVIEW_ADMIN', WP_REVIEW_DIR . trailingslashit( 'admin' ) );

		/* Sets the path to the `includes` directory. */
		define( 'WP_REVIEW_INCLUDES', WP_REVIEW_DIR . trailingslashit( 'includes' ) );

		/* Sets the path to the `assets` directory. */
		define( 'WP_REVIEW_ASSETS', WP_REVIEW_URI . trailingslashit( 'assets' ) );	

		/* Sets plugin base 'directory/file.php' */
		define( 'WP_REVIEW_PLUGIN_BASE', plugin_basename(__FILE__) );

		define( 'WP_REVIEW_COMMENT_TYPE_VISITOR', 'wp_review_visitor' );

		define( 'WP_REVIEW_VISITOR_RATING_METAKEY', 'wp_review_visitor_rating' );

		/* Keys for user review permissions */
		define( 'WP_REVIEW_REVIEW_DISABLED', '0' );
		define( 'WP_REVIEW_REVIEW_VISITOR_ONLY', '2' );

	}

	/**
	 * Internationalize the text strings used.
	 *
	 * @since 1.0
	 */
	function wp_review_i18n() {
		load_plugin_textdomain( 'wp-review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since 1.0
	 */
	function wp_review_includes_libraries() {

		/* Loads the admin functions. */
		require_once( WP_REVIEW_ADMIN . 'admin.php' );

		/* Loads the meta boxes. */
		require_once( WP_REVIEW_ADMIN . 'metaboxes.php' );

		/* Loads the front-end functions. */	
		require_once( WP_REVIEW_INCLUDES . 'functions.php' );

		/* Loads the widget. */	
		require_once( WP_REVIEW_INCLUDES . 'widget.php' );

		/* Loads the enqueue functions. */
		require_once( WP_REVIEW_INCLUDES . 'enqueue.php' );

		/* Loads the settings page. */
		require_once( WP_REVIEW_ADMIN . 'options.php' );

	}

	function wp_review_activation(){
	    /* Loads activation functions */
	    add_option('wp_review_do_activation_redirect', true);
	    update_option('wp_review_activated', time());
	}

	function wp_review_settings_redirect() {
	    if (get_option('wp_review_do_activation_redirect', false)) {
	        delete_option('wp_review_do_activation_redirect');
	         wp_redirect('options-general.php?page=wp-review%2Fadmin%2Foptions.php#help');
	         exit;
	    }
	}

}

?>
