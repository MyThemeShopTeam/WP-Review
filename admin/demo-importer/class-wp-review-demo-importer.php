<?php
/**
 * Demo importer
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Demo_Importer
 */
class WP_Review_Demo_Importer {

	/**
	 * Imports demo.
	 *
	 * @param string $file Import file path.
	 */
	public function import( $file ) {
		add_filter( 'wp_import_post_data_processed', array( $this, 'filter_post_data' ) );
		$importer = new WP_Import();
		$importer->import( $file );
		remove_filter( 'wp_import_post_data_processed', array( $this, 'filter_post_data' ) );
	}

	/**
	 * Filters post data before inserting.
	 *
	 * @param array $post_data Post data.
	 * @return array
	 */
	public function filter_post_data( $post_data ) {
		if ( 'attachment' !== $post_data['post_type'] ) {
			$post_data['post_status'] = 'draft';
		}
		return $post_data;
	}
}
