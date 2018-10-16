<?php
/**
 * Review importer
 *
 * @package WP_Review
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WP_Review_Importer
 */
class WP_Review_Importer {

	/**
	 * The number of posts to import per request.
	 *
	 * @var int
	 */
	private $numposts;

	/**
	 * WP_Review_Importer constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wp_review_import_options', array( $this, 'import_options' ) );
		add_action( 'init', array( $this, 'extra_tasks' ) );

		$this->numposts = apply_filters( 'wp_review_import_numposts', 10 );
	}

	/**
	 * AJAX handler for importing options.
	 */
	public function import_options() {
		check_ajax_referer( 'wp-review-import-options' );
		if ( empty( $_POST['code'] ) ) {
			wp_send_json_error();
		}
		$code = wp_unslash( $_POST['code'] );
		if ( wp_review_import_options( $code ) ) {
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/**
	 * Runs extra tasks.
	 */
	public function extra_tasks() {
		if ( ! function_exists( 'Display_Select_Review' ) ) {
			add_shortcode( 'select-review', '__return_empty_string' );
		}
	}
}

$plugin_importer = new WP_Review_Importer();
