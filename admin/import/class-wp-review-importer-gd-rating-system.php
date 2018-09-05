<?php
/**
 * GD Rating System importer
 *
 * @package WP_Review
 */

/**
 * Class WP_Review_Importer_GD_Rating_System
 */
class WP_Review_Importer_GD_Rating_System implements WP_Review_Importer_Interface {

	/**
	 * Runs import.
	 *
	 * @param int   $numposts Number of posts.
	 * @param int   $offset   Offset.
	 * @param array $options  Import options.
	 * @return WP_Review_Importer_Response.
	 */
	public function run( $numposts, $offset, $options ) {
		$items = $this->get_post_items( $numposts, $offset );
		$posts_count = count( $items );
		if ( ! $posts_count ) {
			return new WP_Review_Importer_Response( __( 'There is no review.', 'wp-review' ), true, 0, true );
		}

		foreach ( $items as $item ) {
			$this->import_item( $item );
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
	 * Gets post items.
	 *
	 * @param int $numposts Number of posts.
	 * @param int $offset   Offset.
	 * @return array
	 */
	protected function get_post_items( $numposts, $offset ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}gdrts_items WHERE entity = 'posts' LIMIT %d OFFSET %d",
				intval( $numposts ),
				intval( $offset )
			)
		);
	}


	/**
	 * Imports post item.
	 *
	 * @param object $item Post item.
	 */
	protected function import_item( $item ) {
		$method = $this->get_item_meta( $item->item_id, 'posts-integration_method', 'stars-rating' );
		$ratings = $this->get_ratings( $item );
		$last_type = 'star';
		// print_r( $ratings ); return;
		foreach ( $ratings as $rating ) {
			if ( $rating->method !== $method ) {
				continue;
			}

			switch ( $method ) {
				case 'stars-rating':
					$this->import_star_rating( $item, $rating );
					$last_type = 'star';
					continue;
				case 'slider-rating':
					$this->import_percentage_rating( $item, $rating );
					$last_type = 'percentage';
					continue;
				case 'thumbs-ratings':
					$this->import_thumbs_rating( $item, $rating );
					$last_type = 'thumbs';
					continue;
			}
		}

		$location = $this->get_item_meta( $item->item_id, 'posts-integration_location', 'stars-rating' );
		if ( 'hide' !== $location ) {
			$user_review = get_post_meta( $item->id, 'wp_review_userReview', true );
			switch ( $user_review ) {
				case 0:
					update_post_meta( $item->id, 'wp_review_userReview', 2 );
					break;
				case 3:
					update_post_meta( $item->id, 'wp_review_userReview', 4 );
					break;
			}
		}

		update_post_meta( $item->id, 'wp_review_user_review_type', $last_type );
	}


	/**
	 * Gets post rating meta value.
	 *
	 * @param int    $item_id Item ID.
	 * @param string $key     Meta key.
	 * @param mixed  $default Default value.
	 * @return mixed|bool
	 */
	protected function get_item_meta( $item_id, $key, $default = false ) {
		global $wpdb;
		$cols = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->prefix}gdrts_itemmeta WHERE item_id = %d AND meta_key = %s LIMIT 1",
				intval( $item_id ),
				$key
			)
		);
		if ( ! $cols ) {
			return $default;
		}
		return maybe_unserialize( $cols[0] );
	}


	/**
	 * Imports star rating.
	 *
	 * @param object $item   Post item.
	 * @param object $rating Rating data.
	 * @return int|false
	 */
	protected function import_star_rating( $item, $rating ) {
		$max = $this->get_rating_meta( $rating->log_id, 'max' );
		$vote = $this->get_rating_meta( $rating->log_id, 'vote' );
		$rating->value = floatval( $vote ) / floatval( $max ) * 5;
		$rating->type = 'star';
		return $this->insert_rating( $item->id, $rating );
	}


	/**
	 * Imports percentage rating.
	 *
	 * @param object $item   Post item.
	 * @param object $rating Rating data.
	 * @return int|false
	 */
	protected function import_percentage_rating( $item, $rating ) {
		$max = $this->get_rating_meta( $rating->log_id, 'max' );
		$vote = $this->get_rating_meta( $rating->log_id, 'vote' );
		$rating->value = floatval( $vote ) / floatval( $max ) * 100;
		$rating->type = 'percentage';
		return $this->insert_rating( $item->id, $rating );
	}


	/**
	 * Imports percentage rating.
	 *
	 * @param object $item   Post item.
	 * @param object $rating Rating data.
	 * @return int|false
	 */
	protected function import_thumbs_rating( $item, $rating ) {
		$vote = $this->get_rating_meta( $rating->log_id, 'vote' );
		$rating->value = intval( $vote ) > 0 ? 100 : 0;
		$rating->type = 'thumbs';
		return $this->insert_rating( $item->id, $rating );
	}


	/**
	 * Gets ratings.
	 *
	 * @param object $item Post item.
	 * @return array
	 */
	protected function get_ratings( $item ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}gdrts_logs WHERE item_id = %d AND status = 'active'", intval( $item->id ) ) );
	}


	/**
	 * Gets rating meta.
	 *
	 * @param int    $rating_id Rating ID.
	 * @param string $key       Meta key.
	 * @return mixed            Return false if not found.
	 */
	protected function get_rating_meta( $rating_id, $key ) {
		global $wpdb;
		$cols = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->prefix}gdrts_logmeta WHERE log_id = %d AND meta_key = %s",
				intval( $rating_id ),
				$key
			)
		);
		if ( ! $cols ) {
			return false;
		}
		return maybe_unserialize( $cols[0] );
	}


	/**
	 * Inserts user rating.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $rating  Rating data.
	 * @return int|false
	 */
	protected function insert_rating( $post_id, $rating ) {
		$rating_type = wp_review_get_rating_type_data( $rating->type );
		$comment_content = sprintf( __( 'Visitor Rating: %s', 'wp-review' ), sprintf( $rating_type['value_text'], $rating->value ) );

		return wp_insert_comment( array(
			'user_id'           => $rating->user_id,
			'comment_type'      => WP_REVIEW_COMMENT_TYPE_VISITOR,
			'comment_post_ID'   => $post_id,
			'comment_parent'    => 0,
			'comment_author_IP' => $rating->ip,
			'comment_content'   => $comment_content,
			'comment_agent'     => '',
			'comment_date'      => $rating->logged,
			'comment_approved'  => 1,
			'comment_meta'      => array(
				WP_REVIEW_VISITOR_RATING_METAKEY => $rating->value,
			),
		) );
	}


	protected function get_entities() {
		return get_option( 'dev4press_gd-rating-system_entities', array() );
	}
}
