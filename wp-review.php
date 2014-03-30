<?php
/**
 * Plugin Name: WP Review
 * Plugin URI: http://mythemeshop.com/
 * Description: Easily create custom review content.
 * Version: 3.1
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Sets the custom db table name. */
define( 'MTS_WP_REVIEW_DB_TABLE', 'mts_wp_reviews' );
	
/* When plugin is activated */
register_activation_hook( __FILE__, 'wp_review_activation' );


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

}

function wp_review_activation(){
    /* Loads activation functions */
    //require_once( plugin_dir_path( __FILE__ ) . '/includes/functions.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/admin/activation.php' );
}
?>