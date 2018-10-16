<?php
/**
 * Demo importer feature
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Require files.
 */
require_once WP_REVIEW_ADMIN . 'demo-importer/class-wp-import.php';
require_once WP_REVIEW_ADMIN . 'demo-importer/class-wp-review-demo-importer.php';


/**
 * Imports demo.
 */
function wp_review_import_demo() {
	$file     = WP_REVIEW_INCLUDES . 'demo/demo-content.xml';
	$importer = new WP_Review_Demo_Importer();
	$importer->import( $file );
}

/**
 * Prints demo importer popup.
 */
function wp_review_print_demo_importer_popup() {
	?>
	<div id="wp-review-demo-importer-popup" class="mfp-hide">
		<div id="wp-review-demo-importer-modal">
			<div id="wp-review-demo-importer-modal-header">
				<h2><span class="spinner is-active"></span><?php esc_html_e( 'Processing, please wait&hellip;', 'wp-review' ); ?></h2>
			</div>
			<div id="wp-review-demo-importer-modal-content">
			</div>
			<div id="wp-review-demo-importer-modal-footer">
				<span id="wp-review-demo-importer-modal-footer-info"><?php esc_html_e( 'Processing, please wait&hellip;', 'wp-review' ); ?></span>
				<button id="wp-review-demo-importer-modal-footer-button" class="button button-primary"><?php esc_html_e( 'Ok', 'wp-review' ); ?></button>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_footer-settings_page_wp-review/admin/options', 'wp_review_print_demo_importer_popup' );

/**
 * AJAX handler for demo importer.
 */
function wp_review_ajax_import_demo() {
	check_ajax_referer( 'wp_review_import_demo', 'nonce' );
	wp_review_import_demo();
	die();
}
add_action( 'wp_ajax_wp-review-import-demo', 'wp_review_ajax_import_demo' );
