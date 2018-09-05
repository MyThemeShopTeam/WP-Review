<?php

defined( 'ABSPATH' ) || exit;

include_once __DIR__ . '/import/class-wp-review-importer-response.php';
include_once __DIR__ . '/import/class-wp-review-importer-interface.php';
include_once __DIR__ . '/import/class-wp-review-importer-factory.php';
include_once __DIR__ . '/import/class-wp-review-importer-yasr.php';
include_once __DIR__ . '/import/class-wp-review-importer-ahreview.php';
include_once __DIR__ . '/import/class-wp-review-importer-wprichsnippets.php';
include_once __DIR__ . '/import/class-wp-review-importer-ultimate-reviews.php';
include_once __DIR__ . '/import/class-wp-review-importer-wp-product-review.php';
include_once __DIR__ . '/import/class-wp-review-importer-gd-rating-system.php';

class WP_Review_Importer {

	/**
	 * The number of posts to import per request.
	 *
	 * @var int
	 */
	private $numposts;

	public function __construct() {
		add_action( 'wp_ajax_wp_review_import_rating', array( $this, 'wp_review_ajax_import_rating' ) );
		add_action( 'wp_ajax_wp_review_import_options', array( $this, 'import_options' ) );
		add_action( 'init', array( $this, 'extra_tasks' ) );

		$this->numposts = apply_filters( 'wp_review_import_numposts', 10 );
	}

	/**
	 * AJAX handler to import ratings.
	 */
	public function wp_review_ajax_import_rating() {
		check_ajax_referer( 'wp_review_import_rating' );

		$source = empty( $_POST['source'] ) ? '' : $_POST['source'];
		$offset = empty( $_POST['offset'] ) ? 0 : $_POST['offset'];
		$options = get_option( 'wp_review_options' );

		try {
			$importer = WP_Review_Importer_Factory::create( $source );
			$res = $importer->run( $this->numposts, $offset, $options );
			wp_send_json_success( $res->to_array() );
		} catch ( Exception $e ) {
			wp_send_json_error( new WP_Error( 'wp-review', $e->getMessage() ) );
		}
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
