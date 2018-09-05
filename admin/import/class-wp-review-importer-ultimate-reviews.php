<?php
/**
 * Ultimate Reviews importer
 *
 * @package WP_Review
 */

/**
 * Class WP_Review_Importer_Ultimate_Reviews
 */
class WP_Review_Importer_Ultimate_Reviews implements WP_Review_Importer_Interface {

	/**
	 * Shortcode shows review.
	 *
	 * @var string
	 */
	protected $shortcode_name = 'select-review';

	/**
	 * Shortcode attribute contains review ID.
	 *
	 * @var string
	 */
	protected $attribute_name = 'review_id';

	/**
	 * Runs import.
	 *
	 * @param int   $numposts Number of posts.
	 * @param int   $offset   Offset.
	 * @param array $options  Import options.
	 * @return WP_Review_Importer_Response.
	 */
	public function run( $numposts, $offset, $options ) {
		$mapping = $this->get_reviews_posts_mapping();
		$posts_count = count( $mapping );
		if ( ! $posts_count ) {
			return new WP_Review_Importer_Response( __( 'There is no review.', 'wp-review' ), true, 0, true );
		}

		foreach ( $mapping as $post_id => $review_ids ) {
			foreach ( $review_ids as $review_id ) {
				$this->add_review( $post_id, $review_id );
				$offset++;
			}
		}

		if ( $offset < $posts_count ) {
			return new WP_Review_Importer_Response(
				sprintf( __( 'Imported %1$s of %2$s.', 'wp-review' ), $offset, $posts_count ),
				false,
				$offset
			);
		}

		return new WP_Review_Importer_Response(
			sprintf( __( 'Imported ratings from %s posts.', 'wp-review' ), $posts_count )
		);
	}

	/**
	 * Gets all posts which can have reviews.
	 *
	 * @return array
	 */
	protected function get_posts() {
		$post_types = get_post_types( array( 'public' => true ) );
		if ( isset( $post_types['urp_review'] ) ) {
			unset( $post_types['urp_review'] );
		}
		return get_posts( array(
			'post_type'      => $post_types,
			'posts_per_page' => -1,
		) );
	}

	/**
	 * Gets reviews posts mapping.
	 *
	 * @return array Array with key is review ID and value is post ID.
	 */
	protected function get_reviews_posts_mapping() {
		$mapping = array();
		$posts = $this->get_posts();

		foreach ( $posts as $post ) {
			$shortcodes = $this->get_review_shortcodes_in_content( $post->post_content );

			if ( ! $shortcodes ) {
				continue;
			}

			foreach ( $shortcodes as $shortcode ) {
				$review_id = $this->get_review_id( $shortcode );
				if ( ! $review_id ) {
					continue;
				}
				if ( ! isset( $mapping[ $post->ID ] ) ) {
					$mapping[ $post->ID ] = array();
				}
				$mapping[ $post->ID ][] = $review_id;
			}
		}
		return $mapping;
	}

	/**
	 * Gets review shortcodes in content.
	 *
	 * @param  string $content The content.
	 * @return array
	 */
	protected function get_review_shortcodes_in_content( $content ) {
		$pattern = get_shortcode_regex();
		preg_match_all( '/' . $pattern . '/s', $content, $matches );
		if ( empty( $matches[0] ) ) {
			return array();
		}

		$results = $matches[0];
		foreach ( $results as $index => $shortcode ) {
			if ( strpos( $shortcode, '[' . $this->shortcode_name ) !== 0 ) {
				unset( $results[ $index ] );
			}
		}

		return array_values( $results );
	}

	/**
	 * Gets shortcode attributes.
	 *
	 * @param  string $shortcode Shortcode text.
	 * @return array
	 */
	protected function parse_atts( $shortcode ) {
		$shortcode = trim( str_replace( array( '[' . $this->shortcode_name, ']' ), '', $shortcode ) );
		if ( ! $shortcode ) {
			return array();
		}
		return shortcode_parse_atts( $shortcode );
	}

	/**
	 * Gets review ID from shortcode.
	 *
	 * @param  string $shortcode Shortcode text.
	 * @return int|false
	 */
	protected function get_review_id( $shortcode ) {
		$atts = $this->parse_atts( $shortcode );
		if ( empty( $atts[ $this->attribute_name ] ) ) {
			return false;
		}
		return intval( $atts[ $this->attribute_name ] );
	}

	/**
	 * Adds review to post.
	 *
	 * @param int $post_id   Post ID.
	 * @param int $review_id Review ID.
	 * @return bool
	 */
	protected function add_review( $post_id, $review_id ) {
		$review = get_post( $review_id );
		if ( ! $review ) {
			return false;
		}
		$insert_data = array(
			'comment_author'       => get_post_meta( $review_id, 'EWD_URP_Post_Author', true ),
			'comment_author_email' => get_post_meta( $review_id, 'EWD_URP_Post_Email', true ),
			'comment_approved'     => 1,
			'comment_content'      => $review->post_content,
			'comment_post_ID'      => $post_id,
			'user_id'              => 0,
			'comment_parent'       => 0,
			'comment_type'         => WP_REVIEW_COMMENT_TYPE_COMMENT,
			'comment_date'         => $review->post_date,
			'comment_date_gmt'     => $review->post_date_gmt,
			'comment_meta'         => array(
				WP_REVIEW_COMMENT_RATING_METAKEY => get_post_meta( $review_id, 'EWD_URP_Overall_Score', true ),
				WP_REVIEW_COMMENT_TITLE_METAKEY  => $review->post_title,
				// Extra fields.
				'product_name'                   => get_post_meta( $review_id, 'EWD_URP_Product_Name', true ),
				'video'                          => get_post_meta( $review_id, 'EWD_URP_Review_Video', true ),
				'order_id'                       => get_post_meta( $review_id, 'EWD_URP_Order_ID', true ),
			),
		);

		return false != wp_insert_comment( $insert_data );
	}
}
