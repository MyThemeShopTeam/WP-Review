<?php
/**
 * WP Product Review importer
 *
 * @package WP_Review
 */

/**
 * Class WP_Review_Importer_WP_Product_Review
 */
class WP_Review_Importer_WP_Product_Review implements WP_Review_Importer_Interface {

	/**
	 * Runs import.
	 *
	 * @param int   $numposts Number of posts.
	 * @param int   $offset   Offset.
	 * @param array $options  Import options.
	 * @return WP_Review_Importer_Response.
	 */
	public function run( $numposts, $offset, $options ) {
		$posts = $this->get_posts( $numposts, $offset );
		$posts_count = count( $posts );
		if ( ! $posts_count ) {
			return new WP_Review_Importer_Response( __( 'There is no review.', 'wp-review' ), true, 0, true );
		}

		foreach ( $posts as $post_id ) {
			$this->import_reviews( $post_id );
		}

		$new_offset = $offset + $posts_count;

		if ( $new_offset < $posts_count ) {
			return new WP_Review_Importer_Response(
				sprintf( __( 'Imported %1$s of %2$s.', 'wp-review' ), $new_offset, $posts_count ),
				false,
				$new_offset
			);
		}

		return new WP_Review_Importer_Response(
			sprintf( __( 'Imported ratings from %s posts.', 'wp-review' ), $posts_count )
		);
	}

	/**
	 * Gets posts have reviews.
	 *
	 * @param int $numposts Number of posts.
	 * @param int $offset   Offset.
	 * @return array
	 */
	protected function get_posts( $numposts, $offset ) {
		return get_posts( array(
			'post_type'      => 'any',
			'posts_per_page' => $numposts,
			'offset'         => $offset,
			'fields'         => 'ids',
			'meta_key'       => 'cwp_meta_box_check',
			'meta_value'     => 'yes',
		) );
	}

	/**
	 * Imports reviews.
	 *
	 * @param int $post_id Post ID.
	 */
	protected function import_reviews( $post_id ) {
		$price = get_post_meta( $post_id, 'cwp_rev_price', true );
		update_post_meta( $post_id, 'wp_review_product_price', $price );

		update_post_meta( $post_id, 'wp_review_type', 'percentage' );

		$heading = get_post_meta( $post_id, 'cwp_rev_product_name', true );
		update_post_meta( $post_id, 'wp_review_heading', $heading );

		$their_items = get_post_meta( $post_id, 'wppr_options', true ) ? get_post_meta( $post_id, 'wppr_options', true ) : array();
		$our_items = array();
		foreach ( $their_items as $item ) {
			$our_items[] = array(
				'wp_review_item_title' => $item['name'],
				'wp_review_item_star'  => $item['value'],
			);
		}
		update_post_meta( $post_id, 'wp_review_item', $our_items );
	}
}
