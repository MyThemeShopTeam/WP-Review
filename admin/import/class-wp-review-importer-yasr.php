<?php

class WP_Review_Importer_Yasr implements WP_Review_Importer_Interface {

	public function __construct() {
		$plugin_pathname = 'yet-another-stars-rating/yet-another-stars-rating.php';
		$plugin_dir = dirname( $plugin_pathname );

		if ( ! defined( 'YASR_VERSION_NUM' ) ) {
			throw new Exception( sprintf( __( 'The plugin %s needs to be installed and activated before importing into WP Review.', 'wp-review' ), 'Yet Another Stars Rating' ) );
		}

		include_once WP_REVIEW_DIR . '../' . $plugin_dir . '/lib/yasr-db-functions.php';
	}

	public function run( $numposts, $offset, $options ) {
		$all_pids = $this->get_posts_with_rating();
		$subset_pids = array_slice( $all_pids, $offset, $numposts );
		$posts_count = count( $all_pids );
		if ( ! $posts_count ) {
			return new WP_Review_Importer_Response( __( 'There is no review.', 'wp-review' ), true, 0, true );
		}

		foreach ( $subset_pids as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post ) {
				continue;
			}

			update_post_meta( $post_id, 'wp_review_type', 'star' );
			update_post_meta( $post_id, 'wp_review_userReview', $options['default_user_review_type'] );
			// update_post_meta( $post_id, 'wp_review_box_template', $options['box_template'] );
			// update_post_meta( $post_id, 'wp_review_color', $options['colors']['color'] );
			// update_post_meta( $post_id, 'wp_review_fontcolor', $options['colors']['font_color'] );
			// update_post_meta( $post_id, 'wp_review_bgcolor1', $options['colors']['bgcolor1'] );
			// update_post_meta( $post_id, 'wp_review_bgcolor2', $options['colors']['bgcolor2'] );
			// update_post_meta( $post_id, 'wp_review_bordercolor', $options['colors']['bordercolor'] );
			// update_post_meta( $post_id, 'wp_review_width', $options['width'] );
			// update_post_meta( $post_id, 'wp_review_align', $options['width'] );

			$this->import_post_overall_rating( $post_id );
			$this->import_post_multiset_ratings( $post_id );
		}

		$new_offset = $offset + count( $subset_pids );

		if ( $new_offset < count( $all_pids ) ) {
			return new WP_Review_Importer_Response(
				sprintf( __( 'Imported %1$s of %2$s.', 'wp-review' ), $new_offset, count( $all_pids ) ),
				false,
				$new_offset
			);
		}

		return new WP_Review_Importer_Response(
			sprintf( __( 'Imported ratings from %s posts.', 'wp-review' ), count( $all_pids ) )
		);
	}

	/**
	 * Get all posts with overall rating and multiset ratings.
	 *
	 * @return int[] Array of post IDs
	 */
	private function get_posts_with_rating() {
		global $wpdb;

		$overall_rating_posts = get_posts( array(
			'fields' => 'ids',
			'meta_key' => 'yasr_overall_rating',
			'posts_per_page' => -1,
		) );


		$multiset_rating_posts = $wpdb->get_col(
			'select post_id from ' . YASR_MULTI_SET_VALUES_TABLE .
				' group by post_id'
		);

		return wp_parse_id_list( array_merge(
			$overall_rating_posts,
			$multiset_rating_posts
		) );
	}

	private function import_post_overall_rating( $post_id ) {
		$overall_rating = yasr_get_overall_rating( $post_id, false );
		if ( $overall_rating ) {
			update_post_meta( $post_id, 'wp_review_total', $overall_rating );
		}

		$this->import_post_overall_visitor_rating( $post_id );
	}

	private function import_post_overall_visitor_rating( $post_id ) {
		global $wpdb;

		$ratings = $wpdb->get_results( $wpdb->prepare(
			'select * from ' . YASR_LOG_TABLE . ' where post_id = %d',
			$post_id
		), ARRAY_A );

		foreach ( $ratings as $rating ) {
			$comment_content = sprintf( __( 'Visitor Rating: %s', 'wp-review' ), sprintf( '%d stars', $rating['vote'] ) );

			wp_insert_comment( array(
				'user_id' => $rating['user_id'],
				'comment_post_ID' => $rating['post_id'],
				'comment_date' => strtotime( $rating['date'] ),
				'comment_approved' => 1,
				'comment_content' => $comment_content,
			) );
		}

		$sum = array_sum( wp_list_pluck( $ratings, 'vote' ) );

		update_post_meta( $post_id, 'wp_review_user_review_type', 'star' );
		update_post_meta( $post_id, 'wp_review_user_reviews', $sum );
		update_post_meta( $post_id, 'wp_review_review_count', count( $ratings ) );
	}

	private function import_post_multiset_ratings( $post_id ) {
		$set_types = $this->get_post_multiset_types( $post_id );
		$individual_ratings = array();
		$individual_visitor_ratings = array();
		$rating_ids = array();

		// There usually is just a single set type but if there are multiple set types, let's just merge them into one.
		foreach ( $set_types as $set_type ) {
			$set_values = yasr_get_multi_set_values_and_field( $post_id, $set_type );

			foreach ( $set_values as $value ) {
				$id = sanitize_title( $value->name ) . '_' . wp_generate_password( 6 );
				$rating = array();

				$rating['wp_review_item_star'] = (float) $value->vote;
				$rating['wp_review_item_title'] = $value->name;
				$rating['id'] = $rating_ids[ $value->id ] = $id;

				$individual_ratings[] = $rating;
			}

			$set_visitor_values = yasr_get_multi_set_visitor( $post_id, $set_type );
			foreach ( $set_visitor_values as $value ) {
				// Shouldn't happen but still checking.
				if ( ! isset( $rating_ids[ $value->id ] ) ) {
					continue;
				}

				$visitor_rating = array();
				$visitor_rating['total'] = (float) $value->sum_votes;
				$visitor_rating['count'] = (int) $value->number_of_votes;

				$individual_visitor_ratings[ $rating_ids[ $value->id ] ] = $visitor_rating;
			}
		}

		if ( ! empty( $individual_ratings ) ) {
			// Use the average of the multiset as rating total instead.
			$total = array_sum( wp_list_pluck( $individual_ratings, 'wp_review_item_star' ) );
			$mean = round( $total / count( $individual_ratings ), 1 );
			update_post_meta( $post_id, 'wp_review_total', $mean );
			update_post_meta( $post_id, 'wp_review_item', $individual_ratings );
		}

		if ( ! empty( $individual_visitor_ratings ) ) {
			update_post_meta( $post_id, 'wp_review_user_feature_reviews', $individual_visitor_ratings );
			update_post_meta( $post_id, 'wp_review_comment_feature_reviews', $individual_visitor_ratings );
		}
	}

	private function get_post_multiset_types( $post_id ) {
		global $wpdb;

		return $wpdb->get_col(
			$wpdb->prepare(
				'select set_type from ' . YASR_MULTI_SET_VALUES_TABLE .
					' where post_id = %d' .
					' group by set_type',
				$post_id
			)
		);
	}
}
