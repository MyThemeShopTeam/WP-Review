<?php

class WP_Review_Importer_AhReview implements WP_Review_Importer_Interface {

	public function run( $numposts, $offset, $options ) {
		$posts = get_posts( array(
			'posts_per_page' => $numposts,
			'offset' => $offset,
			'meta_key' => 'ta_post_review_rating',
			'fields' => 'ids',
		) );

		$posts_count = count( $posts );
		if ( ! $posts_count ) {
			return new WP_Review_Importer_Response( __( 'There is no review.', 'wp-review' ), true, 0, true );
		}

		foreach ( $posts as $post_id ) {
			$rating = get_post_meta( $post_id, 'ta_post_review_rating', true );

			update_post_meta( $post_id, 'wp_review_type', 'star' );
			update_post_meta( $post_id, 'wp_review_userReview', $options['default_user_review_type'] );
			update_post_meta( $post_id, 'wp_review_total', $rating );
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
}
