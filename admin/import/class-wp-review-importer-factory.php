<?php
/**
 * Importer factory.
 *
 * @package WP_Review
 */

/**
 * Class WP_Review_Importer_Factory
 */
class WP_Review_Importer_Factory {

	/**
	 * Create importer.
	 *
	 * @throws Exception If source is unsupported.
	 *
	 * @param string $source Import source.
	 * @return WP_Review_Importer_Interface
	 */
	public static function create( $source ) {
		switch ( $source ) {
			case 'yet-another-stars-rating':
				return new WP_Review_Importer_Yasr();
			case 'author-hreview':
				return new WP_Review_Importer_AhReview();
			case 'wp-rich-snippets':
				return new WP_Review_Importer_WPRichSnippets();
			case 'ultimate-reviews':
				return new WP_Review_Importer_Ultimate_Reviews();
			case 'wp-product-review':
				return new WP_Review_Importer_WP_Product_Review();
			case 'gd-rating-system':
				return new WP_Review_Importer_GD_Rating_System();
			default:
				// Translators: Import source.
				throw new Exception( sprintf( __( 'Unsupported plugin: %s', 'wp-review' ), $source ) );
		}
	}
}
