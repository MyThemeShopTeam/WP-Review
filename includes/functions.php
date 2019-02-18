<?php
/**
 * WP Review
 *
 * @package   WP_Review
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Gets plugin option.
 *
 * @since 3.0.0
 *
 * @param  string $name    Option name.
 * @param  mixed  $default Default value.
 * @return mixed
 */
function wp_review_option( $name, $default = null ) {
	static $options = null;
	if ( ! is_array( $options ) ) {
		$options = get_option( 'wp_review_options', array() );
	}

	$value = isset( $options[ $name ] ) ? $options[ $name ] : $default;
	$value = apply_filters( 'wp_review_option_' . $name, $value );
	$value = apply_filters( 'wp_review_option', $value, $name );
	return $value;
}


/**
 * Gets default colors.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_default_colors() {
	$default_colors = array(
		'color'          => '#1e73be',
		'inactive_color' => '#95bae0',
		'fontcolor'      => '#555555',
		'bgcolor1'       => '#e7e7e7',
		'bgcolor2'       => '#ffffff',
		'bordercolor'    => '#e7e7e7',
	);

	$custom_default_colors = apply_filters( 'wp_review_default_colors', $default_colors );

	return array_merge( $default_colors, $custom_default_colors );
}


/**
 * Gets global colors setting.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_global_colors() {
	$colors         = wp_review_option( 'colors' );
	$default_colors = wp_review_get_default_colors();
	$fields         = array( 'color', 'inactive_color', 'fontcolor', 'bgcolor1', 'bgcolor2', 'bordercolor' );
	foreach ( $fields as $key ) {
		if ( empty( $colors[ $key ] ) ) {
			$colors[ $key ] = isset( $default_colors[ $key ] ) ? $default_colors[ $key ] : '';
		}
	}
	return $colors;
}


/**
 * Gets default location.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wp_review_get_default_location() {
	return apply_filters( 'wp_review_default_location', 'bottom' );
}


/**
 * Gets default criteria.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_default_criteria() {
	$default_criteria = apply_filters( 'wp_review_default_criteria', array() );
	if ( ! $default_criteria && wp_review_option( 'default_features', array() ) ) {
		$default_criteria = wp_review_option( 'default_features', array() );
	}
	return $default_criteria;
}


/**
 * Gets post custom layout data.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return array
 */
function wp_review_get_custom_layout_data( $post_id ) {
	$data = array(
		'enable'         => get_post_meta( $post_id, 'wp_review_custom_colors', true ),
		'box_template'   => get_post_meta( $post_id, 'wp_review_box_template', true ),
		'color'          => get_post_meta( $post_id, 'wp_review_color', true ),
		'inactive_color' => get_post_meta( $post_id, 'wp_review_inactive_color', true ),
		'fontcolor'      => get_post_meta( $post_id, 'wp_review_fontcolor', true ),
		'bgcolor1'       => get_post_meta( $post_id, 'wp_review_bgcolor1', true ),
		'bgcolor2'       => get_post_meta( $post_id, 'wp_review_bgcolor2', true ),
		'bordercolor'    => get_post_meta( $post_id, 'wp_review_bordercolor', true ),
	);
	return $data;
}


// Image sizes for the widgets.
add_image_size( 'wp_review_large', 320, 200, true );
add_image_size( 'wp_review_small', 65, 65, true );

// Filter to add custom images sizes in Thumbnail selection box.
add_filter(
	'image_size_names_choose',
	function( $sizes ) {
		return array_merge(
			$sizes,
			apply_filters(
				'wp_review_custom_image_sizes',
				array(
					'wp_review_small' => __( 'WP Review Small', 'wp-review' ),
					'wp_review_large' => __( 'WP Review Large', 'wp-review' ),
				)
			)
		);
	}
);


/**
 * Get the meta box data.
 * Replaced by wp_review_get_review_box() in v2.0
 *
 * @since 1.0
 *
 * @param int $post_id Post ID.
 * @return string
 */
function wp_review_get_data( $post_id = null ) {
	return wp_review_get_review_box( $post_id );
}


/**
 * Injects reviews to content.
 *
 * @param  string $content Post content.
 * @return string
 */
function wp_review_inject_data( $content ) {
	$post_id         = get_the_ID();
	$options         = get_option( 'wp_review_options' );
	$custom_location = get_post_meta( $post_id, 'wp_review_custom_location', true );
	$location        = get_post_meta( $post_id, 'wp_review_location', true );
	if ( ! $custom_location && ! empty( $options['review_location'] ) ) {
		$location = $options['review_location'];
	}

	$location = apply_filters( 'wp_review_location', $location, $post_id );

	if ( ! $location || 'custom' === $location ) {
		return $content;
	}

	if ( ! is_singular() || ! is_main_query() ) {
		return $content;
	}

	if ( ! wp_review_is_amp_page() && ! in_the_loop() ) {
		return $content;
	}

	$review = wp_review_get_review_box();

	if ( 'bottom' == $location ) {
		global $multipage, $numpages, $page;
		if ( $multipage ) {
			if ( $page == $numpages ) {
				return $content . $review;
			} else {
				return $content;
			}
		} else {
			return $content . $review;
		}
	} elseif ( 'top' == $location ) {
		return $review . $content;
	} else {
		return $content;
	}
}
/* Display the meta box data below 'the_content' hook. */
add_filter( 'the_content', 'wp_review_inject_data' );

/**
 * Retrieve only total rating.
 * To be used on archive pages, etc.
 *
 * @since 1.0
 *
 * @param bool   $echo    Echo or not.
 * @param string $class   CSS class.
 * @param int    $post_id Post ID. Use current post ID if empty.
 * @param array  $args    Custom arguments. Use for filter.
 */
function wp_review_show_total( $echo = true, $class = 'review-total-only', $post_id = null, $args = array() ) {
	global $wp_review_rating_types;

	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$type      = wp_review_get_post_review_type( $post_id );
	$user_type = wp_review_get_post_user_review_type( $post_id );
	if ( ! $type && ! $user_type ) {
		return '';
	}

	wp_enqueue_style( 'wp_review-style' );

	// Fix for themes.
	if ( false !== strpos( $class, 'latestPost-review-wrapper' ) ) {
		$args['color']          = '#fff';
		$args['inactive_color'] = '#dedcdc';
	}
	// Fix for rank-math.
	if ( false !== strpos( $class, 'rank-math-snippet' ) ) {
		$args['color']          = '#fff';
		$args['inactive_color'] = '#dedcdc';
	}

	$show_on_thumbnails_type = 'author';
	$show_on_thumbnails_type = apply_filters( 'wp_review_thumbnails_total', $show_on_thumbnails_type, $post_id, $args ); // Will override option.

	$rating = '';
	$total  = '';
	switch ( $show_on_thumbnails_type ) {
		case 'author':
			$total = get_post_meta( $post_id, 'wp_review_total', true );

			if ( in_array( $type, array( 'point', 'percentage' ) ) ) {
				$rating = sprintf( $wp_review_rating_types[ $type ]['value_text'], $total );
			} else {
				$rating = wp_review_rating( $total, $post_id, $args );
			}
			break;

		case 'visitors':
			$total = get_post_meta( $post_id, 'wp_review_user_reviews', true );

			if ( 'point' == $user_type || 'percentage' == $user_type ) {
				$rating = sprintf( $wp_review_rating_types[ $user_type ]['value_text'], $total );
			} else {
				$rating = wp_review_rating( $total, $post_id, array( 'user_rating' => true ) ); // Return just output template.
			}
			break;
	}

	$review = '';
	if ( ! empty( $rating ) && ! empty( $total ) ) {
		$style = '';
		if ( ! empty( $args['context'] ) && 'product-rating' === $args['context'] ) {
			$colors = wp_review_get_colors( $post_id );
			$style .= sprintf( ' background-color: %s;', esc_attr( $colors['color'] ) );
		}

		if ( trim( $style ) ) {
			$style = 'style="' . esc_attr( $style ) . '"';
		}

		$review .= '<div class="review-type-' . $type . ' ' . esc_attr( $class ) . ' wp-review-show-total wp-review-total-' . $post_id . ' wp-review-total-' . $type . '" ' . $style . '> ';
		$review .= $rating;
		$review .= '</div>';
	}

	$review = apply_filters( 'wp_review_show_total', $review, $post_id, $type, $total );
	$review = apply_filters( 'wp_review_total_output', $review, $post_id, $type, $total, $class, $args );

	if ( $echo ) {
		echo $review; // WPCS: xss ok.
	} else {
		return $review;
	}
}


/**
 * Gets post visitor reviews data.
 *
 * @param  int  $post_id Post ID.
 * @param  bool $force   Force fetching from comments instead of post meta.
 * @return array
 */
function mts_get_post_reviews( $post_id, $force = false ) {
	$post_reviews   = get_post_meta( $post_id, 'wp_review_user_reviews', true );
	$review_count   = (int) get_post_meta( $post_id, 'wp_review_review_count', true );
	$positive_count = (int) get_post_meta( $post_id, 'wp_review_positive_count', true );
	$negative_count = (int) get_post_meta( $post_id, 'wp_review_negative_count', true );

	if ( ! $force && $post_reviews && $review_count ) {
		return array(
			'rating'         => $post_reviews,
			'count'          => $review_count,
			'positive_count' => $positive_count,
			'negative_count' => $negative_count,
		);
	}

	if ( is_numeric( $post_id ) && $post_id > 0 ) {
		$comments     = get_comments(
			array(
				'post_id' => $post_id,
				'type'    => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'status'  => 'approve',
			)
		);
		$rating       = array_reduce( $comments, 'wpreview_visitor_ratings_callback', 0 );
		$count        = count( $comments );
		$post_reviews = array(
			'rating' => $count > 0 ? round( $rating / $count, 2 ) : 0,
			'count'  => $count,
		);

		$positive_comments = get_comments(
			array(
				'post_id'    => $post_id,
				'type'       => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'status'     => 'approve',
				'fields'     => 'ids',
				'meta_key'   => WP_REVIEW_VISITOR_RATING_METAKEY,
				'meta_value' => 100,
			)
		);

		$post_reviews['positive_count'] = count( $positive_comments );

		$negative_comments = get_comments(
			array(
				'post_id'    => $post_id,
				'type'       => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'status'     => 'approve',
				'fields'     => 'ids',
				'meta_key'   => WP_REVIEW_VISITOR_RATING_METAKEY,
				'meta_value' => 0,
			)
		);

		$post_reviews['negative_count'] = count( $negative_comments );

		update_post_meta( $post_id, 'wp_review_user_reviews', $post_reviews['rating'] );
		update_post_meta( $post_id, 'wp_review_review_count', $post_reviews['count'] );
		update_post_meta( $post_id, 'wp_review_positive_count', $post_reviews['positive_count'] );
		update_post_meta( $post_id, 'wp_review_negative_count', $post_reviews['negative_count'] );

		return $post_reviews;
	}
}


/**
 * Gets post comment reviews data.
 *
 * @param  int  $post_id Post ID.
 * @param  bool $force   Force fetching from comments instead of post meta.
 * @return array
 */
function mts_get_post_comments_reviews( $post_id, $force = false ) {
	$post_reviews   = get_post_meta( $post_id, 'wp_review_comments_rating_value', true );
	$review_count   = (int) get_post_meta( $post_id, 'wp_review_comments_rating_count', true );
	$positive_count = (int) get_post_meta( $post_id, 'wp_review_comments_positive_count', true );
	$negative_count = (int) get_post_meta( $post_id, 'wp_review_comments_negative_count', true );

	if ( ! $force && $post_reviews && $review_count ) {
		return array(
			'rating'         => $post_reviews,
			'count'          => $review_count,
			'positive_count' => $positive_count,
			'negative_count' => $negative_count,
		);
	}

	if ( is_numeric( $post_id ) && $post_id > 0 ) {
		$comments     = get_comments(
			array(
				'post_id' => $post_id,
				'type'    => WP_REVIEW_COMMENT_TYPE_COMMENT,
				'status'  => 'approve',
			)
		);
		$rating       = array_reduce( $comments, 'wpreview_comment_ratings_callback', 0 );
		$count        = count( $comments );
		$post_reviews = array(
			'rating' => $count > 0 ? round( $rating / $count, 2 ) : 0,
			'count'  => $count,
		);

		$positive_comments = get_comments(
			array(
				'post_id'    => $post_id,
				'type'       => WP_REVIEW_COMMENT_TYPE_COMMENT,
				'status'     => 'approve',
				'fields'     => 'ids',
				'meta_key'   => WP_REVIEW_COMMENT_RATING_METAKEY,
				'meta_value' => 100,
			)
		);

		$post_reviews['positive_count'] = count( $positive_comments );

		$negative_comments = get_comments(
			array(
				'post_id'    => $post_id,
				'type'       => WP_REVIEW_COMMENT_TYPE_COMMENT,
				'status'     => 'approve',
				'fields'     => 'ids',
				'meta_key'   => WP_REVIEW_COMMENT_RATING_METAKEY,
				'meta_value' => 0,
			)
		);

		$post_reviews['negative_count'] = count( $negative_comments );

		update_post_meta( $post_id, 'wp_review_comments_rating_value', $post_reviews['rating'] );
		update_post_meta( $post_id, 'wp_review_comments_rating_count', $post_reviews['count'] );
		update_post_meta( $post_id, 'wp_review_comments_positive_count', $post_reviews['positive_count'] );
		update_post_meta( $post_id, 'wp_review_comments_negative_count', $post_reviews['negative_count'] );

		return $post_reviews;
	}
}


/**
 * Gets post visitor feature reviews data.
 *
 * @param  int    $post_id Post ID.
 * @param  bool   $force   Force fetching from comments instead of post meta.
 * @param  string $type    Accepts `user` or `comment`.
 * @return array
 */
function wp_review_get_post_feature_reviews( $post_id = null, $force = false, $type = 'user' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post_meta_key = "wp_review_{$type}_feature_reviews";
	$reviews       = get_post_meta( $post_id, $post_meta_key, true );
	if ( ! $force && $reviews ) {
		return $reviews;
	}

	$features = wp_review_get_review_items( $post_id );
	if ( ! $features ) {
		return array();
	}
	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'type'    => 'comment' === $type ? WP_REVIEW_COMMENT_TYPE_COMMENT : WP_REVIEW_COMMENT_TYPE_VISITOR,
			'status'  => 'approve',
		)
	);
	$reviews  = array();
	foreach ( $features as $value ) {
		$reviews[ $value['id'] ] = array(
			'total'    => 0,
			'count'    => 0,
			'positive' => 0,
			'negative' => 0,
		);
	}

	foreach ( $comments as $comment ) {
		$data = get_comment_meta( $comment->comment_ID, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
		if ( ! $data ) {
			continue;
		}
		foreach ( $data as $key => $value ) {
			if ( empty( $reviews[ $key ] ) ) {
				continue;
			}
			$reviews[ $key ]['total'] += $value;
			if ( 100 == $value ) {
				$reviews[ $key ]['positive']++;
			} else {
				$reviews[ $key ]['negative']++;
			}
			$reviews[ $key ]['count']++;
		}
	}

	update_post_meta( $post_id, $post_meta_key, $reviews );
	return $reviews;
}


/**
 * Clears cached when modify comment.
 *
 * @since 3.0.0
 *
 * @param WP_Comment $comment    Comment object.
 */
function wp_review_clear_cached_reviews( $comment ) {
	delete_post_meta( $comment->comment_post_ID, 'wp_review_comment_feature_reviews' );
	delete_post_meta( $comment->comment_post_ID, 'wp_review_comments_rating_value' );
	delete_post_meta( $comment->comment_post_ID, 'wp_review_comments_rating_count' );
	delete_post_meta( $comment->comment_post_ID, 'wp_review_user_feature_reviews' );
	delete_post_meta( $comment->comment_post_ID, 'wp_review_user_reviews' );
	delete_post_meta( $comment->comment_post_ID, 'wp_review_review_count' );
}


/**
 * Runs something when change comment status.
 *
 * @since 3.0.0
 *
 * @param string     $new_status New status.
 * @param string     $old_status Old status.
 * @param WP_Comment $comment    Comment object.
 */
function wp_review_on_change_comment_status( $new_status, $old_status, $comment ) {
	wp_review_clear_cached_reviews( $comment );
}
add_action( 'transition_comment_status', 'wp_review_on_change_comment_status', 10, 3 );

/**
 * Comments rating callback for array_reduce().
 *
 * @param float  $carry   Rating.
 * @param object $comment Comment object.
 * @return float
 */
function wpreview_comment_ratings_callback( $carry, $comment ) {
	$rating = get_comment_meta( $comment->comment_ID, WP_REVIEW_COMMENT_RATING_METAKEY, true );
	$carry += floatval( $rating );
	return $carry;
}

/**
 * Visitors rating callback for array_reduce().
 *
 * @param float  $carry   Rating.
 * @param object $comment Comment object.
 * @return float
 */
function wpreview_visitor_ratings_callback( $carry, $comment ) {
	$rating = get_comment_meta( $comment->comment_ID, WP_REVIEW_VISITOR_RATING_METAKEY, true );
	$carry += floatval( $rating );
	return $carry;
}


/**
 * Check if user has reviewed this post previously.
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $ip      User IP.
 * @param string $type    Rating type.
 * @return bool
 */
function hasPreviousReview( $post_id, $user_id, $ip, $type = 'any' ) { // phpcs:ignore
	_deprecated_function( __FUNCTION__, '3.0.0', 'wp_review_has_reviewed' );
	return wp_review_has_reviewed( $post_id, $user_id, $ip, $type );
}


/**
 * Check if user has reviewed this post previously.
 *
 * @since 3.0.0
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $ip      User IP.
 * @param string $type    Rating type.
 * @return bool
 */
function wp_review_has_reviewed( $post_id, $user_id, $ip = null, $type = 'any' ) {
	if ( ! $ip ) {
		$ip = wp_review_get_user_ip();
	}

	if ( is_user_logged_in() && wp_review_option( 'multi_reviews_per_account' ) ) {
		return false; // Allow multiple reviews per account.
	}

	if ( is_user_logged_in() ) {
		return wp_review_has_reviewed_by_user_id( $post_id, $user_id, $ip, $type );
	}

	return wp_review_has_reviewed_by_ip( $post_id, $user_id, $ip, $type );
}


/**
 * Check if user has reviewed this post previously by user id.
 *
 * @since 3.0.0
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $ip      User IP.
 * @param string $type    Rating type.
 * @return bool
 */
function wp_review_has_reviewed_by_user_id( $post_id, $user_id, $ip, $type = 'any' ) {
	if ( ! $user_id ) {
		return false;
	}
	$args = array(
		'post_id' => $post_id,
		'count'   => true,
		'user_id' => $user_id,
	);
	if ( 'any' === $type ) {
		$args['type_in'] = array( WP_REVIEW_COMMENT_TYPE_COMMENT, WP_REVIEW_COMMENT_TYPE_VISITOR );
	} else {
		$args['type'] = $type;
	}
	$count = intval( get_comments( $args ) );
	return $count > 0;
}


/**
 * Check if user has reviewed this post previously by ip address.
 *
 * @since 3.0.0
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $ip      User IP.
 * @param string $type    Rating type.
 * @return bool
 */
function wp_review_has_reviewed_by_ip( $post_id, $user_id, $ip, $type = 'any' ) {
	$args = array(
		'post_id' => $post_id,
		'count'   => true,
	);
	if ( 'any' === $type ) {
		$args['type_in'] = array( WP_REVIEW_COMMENT_TYPE_COMMENT, WP_REVIEW_COMMENT_TYPE_VISITOR );
	} else {
		$args['type'] = $type;
	}
	set_query_var( 'wp_review_ip', $ip );
	add_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
	$count = intval( get_comments( $args ) );
	remove_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
	return $count > 0;
}


/**
 * Gets previous review comment.
 *
 * @since 3.0.0
 *
 * @param  WP_Comment $comment Comment object.
 * @return WP_Comment|false
 */
function wp_review_get_previous_review_comment( $comment ) {
	$post_id    = $comment->comment_post_ID;
	$query_args = array(
		'post_id'      => $post_id,
		'type_in'      => array( WP_REVIEW_COMMENT_TYPE_COMMENT ),
		'meta_key'     => WP_REVIEW_COMMENT_RATING_METAKEY,
		'meta_value'   => 0,
		'meta_compare' => '>',
	);

	if ( intval( $comment->user_id ) ) {
		$query_args['user_id'] = $comment->user_id;
	} else {
		set_query_var( 'wp_review_ip', $comment->comment_author_IP );
		add_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
	}

	$comments = get_comments( $query_args );

	remove_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );

	if ( ! $comments ) {
		return false;
	}
	return $comments[0];
}


/**
 * Add the comment type to comment query.
 *
 * @param WP_Comment_Query $query Comment query.
 * @return WP_Comment_Query
 */
function wp_review_add_comment_type_to_query( WP_Comment_Query $query ) {
	$commenttype = get_query_var( 'wp_review_commenttype' );
	if ( 'any' === $commenttype ) {
		$query->query_vars['type__in'] = array( WP_REVIEW_COMMENT_TYPE_COMMENT, WP_REVIEW_COMMENT_TYPE_VISITOR );
	} else {
		$query->query_vars['type'] = $commenttype;
	}
	return $query;
}

/**
 * Add a conditional to filter the comment query by IP.
 *
 * @param array $clauses Where clauses.
 * @return array
 */
function wp_review_filter_comment_by_ip( array $clauses ) {
	global $wpdb;
	$clauses['where'] .= $wpdb->prepare( ' AND comment_author_IP = %s', get_query_var( 'wp_review_ip' ) );
	return $clauses;
}

/**
 * Gets previous preview.
 *
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $ip      IP Address.
 * @param string $type    Review type.
 * @return bool
 */
function getPreviousReview( $post_id, $user_id, $ip, $type = 'any' ) { // phpcs:ignore
	if ( is_numeric( $post_id ) && $post_id > 0 ) {
		$args = array(
			'post_id' => $post_id,
			'user_id' => 0,
		);
		set_query_var( 'wp_review_commenttype', $type );
		add_filter( 'pre_get_comments', 'wp_review_add_comment_type_to_query' );
		if ( $user_id ) {
			$args['user_id'] = array( $user_id );
		} else {
			set_query_var( 'wp_review_ip', $ip );
			add_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
		}

		$comment = get_comments( $args );
		remove_filter( 'pre_get_comments', 'wp_review_add_comment_type_to_query' );
		remove_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );

		if ( ! empty( $comment ) ) {
			return get_comment_meta( $comment[0]->comment_ID, WP_REVIEW_COMMENT_RATING_METAKEY, true );
		}
	}
	return false;
}

/**
 * Sets theme defaults.
 *
 * @param array $new_options  New options.
 * @param bool  $force_change Force changes.
 */
function wp_review_theme_defaults( $new_options, $force_change = false ) {
	global $pagenow;
	$opt_name = 'wp_review_options_' . wp_get_theme();
	$options  = get_option( 'wp_review_options' );
	if ( empty( $options ) ) {
		$options = array();
	}
	$options_updated = get_option( $opt_name );
	// If the theme was just activated OR options weren't updated yet.
	if ( empty( $options_updated ) || $options_updated != $new_options || $force_change || ( isset( $_GET['activated'] ) && 'themes.php' == $pagenow ) ) {
		update_option( 'wp_review_options', array_merge( $options, $new_options ) );
		update_option( $opt_name, $new_options );
	}
}

/**
 * Gets all image sizes.
 *
 * @return array
 */
function wp_review_get_all_image_sizes() {
	global $_wp_additional_image_sizes;

	$default_image_sizes = array( 'thumbnail', 'medium', 'large' );

	foreach ( $default_image_sizes as $size ) {
		$image_sizes[ $size ]['width']  = intval( get_option( "{$size}_size_w" ) );
		$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
		$image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}

	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}

	return $image_sizes;
}

/**
 * Exclude review-type comments from being included in the comment query.
 *
 * @param WP_Comment_Query $query Comment query.
 */
function wp_review_exclude_review_comments( WP_Comment_Query $query ) {
	if ( ! is_admin() && ( WP_REVIEW_COMMENT_TYPE_VISITOR !== $query->query_vars['type'] && ! in_array( WP_REVIEW_COMMENT_TYPE_VISITOR, (array) $query->query_vars['type__in'] ) ) ) {
		$query->query_vars['type__not_in'] = array_merge(
			is_array( $query->query_vars['type__not_in'] ) ? $query->query_vars['type__not_in'] : array(),
			array( WP_REVIEW_COMMENT_TYPE_VISITOR )
		);
	}
}
add_action( 'pre_get_comments', 'wp_review_exclude_review_comments', 15 );

/**
 * Add "Reviews" to comments table view.
 *
 * @param array $comment_types Comment types.
 * @return mixed
 */
function wp_review_add_to_comment_table_dropdown( $comment_types ) {
	$comment_types[ WP_REVIEW_COMMENT_TYPE_COMMENT ] = __( 'Comment Reviews', 'wp-review' );
	$comment_types[ WP_REVIEW_COMMENT_TYPE_VISITOR ] = __( 'Visitor Reviews', 'wp-review' );

	return $comment_types;
}
add_filter( 'admin_comment_types_dropdown', 'wp_review_add_to_comment_table_dropdown' );

/**
 * Gets user rating type.
 *
 * @since 3.0.0 Combine with global option.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 *
 * 0 - Disabled
 * 1 - Visitor Rating Only
 * 2 - Comment Rating Only
 * 3 - Both
 */
function wp_review_get_user_rating_setup( $post_id ) {
	$default      = wp_review_option( 'default_user_review_type', WP_REVIEW_REVIEW_DISABLED );
	$user_reviews = get_post_meta( $post_id, 'wp_review_userReview', true );
	$enabled      = '' === $user_reviews ? $default : (int) $user_reviews;
	if ( is_array( $user_reviews ) ) {
		$enabled = (int) $user_reviews[0];
	}

	// Reviews through comments: enabled by default.
	$review_through_comment = get_post_meta( $post_id, 'wp_review_through_comment', true );
	$custom_fields          = get_post_custom();
	if ( ! isset( $custom_fields['wp_review_through_comment'] ) ) {
		$review_through_comment = 0;
	}
	// Compatibility with the old option.
	if ( 1 === $enabled ) {
		if ( $review_through_comment ) {
			$enabled = WP_REVIEW_REVIEW_ALLOW_BOTH;
		} else {
			$enabled = WP_REVIEW_REVIEW_VISITOR_ONLY;
		}
	}

	return $enabled;
}

/**
 * Exclude visitor ratings when updating a post's comment count.
 *
 * @param int    $post_id Post ID.
 * @param object $new     New comment.
 * @param object $old     Old comment.
 *
 * @internal param $comment_id
 * @internal param $comment
 */
function wp_review_exclude_visitor_review_count( $post_id, $new, $old ) {
	global $wpdb;
	$count = get_comments(
		array(
			'type__not_in' => array( WP_REVIEW_COMMENT_TYPE_VISITOR ),
			'post_id'      => $post_id,
			'count'        => true,
		)
	);
	$wpdb->update( $wpdb->posts, array( 'comment_count' => $count ), array( 'ID' => $post_id ) );

	// Update user review count.
	mts_get_post_reviews( $post_id, true );

	clean_post_cache( $post_id );
}
add_action( 'wp_update_comment_count', 'wp_review_exclude_visitor_review_count', 10, 3 );

/**
 * Get the schema type of a review.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function wp_review_get_review_schema( $post_id ) {
	$schema  = get_post_meta( $post_id, 'wp_review_schema', true );
	$schemas = wp_review_schema_types();

	if ( empty( $schema ) || ! isset( $schemas[ $schema ] ) ) {
		$schema = 'Thing';
	}

	return $schema;
}

/**
 * Get the IP of the current user.
 *
 * @return string
 */
function wp_review_get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

/**
 * Get the HTML for user reviews in review box.
 *
 * @param int  $post_id       Post ID.
 * @param bool $votable       Voteable or not.
 * @param bool $force_display Force display or not.
 * @return string
 */
function wp_review_user_review( $post_id, $votable = true, $force_display = false ) {
	$review = '';

	if ( ! $force_display && ! in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_VISITOR_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) ) ) {
		return $review;
	}

	$allowed_class       = 'allowed-to-rate';
	$has_not_rated_class = ' has-not-rated-yet';
	$post_reviews        = mts_get_post_reviews( $post_id );
	$user_total          = $post_reviews['rating'];
	$users_reviews_count = $post_reviews['count'];
	$positive_rating     = $post_reviews['positive_count'];
	$negative_rating     = $post_reviews['negative_count'];
	$total               = get_post_meta( $post_id, 'wp_review_total', true );
	$type                = get_post_meta( $post_id, 'wp_review_user_review_type', true );

	$options = get_option( 'wp_review_options' );
	$colors  = wp_review_get_colors( $post_id );
	$color   = $colors['color'];

	$user_id = '';
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( '' == $user_total ) {
		$user_total = '0.0';
	}
	$value = $user_total;

	if ( ! $votable || wp_review_has_reviewed( $post_id, $user_id, wp_review_get_user_ip(), WP_REVIEW_COMMENT_TYPE_VISITOR ) || ( ! is_user_logged_in() && ! empty( $options['registered_only'] ) ) ) {
		$has_not_rated_class = '';
	}

	$class = $allowed_class . $has_not_rated_class;

	$template = mts_get_template_path( $type, 'star-output' );
	set_query_var( 'rating', compact( 'value', 'users_reviews_count', 'user_id', 'class', 'post_id', 'color', 'colors', 'positive_rating', 'negative_rating' ) );
	ob_start();
	load_template( $template, false );
	$review = ob_get_contents();
	ob_end_clean();

	if ( '0.0' !== $user_total && '' === $total ) { // Dont'show if no user ratings and there is review.
		$review .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
							<meta itemprop="ratingValue" content="' . $user_total . '" />
							<meta itemprop="reviewCount" content="' . $users_reviews_count . '" />
						</div>';
	}

	return $review;
}


/**
 * Get the path to a rating template prioritizing theme directory first.
 *
 * @param string $type    Rating type.
 * @param string $default Default rating type.
 *
 * @return string
 */
function mts_get_template_path( $type, $default = 'star' ) {
	$template = get_stylesheet_directory() . '/wp-review/' . $type . '.php';

	// Template does not exist on theme dir, use plugin dir.
	if ( ! file_exists( $template ) ) {
		$template = WP_REVIEW_DIR . 'rating-types/' . $type . '.php';
	}

	// Template does not exist, fallback to star.
	if ( ! file_exists( $template ) ) {
		$template = WP_REVIEW_DIR . 'rating-types/' . $default . '.php';
	}

	return $template;
}


/*
 * Custom Rating Types
 *
 */
$wp_review_rating_types = array();


/**
 * Registers rating type.
 *
 * @param  string $rating_type Rating type name.
 * @param  array  $args        Rating type args.
 * @return bool
 */
function wp_review_register_rating_type( $rating_type, $args ) {
	global $wp_review_rating_types;

	if ( empty( $args['output_template'] ) && empty( $args['template'] ) ) {
		return false;
	}

	/*
	 * If it has combined 'template'
	 * or 'input_template' (for user rating).
	 */
	$args['user_rating'] = ! empty( $args['template'] ) || ! empty( $args['input_template'] );

	$wp_review_rating_types[ $rating_type ] = $args;

	return true;
}

/**
 * Registers default rating types.
 */
function wp_review_register_default_rating_types() {
	wp_review_register_rating_type(
		'star',
		array(
			'label'               => __( 'Star', 'wp-review' ),
			'max'                 => 5,
			'decimals'            => 1,
			// translators: rating value text.
			'value_text'          => _x( '%s Stars', 'star rating value text', 'wp-review' ),
			// translators: rating value singular text.
			'value_text_singular' => _x( '%s Star', 'star rating value text singular', 'wp-review' ),
			'input_template'      => WP_REVIEW_DIR . 'rating-types/star-input.php',
			'output_template'     => WP_REVIEW_DIR . 'rating-types/star-output.php',
		)
	);

	wp_review_register_rating_type(
		'point',
		array(
			'label'               => __( 'Point', 'wp-review' ),
			'max'                 => 10,
			'decimals'            => 1,
			// translators: rating value text.
			'value_text'          => _x( '%s/10', 'point rating value text', 'wp-review' ),
			// translators: rating value singular text.
			'value_text_singular' => _x( '%s/10', 'point rating value text singular', 'wp-review' ),
			'input_template'      => WP_REVIEW_DIR . 'rating-types/point-input.php',
			'output_template'     => WP_REVIEW_DIR . 'rating-types/point-output.php',
		)
	);

	wp_review_register_rating_type(
		'percentage',
		array(
			'label'               => __( 'Percentage', 'wp-review' ),
			'max'                 => 100,
			'decimals'            => 1,
			// translators: rating value text.
			'value_text'          => _x( '%s%%', 'percentage rating value text', 'wp-review' ),
			// translators: rating value singular text.
			'value_text_singular' => _x( '%s%%', 'percentage rating value text singular', 'wp-review' ),
			'input_template'      => WP_REVIEW_DIR . 'rating-types/percentage-input.php',
			'output_template'     => WP_REVIEW_DIR . 'rating-types/percentage-output.php',
		)
	);

	wp_review_register_rating_type(
		'circle',
		array(
			'label'               => __( 'Circle', 'wp-review' ),
			'max'                 => 100,
			'decimals'            => 2,
			// translators: rating value text.
			'value_text'          => _x( '%s', 'circle rating value text', 'wp-review' ), // phpcs:ignore
			// translators: rating value singular text.
			'value_text_singular' => _x( '%s', 'circle rating value text singular', 'wp-review' ), // phpcs:ignore
			'input_template'      => WP_REVIEW_DIR . 'rating-types/circle-input.php',
			'output_template'     => WP_REVIEW_DIR . 'rating-types/circle-output.php',
		)
	);

	wp_review_register_rating_type(
		'thumbs',
		array(
			'label'               => __( 'Thumbs', 'wp-review' ),
			'max'                 => 100,
			'decimals'            => 0,
			// translators: rating value text.
			'value_text'          => _x( '%s/100', 'thumbs rating value text', 'wp-review' ),
			// translators: rating value singular text.
			'value_text_singular' => _x( '%s/100', 'thumbs rating value text singular', 'wp-review' ),
			'input_template'      => WP_REVIEW_DIR . 'rating-types/thumbs-input.php',
			'output_template'     => WP_REVIEW_DIR . 'rating-types/thumbs-output.php',
		)
	);
}
add_action( 'init', 'wp_review_register_default_rating_types' );


/**
 * Gets rating types.
 *
 * @return array
 */
function wp_review_get_rating_types() {
	global $wp_review_rating_types;
	return apply_filters( 'wp_review_rating_types', $wp_review_rating_types );
}


/**
 * Gets rating type data.
 *
 * @since 3.0.0
 *
 * @param  string $type Rating type name.
 * @return array|false
 */
function wp_review_get_rating_type_data( $type ) {
	if ( ! $type ) {
		return false;
	}
	$rating_types = wp_review_get_rating_types();
	if ( ! isset( $rating_types[ $type ] ) ) {
		return false;
	}
	return wp_parse_args(
		$rating_types[ $type ],
		array(
			'label'               => '',
			'max'                 => 5,
			'decimals'            => 0,
			'value_text'          => '',
			'value_text_singular' => '',
			'input_template'      => '',
			'output_template'     => '',
		)
	);
}


/**
 * Gets post review type.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return string Empty string if no review.
 */
function wp_review_get_post_review_type( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$type = get_post_meta( $post_id, 'wp_review_type', true );
	if ( '' === $type ) {
		$type = wp_review_option( 'review_type', 'none' );
	}

	$rating_types = wp_review_get_rating_types();

	if ( 'none' === $type ) {
		$type = '';
	}

	if ( $type && ! isset( $rating_types[ $type ] ) ) {
		$type = 'star';
	}

	return apply_filters( 'wp_review_get_review_type', $type, $post_id );
}


/**
 * Gets user review type for post.
 *
 * @param  int $post_id Post ID.
 * @return string       Empty string if no review.
 */
function wp_review_get_post_user_review_type( $post_id = null ) {
	$rating_types = wp_review_get_rating_types();
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$type = wp_review_get_post_review_type( $post_id );
	if ( empty( $type ) ) {
		return ''; // Not a review.
	}

	$type = get_post_meta( $post_id, 'wp_review_user_review_type', true );

	$user_rating_setup = wp_review_get_user_rating_setup( $post_id );
	$user_reviews      = in_array( $user_rating_setup, array( WP_REVIEW_REVIEW_VISITOR_ONLY, WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );
	if ( ! $user_reviews ) {
		return ''; // User ratings not enabled.
	}

	if ( empty( $rating_types[ $type ]['user_rating'] ) ) {
		$type = 'star'; // Fallback if specific $type is not available.
	}

	return apply_filters( 'wp_review_get_user_review_type', $type, $post_id );
}

/**
 * Custom Box Templates.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function wp_review_get_post_box_template( $post_id = null ) {
	global $post;

	if ( empty( $post_id ) ) {
		$post_id = $post->ID;
	}

	$template  = wp_review_get_box_template( $post_id );
	$template .= '.php';
	if ( empty( $template ) || ! wp_review_locate_box_template( $template ) ) {
		$template = 'default.php'; // fallback to default.php.
	}

	return apply_filters( 'wp_review_get_box_template', $template, $post_id );
}

/**
 * Gets template file path.
 *
 * @param string $template_name    Template file name.
 * @param bool   $return_full_path Return full path or not.
 * @return string
 */
function wp_review_locate_box_template( $template_name, $return_full_path = true ) {
	// We look for box templates in:
	// 1. plugins_dir/box-templates
	// 2. theme_dir/wp-review
	// 3. childtheme_dir/wp-review
	// 4... Use filter to add more.
	$default_paths  = array(
		WP_REVIEW_DIR . 'box-templates',
		get_template_directory() . '/wp-review',
		get_stylesheet_directory() . '/wp-review',
	);
	$template_paths = apply_filters( 'wp_review_box_template_paths', $default_paths );

	$paths        = array_reverse( $template_paths );
	$located      = '';
	$path_partial = '';
	foreach ( $paths as $path ) {
		$full_path = trailingslashit( $path ) . $template_name;
		if ( file_exists( $full_path ) ) {
			$located      = $full_path;
			$path_partial = $path;
			break;
		}
	}
	return $return_full_path ? $located : $path_partial;
}


/**
 * Locates template.
 * Is an alias of {@see wp_review_locate_box_template()}.
 *
 * @since 3.0.0
 * @see wp_review_locate_box_template()
 *
 * @param  string $template_name    Template name with extension and folders.
 * @param  bool   $return_full_path Return full path.
 * @return string
 */
function wp_review_locate_template( $template_name, $return_full_path = true ) {
	return wp_review_locate_box_template( $template_name, $return_full_path );
}


/**
 * Loads template.
 *
 * @since 3.0.0
 *
 * @param string $template_name Template name with extension and folders.
 * @param array  $data          Data passed to template file.
 */
function wp_review_load_template( $template_name, $data = array() ) {
	$path = wp_review_locate_template( $template_name, true );
	if ( $path ) {
		extract( $data ); // phpcs:ignore
		include $path; // phpcs:ignore
	}
}


/**
 * Shows rating using output template
 *
 * @param  float $value   Rating value.
 * @param  int   $post_id Optional post ID.
 * @param  array $args    Custom args.
 * @return string
 */
function wp_review_rating( $value, $post_id = null, $args = array() ) {
	global $post;

	if ( ! empty( $args['user_rating'] ) ) {
		$type = wp_review_get_post_user_review_type( $post_id );
	} else {
		$type = wp_review_get_post_review_type( $post_id );
	}

	if ( ! $type ) {
		return '';
	}

	if ( empty( $post_id ) ) {
		$post_id = $post->ID;
	}

	$rating_type = wp_review_get_rating_type_data( $type );

	$colors = wp_review_get_colors( $post_id );
	$colors = array_merge( $colors, $args );

	if ( ! empty( $args['bar_text_color_from'] ) && isset( $colors[ $args['bar_text_color_from'] ] ) ) {
		$colors['bar_text_color'] = $colors[ $args['bar_text_color_from'] ];
	}

	$colors = apply_filters( 'wp_review_colors', $colors, $post_id );
	$color  = $colors['color'];

	// Don't allow higher rating than max.
	if ( $value > $rating_type['max'] ) {
		$value = $rating_type['max'];
	}
	$template       = $rating_type['output_template'];
	$comment_rating = false;
	set_query_var( 'rating', compact( 'value', 'post_id', 'type', 'args', 'comment_rating', 'color', 'colors' ) );
	ob_start();
	load_template( $template, false );
	$review = ob_get_contents();
	ob_end_clean();
	return $review;
}

/**
 * Gets user rating.
 *
 * @param int   $post_id Post ID.
 * @param array $args    Custom args.
 * @return string
 */
function wp_review_user_rating( $post_id = null, $args = array() ) {
	$options = get_option( 'wp_review_options' );
	$type    = wp_review_get_post_user_review_type( $post_id );
	if ( empty( $type ) ) {
		return '';
	}

	$rating_type = wp_review_get_rating_type_data( $type );

	$post_reviews           = mts_get_post_reviews( $post_id );
	$value                  = ! empty( $post_reviews['rating'] ) ? $post_reviews['rating'] : '0.0';
	$args['positive_count'] = isset( $post_reviews['positive_count'] ) ? $post_reviews['positive_count'] : 0;
	$args['negative_count'] = isset( $post_reviews['negative_count'] ) ? $post_reviews['negative_count'] : 0;

	$user_id = '';
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( wp_review_has_reviewed( $post_id, $user_id, wp_review_get_user_ip(), WP_REVIEW_COMMENT_TYPE_VISITOR ) || ( ! is_user_logged_in() && ! empty( $options['registered_only'] ) ) ) {
		$output = wp_review_rating(
			$value,
			$post_id,
			array(
				'user_rating'    => true,
				'positive_count' => $args['positive_count'],
				'negative_count' => $args['negative_count'],
			)
		); // Return just output template.
		return $output;
	}

	$colors = wp_review_get_colors( $post_id );
	$color  = $colors['color'];

	$rating_type_template = $rating_type['input_template'];
	$comment_rating       = false;
	set_query_var( 'rating', compact( 'value', 'post_id', 'comment_rating', 'args', 'color', 'colors' ) );
	ob_start();
	load_template( $rating_type_template, false );
	$review = '<div class="wp-review-user-rating wp-review-user-rating-' . $type . '">' . ob_get_contents() . '</div>';
	ob_end_clean();

	return $review;
}


/**
 * Shows visitor features rating.
 *
 * @since 3.0.0
 *
 * @param  int   $post_id Post ID.
 * @param  array $args    Custom arguments.
 * @return string
 */
function wp_review_visitor_feature_rating( $post_id = null, $args = array() ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$args = wp_parse_args( $args, array( 'type' => 'user' ) );

	$type        = wp_review_get_post_user_review_type( $post_id );
	$rating_type = wp_review_get_rating_type_data( $type );
	if ( empty( $rating_type ) ) {
		return '';
	}

	$colors = wp_review_get_colors( $post_id );
	$color  = $colors['color'];

	$user_has_reviewed    = wp_review_has_reviewed( $post_id, get_current_user_id(), wp_review_get_user_ip(), 'comment' === $args['type'] ? WP_REVIEW_COMMENT_TYPE_COMMENT : WP_REVIEW_COMMENT_TYPE_VISITOR );
	$is_output            = $user_has_reviewed;
	$rating_type_template = $is_output ? $rating_type['output_template'] : $rating_type['input_template'];
	$comment_rating       = false;

	$args['hide_button'] = true;

	$output  = sprintf(
		'<div class="wpr-%1$s-features-rating" data-type="%2$s" data-nonce="%3$s" data-post_id="%4$s">',
		esc_attr( $args['type'] ),
		esc_attr( $type ),
		esc_attr( wp_create_nonce( 'wpr_user_features_rating' ) ),
		intval( $post_id )
	);
	$output .= '<h5 class="user-review-title">' . __( 'User Review', 'wp-review' ) . '</h5>';
	$output .= '<ul class="features-rating-list review-list">';

	$features = wp_review_get_review_items( $post_id );
	$reviews  = wp_review_get_post_feature_reviews( $post_id, false, $args['type'] );
	if ( is_array( $features ) && ! empty( $features ) ) {
		foreach ( $features as $feature_id => $feature ) {
			if ( ! isset( $reviews[ $feature_id ] ) ) {
				$review = array(
					'total'    => 0,
					'count'    => 0,
					'positive' => 0,
					'negative' => 0,
				);
			} else {
				$review = $reviews[ $feature_id ];
			}

			$value = 0;
			if ( $is_output ) {
				$value = intval( $review['count'] ) ? $review['total'] / $review['count'] : 0;
			}
			$value_text = '<span>' . sprintf( $rating_type['value_text'], $value ) . '</span> - ';
			$output    .= '<li>';

			$rating_output = '';
			$title_output  = '';

			if ( ! $user_has_reviewed ) {
				$rating_output .= sprintf(
					'<div class="wp-review-%1$s-rating wp-review-user-feature-rating-%2$s" data-feature-id="%3$s">',
					esc_attr( $args['type'] ),
					esc_attr( $type ),
					esc_attr( $feature_id )
				);
			}

			if ( ! empty( $feature['wp_review_item_color'] ) ) {
				$color           = $feature['wp_review_item_color'];
				$colors['color'] = $color;
			}

			if ( ! empty( $feature['wp_review_item_inactive_color'] ) ) {
				$inactive_color           = $feature['wp_review_item_inactive_color'];
				$colors['inactive_color'] = $inactive_color;
			}

			$args['positive_count'] = ! empty( $review['positive'] ) ? $review['positive'] : 0;
			$args['negative_count'] = ! empty( $review['negative'] ) ? $review['negative'] : 0;

			set_query_var( 'rating', compact( 'value', 'post_id', 'comment_rating', 'color', 'colors', 'feature_id', 'args' ) );
			ob_start();
			load_template( $rating_type_template, false );
			$rating_output .= ob_get_clean();

			if ( ! $user_has_reviewed ) {
				$rating_output .= '</div>';
			}

			if ( ! in_array( $type, array( 'star' ) ) && $user_has_reviewed ) {
				$title_output .= '<span>' . wp_kses_post( $feature['wp_review_item_title'] . $value_text ) . '</span>';
			} else {
				$title_output .= '<span>' . wp_kses_post( $feature['wp_review_item_title'] ) . '</span>';
			}

			if ( ! empty( $args['title_first'] ) || ! isset( $args['title_first'] ) && 'star' === $type ) {
				// Star rating is rendered after by default.
				$output .= $title_output;
				$output .= $rating_output;
			} else {
				$output .= $rating_output;
				$output .= $title_output;
			}

			$output .= '</li>';
		}
	}
	$output .= '</ul>';
	$output .= sprintf(
		'<button type="button" class="wpr-rating-accept-btn" style="display: none;" disabled>%s</button>',
		esc_html__( 'Submit', 'wp-review' )
	);
	$output .= '</div><!-- End .wpr-user-features-rating -->';
	return $output;
}

/**
 * Gets user comments rating.
 *
 * @param int   $post_id Post ID.
 * @param array $args    Custom args.
 * @return string
 */
function wp_review_user_comments_rating( $post_id = null, $args = array() ) {
	$type = wp_review_get_post_user_review_type( $post_id );

	if ( 'none' === $type ) {
		return '';
	}

	$post_reviews           = mts_get_post_comments_reviews( $post_id );
	$value                  = $post_reviews['rating'];
	$args['positive_count'] = isset( $post_reviews['positive_count'] ) ? $post_reviews['positive_count'] : 0;
	$args['negative_count'] = isset( $post_reviews['negative_count'] ) ? $post_reviews['negative_count'] : 0;
	$args['user_rating']    = true;
	$args['comment_rating'] = true;

	if ( '' == $value ) {
		$value = '0.0';
	}

	return wp_review_rating( $value, $post_id, $args ); // Return just output template.
}


global $wp_embed;
// Not use the_content filter.
add_filter( 'wp_review_desc', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'wp_review_desc', array( $wp_embed, 'autoembed' ), 8 );
add_filter( 'wp_review_desc', 'wptexturize' );
add_filter( 'wp_review_desc', 'convert_smilies', 20 );
add_filter( 'wp_review_desc', 'wpautop' );
add_filter( 'wp_review_desc', 'shortcode_unautop' );
add_filter( 'wp_review_desc', 'do_shortcode', 11 );

/**
 * Gets review data.
 *
 * @since 3.0.0
 *
 * @param  int   $post_id Post ID. Default is current post ID.
 * @param  array $args    Custom arguments. Use for filtering result.
 * @return array
 */
function wp_review_get_review_data( $post_id = null, $args = array() ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$data = array();

	$data['post_id'] = $post_id;

	$data['heading'] = get_post_meta( $post_id, 'wp_review_heading', true );

	$desc_title = get_post_meta( $post_id, 'wp_review_desc_title', true );
	if ( ! $desc_title ) {
		$desc_title = __( 'Summary', 'wp-review' );
	}
	$data['desc_title'] = $desc_title;

	$data['desc'] = get_post_meta( $post_id, 'wp_review_desc', true );

	$data['product_price'] = wp_review_get_product_price( $post_id );

	$data['items']            = wp_review_get_review_items( $post_id );
	$data['disable_features'] = get_post_meta( $post_id, 'wp_review_disable_features', true );

	$data['type'] = wp_review_get_post_review_type( $post_id );

	$data['total'] = get_post_meta( $post_id, 'wp_review_total', true );
	$data['total'] = wp_review_normalize_rating_value( $data['total'], $data['type'] );

	$data['hide_desc'] = get_post_meta( $post_id, 'wp_review_hide_desc', true );

	$data['schema'] = wp_review_get_review_schema( $post_id );

	$data['schema_data'] = get_post_meta( $post_id, 'wp_review_schema_options', true );

	$data['show_schema_data'] = get_post_meta( $post_id, 'wp_review_show_schema_data', true );

	$data['rating_schema'] = wp_review_get_rating_schema( $post_id );

	$data['links'] = wp_review_get_review_links( $post_id );

	$custom_author  = get_post_meta( $post_id, 'wp_review_custom_author', true );
	$author_field   = get_post_meta( $post_id, 'wp_review_author', true );
	$data['author'] = ( ! $author_field || empty( $author_field ) || ! $custom_author ) ? get_the_author() : $author_field;

	$colors         = wp_review_get_colors( $post_id );
	$data['colors'] = $colors;

	$data['width'] = 100;
	$data['align'] = 'left';

	$user_review          = in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_VISITOR_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );
	$comments_review      = in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );
	$user_review_type     = '';
	$user_review_total    = '';
	$user_review_positive = '';
	$user_review_negative = '';
	$user_review_count    = 0;
	$user_has_reviewed    = false;
	if ( $user_review || $comments_review ) {
		$user_review_type = wp_review_get_post_user_review_type( $post_id );
	}

	if ( $user_review ) {
		$post_reviews         = mts_get_post_reviews( $post_id );
		$user_review_total    = $post_reviews['rating'];
		$user_review_count    = $post_reviews['count'];
		$user_review_positive = $post_reviews['positive_count'];
		$user_review_negative = $post_reviews['negative_count'];
		$user_id              = is_user_logged_in() ? get_current_user_id() : 0;
		$uip                  = wp_review_get_user_ip();
		if ( wp_review_has_reviewed( $post_id, $user_id, $uip, WP_REVIEW_COMMENT_TYPE_VISITOR ) ) {
			$user_has_reviewed = true;
		}
	}
	$data['user_review']          = $user_review;
	$data['comments_review']      = $comments_review;
	$data['user_review_type']     = $user_review_type;
	$data['user_review_total']    = $user_review_total;
	$data['user_review_count']    = $user_review_count;
	$data['user_review_positive'] = $user_review_positive;
	$data['user_review_negative'] = $user_review_negative;
	$data['user_has_reviewed']    = $user_has_reviewed;
	$data['hide_comments_rating'] = get_post_meta( $post_id, 'wp_review_hide_comments_total', true );

	$hide_user_reviews = wp_review_network_option( 'hide_user_reviews_' );
	if ( $hide_user_reviews ) {
		$data['user_review']     = false;
		$data['comments_review'] = false;
	}
	$hide_desc = wp_review_network_option( 'hide_review_description_' );
	if ( $hide_desc ) {
		$data['hide_desc'] = true;
	}
	$hide_links = wp_review_network_option( 'hide_review_links_' );
	if ( $hide_links ) {
		$data['links'] = true;
	}
	$hide_features = wp_review_network_option( 'hide_features_' );
	if ( $hide_features ) {
		$data['disable_features'] = true;
	}
	/**
	 * Filters review data.
	 *
	 * @since 3.0.0
	 *
	 * @hooked wp_review_force_hiding_review_elements() - 10
	 *
	 * @param array $data Review data.
	 * @param array $args Custom arguments.
	 */
	return apply_filters( 'wp_review_get_review_data', $data, $args );
}


/**
 * Gets review color data.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return array
 */
function wp_review_get_colors( $post_id ) {
	$color_options = wp_review_get_global_colors();
	$custom_colors = get_post_meta( $post_id, 'wp_review_custom_colors', true );

	$colors                    = array();
	$colors['custom_colors']   = $custom_colors;
	$colors['custom_location'] = get_post_meta( $post_id, 'wp_review_custom_location', true );
	$colors['custom_width']    = get_post_meta( $post_id, 'wp_review_custom_width', true );
	$colors['color']           = get_post_meta( $post_id, 'wp_review_color', true );
	$colors['inactive_color']  = get_post_meta( $post_id, 'wp_review_inactive_color', true );
	$colors['type']            = wp_review_get_post_review_type( $post_id );
	$colors['fontcolor']       = get_post_meta( $post_id, 'wp_review_fontcolor', true );
	$colors['bgcolor1']        = get_post_meta( $post_id, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']        = get_post_meta( $post_id, 'wp_review_bgcolor2', true );
	$colors['bordercolor']     = get_post_meta( $post_id, 'wp_review_bordercolor', true );

	if ( ! $custom_colors && is_array( $color_options ) ) {
		$colors = array_merge( $colors, $color_options );
	} else {
		foreach ( $colors as $key => $color_value ) {
			if ( ! $color_value && ! empty( $color_options[ $key ] ) ) {
				$colors[ $key ] = $color_options[ $key ];
			}
		}
	}

	return apply_filters( 'wp_review_colors', $colors, $post_id );
}


/**
 * Forces hiding review element based on custom arguments.
 *
 * @since 3.0.0
 *
 * @param  array $review Review data.
 * @param  array $args   Custom arguments.
 * @return array
 */
function wp_review_force_hiding_review_elements( $review, $args ) {
	if ( ! empty( $args['hide_heading'] ) ) {
		$review['heading'] = '';
	}

	if ( ! empty( $args['hide_desc'] ) ) {
		$review['hide_desc'] = true;
	}

	if ( ! empty( $args['hide_rating_box'] ) ) {
		$review['user_review']     = false;
		$review['comments_review'] = false;
	}

	if ( ! empty( $args['hide_links'] ) ) {
		$review['links'] = array();
	}

	return $review;
}
add_filter( 'wp_review_get_review_data', 'wp_review_force_hiding_review_elements', 10, 2 );


/**
 * Returns WP Review box html using the box template chosen for the review.
 * Replaces wp_review_get_data()
 *
 * @param  int $post_id Post ID.
 * @return string       Review box output.
 */
function wp_review_get_review_box( $post_id = null ) {
	$hide_user_reviews = wp_review_network_option( 'hide_ratings_in_posts_' );
	if ( ! wp_review_is_enable( $post_id ) || $hide_user_reviews ) {
		return '';
	}

	// WPML workaround to show translated post data instead of original post.
	if ( is_singular() && function_exists( 'icl_object_id' ) ) {
		global $post;
		$post_id = $post->ID;
	}

	$review_data = wp_review_get_review_data( $post_id );

	$template          = wp_review_get_post_box_template( $post_id );
	$box_template_path = wp_review_locate_box_template( $template );

	$template_id = rtrim( $template, '.php' );

	$css_classes = array(
		'review-wrapper',
		"wp-review-{$review_data['post_id']}",
		"wp-review-{$review_data['type']}-type",
		"wp-review-{$template_id}-template",
		'wp-review-box-full-width',
	);

	$review_data['css_classes'] = $css_classes;

	// Pass variables to template.
	set_query_var( 'review', $review_data );
	ob_start();
	load_template( $box_template_path, false );
	$review = ob_get_contents();
	ob_end_clean();
	$review = apply_filters( 'wp_review_get_data', $review, $review_data['post_id'], $review_data['type'], $review_data['total'], $review_data['items'] );
	return $review;
}

/**
 * Gets box template info.
 *
 * @param bool $template Template name.
 * @return array
 */
function wp_review_get_box_template_info( $template = false ) {
	$default_template_headers = array(
		'Name'        => 'WP Review',
		'TemplateURI' => 'Template URI',
		'Version'     => 'Version',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
	);

	if ( ! $template ) {
		$template = wp_review_get_post_box_template();
	}

	$path = wp_review_locate_box_template( $template );

	if ( $path ) {
		return get_file_data( $path, $default_template_headers );
	}

	return array( $default_template_headers );
}

/**
 *  Returns absolute path to template directory.
 */
function wp_review_get_box_template_directory() {
	$template = wp_review_get_post_box_template();
	if ( ! $template ) {
		return '';
	}

	$current_template_directory = wp_review_locate_box_template( $template );

	return dirname( $current_template_directory );
}

/**
 *  Returns template directory URI. To be used in template file.
 */
function wp_review_get_box_template_directory_uri() {
	// Let's hope this will work in most cases.
	return get_bloginfo( 'url' ) . '/' . str_replace( ABSPATH, '', wp_review_get_box_template_directory() );
}


/**
 * Gets box templates list.
 *
 * @return array
 */
function wp_review_get_box_templates_list() {

	$default_paths = array(
		WP_REVIEW_DIR . 'box-templates',
		get_template_directory() . '/wp-review',
		get_stylesheet_directory() . '/wp-review',
	);
	$paths         = apply_filters( 'wp_review_box_template_paths', $default_paths );

	$templates = array();

	foreach ( $paths as $path ) {
		$path = trailingslashit( $path );
		// Look for files containing our header 'Launcher template'.
		$files = (array) wp_review_scandir( $path, 'php', 2 );
		foreach ( $files as $file => $full_path ) {
			if ( ! $full_path || ! preg_match( '|WP Review:(.*)$|mi', file_get_contents( $full_path ), $header ) ) {
				continue;
			}

			$templates[ $file ]         = wp_review_get_box_template_info( $file );
			$templates[ $file ]['path'] = $path;
		}
	}
	return $templates;
}

/**
 * Scans directory.
 *
 * @param string $path          Directory path.
 * @param array  $extensions    Extensions.
 * @param int    $depth         Depth.
 * @param string $relative_path Relative path.
 * @return array|bool
 */
function wp_review_scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
	if ( ! is_dir( $path ) ) {
		return false;
	}

	if ( $extensions ) {
		$extensions  = (array) $extensions;
		$_extensions = implode( '|', $extensions );
	}

	$relative_path = trailingslashit( $relative_path );
	if ( '/' == $relative_path ) {
		$relative_path = '';
	}

	$results = scandir( $path );
	$files   = array();
	foreach ( $results as $result ) {
		if ( '.' == $result[0] ) {
			continue;
		}
		if ( is_dir( $path . '/' . $result ) ) {
			if ( ! $depth || 'CVS' == $result ) {
				continue;
			}
			$found = wp_review_scandir( $path . '/' . $result, $extensions, $depth - 1, $relative_path . $result );
			$files = array_merge_recursive( $files, $found );
		} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
			$files[ $relative_path . $result ] = $path . '/' . $result;
		}
	}
	return $files;
}

/**
 * Adds admin columns.
 */
function wp_review_add_admin_columns() {
	$post_types          = get_post_types( array( 'public' => true ), 'names' );
	$excluded_post_types = apply_filters( 'wp_review_excluded_post_types', array( 'attachment' ) );
	$allowed_post_types  = array_diff( $post_types, $excluded_post_types );
	foreach ( $allowed_post_types as $key => $value ) {
		// Add post list table column.
		add_filter( 'manage_' . $value . '_posts_columns', 'wp_review_post_list_column' );
		// Post list table column content.
		add_action( 'manage_' . $value . '_posts_custom_column', 'wp_review_post_list_column_content', 10, 2 );
	}
}
add_action( 'init', 'wp_review_add_admin_columns' );

/**
 * Adds posts list columns.
 *
 * @param array $columns Posts list columns.
 * @return array
 */
function wp_review_post_list_column( $columns ) {
	$columns['wp_review_rating'] = __( 'Rating', 'wp-review' );
	return $columns;
}

/**
 * Shows posts list column content.
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 */
function wp_review_post_list_column_content( $column_name, $post_id ) {
	if ( 'wp_review_rating' === $column_name ) {
		$total = get_post_meta( $post_id, 'wp_review_total', true );
		if ( $total ) {
			$args = array(
				'bar_text_color_from' => 'inactive_color',
			);
			echo wp_review_rating( $total, $post_id, $args );
		} else {
			echo '<span class="no-rating">' . __( 'No Rating', 'wp-review' ) . '</span>';
		}
	}
}

/**
 * Ignores migrate notice.
 */
function wp_review_migrate_notice_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset( $_GET['wp_review_migrate_notice_ignore'] ) && '1' == $_GET['wp_review_migrate_notice_ignore'] ) {
		add_user_meta( $user_id, 'wp_review_migrate_notice_ignore', 'true', true );
	}
}
add_action( 'admin_init', 'wp_review_migrate_notice_ignore' );


/**
 * Shows migrate notice.
 */
function wp_review_migrate_notice() {
	// Migrate.
	global $wpdb, $current_user;
	$user_id = $current_user->ID;
	if ( get_user_meta( $user_id, 'wp_review_migrate_notice_ignore' ) ) {
		return;
	}

	$has_migrated = get_option( 'wp_review_has_migrated', false );
	if ( $has_migrated ) {
		return;
	}

	$current_blog_id = get_current_blog_id();
	$rows_left       = 0;
	$migrated_rows   = get_option( 'wp_review_migrated_rows', 0 );
	if ( ! $has_migrated && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mts_wp_reviews'" ) == "{$wpdb->base_prefix}mts_wp_reviews" ) {
		// Table exists and not migrated (fully) yet.
		$total_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->base_prefix . 'mts_wp_reviews WHERE blog_id = ' . $current_blog_id ); // WPCS: unprepared SQL ok.
		$rows_left  = $total_rows - $migrated_rows;
	}

	if ( ! $rows_left ) {
		return;
	}
	?>
	<div class="updated notice-info wp-review-notice">
		<p>
			<?php
			// translators: settings link.
			printf( __( 'Thank you for updating WP Review Pro. Your existing user ratings will show up after importing them in %s.', 'wp-review' ), '<a href="' . admin_url( 'options-general.php?page=wp-review-pro%2Fadmin%2Foptions.php#migrate' ) . '">' . __( 'Settings &gt; WP Review Pro &gt; Migrate Ratings', 'wp-review' ) . '</a>' );
			?>
		</p>
		<a class="notice-dismiss" href="<?php echo esc_url( add_query_arg( 'wp_review_migrate_notice_ignore', '1' ) ); ?>"></a>
	</div>
	<?php
}
add_action( 'admin_notices', 'wp_review_migrate_notice' );

/**
 * Gets schema.
 *
 * @param array $review Review data.
 * @return string
 */
function wp_review_get_schema( $review ) {
	if ( 'none' === $review['schema'] ) {
		return '';
	}

	/*if ( empty( $review['total'] ) || ! floatval( $review['total'] ) ) {
		return '';
	}*/

	$output = '';

	$nesting_mode = apply_filters( 'wp_review_schema_nesting_mode', 'type' ); // type, rating, none.

	// Force rating nesting in certain types ( weird results in testing tool otherwise ).
	if ( in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_rating_types', array( 'Movie', 'Book' ) ) ) ) {
		$nesting_mode = 'rating';
	}

	// If type requires nested aggregateRating don't nest it in aggregateRating.
	if ( in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_user_rating_types', array( 'SoftwareApplication', 'Recipe' ) ) ) && in_array( $review['rating_schema'], array( 'visitors' ) ) ) {
		$nesting_mode = 'rating';
	}

	switch ( $nesting_mode ) {

		case 'type': // schema.org typed element ( Movie, Recipe, etc) nested in review/aggregateRating type.
			if ( in_array( $review['rating_schema'], array( 'visitors' ) ) ) {
				$output .= wp_review_get_schema_user_rating( $review, true );
			} else {
				$output .= wp_review_get_schema_review_rating( $review, true );
			}
			break;

		case 'rating': // review/aggregateRating type nested in specific type ( Movie, Recipe, etc).
			$output .= wp_review_get_schema_type( $review, true );
			break;

		case 'none': // separated reviewed item type ( Movie, Recipe, etc) and review/aggregateRating.
			$output .= wp_review_get_schema_type( $review );
			if ( in_array( $review['rating_schema'], array( 'visitors' ) ) ) {
				$output .= wp_review_get_schema_user_rating( $review );
			} else {
				$output .= wp_review_get_schema_review_rating( $review );
			}
			break;
	}

	return apply_filters( 'wp_review_get_schema', $output, $review );

}

/**
 * Gets schema type.
 *
 * @param array $review        Review data.
 * @param bool  $nested_rating Is nested rating or not.
 * @return array
 */
function wp_review_get_schema_type( $review, $nested_rating = false ) {

	if ( empty( $review['schema'] ) || 'Thing' === $review['schema'] || ! isset( $review['schema_data'] ) ) {
		return;
	}

	$args = array(
		'@context' => 'http://schema.org',
		'@type'    => $review['schema'],
	);

	$ldjson_data = wp_review_get_ldjson_data( $review['schema'], $review['schema_data'][ $review['schema'] ], $review );
	if ( $ldjson_data ) {
		$args += $ldjson_data;
	} else {
		$schemas = wp_review_schema_types();
		$fields  = isset( $schemas[ $review['schema'] ] ) && isset( $schemas[ $review['schema'] ]['fields'] ) ? $schemas[ $review['schema'] ]['fields'] : array();

		foreach ( $fields as $key => $data ) {
			if ( ! empty( $data['omit'] ) ) {
				continue;
			}
			if ( isset( $review['schema_data'][ $review['schema'] ][ $data['name'] ] ) && ! empty( $review['schema_data'][ $review['schema'] ][ $data['name'] ] ) ) {
				if ( isset( $data['multiline'] ) && $data['multiline'] ) {
					$review['schema_data'][ $review['schema'] ][ $data['name'] ] = preg_split( '/\r\n|[\r\n]/', $review['schema_data'][ $review['schema'] ][ $data['name'] ] );
				}
				if ( isset( $data['part_of'] ) ) {
					$args[ $data['part_of'] ]['@type'] = $data['@type'];
					if ( 'image' === $data['type'] ) {
						$args[ $data['part_of'] ][ $data['name'] ] = $review['schema_data'][ $review['schema'] ][ $data['name'] ]['url'];
					} elseif ( in_array( $data['name'], apply_filters( 'wp_reviev_schema_ISO_8601_duration_items', array( 'prepTime', 'cookTime', 'totalTime', 'duration' ) ) ) ) { // phpcs:ignore
						$args[ $data['part_of'] ][ $data['name'] ] = 'PT' . $review['schema_data'][ $review['schema'] ][ $data['name'] ];
					} else {
						$args[ $data['part_of'] ][ $data['name'] ] = $review['schema_data'][ $review['schema'] ][ $data['name'] ];
					}
				} else {
					if ( 'image' === $data['type'] ) {
						$args[ $data['name'] ] = $review['schema_data'][ $review['schema'] ][ $data['name'] ]['url'];
					} elseif ( in_array( $data['name'], apply_filters( 'wp_reviev_schema_ISO_8601_duration_items', array( 'prepTime', 'cookTime', 'totalTime', 'duration' ) ) ) ) { // phpcs:ignore
						$args[ $data['name'] ] = 'PT' . $review['schema_data'][ $review['schema'] ][ $data['name'] ];
					} else {
						$args[ $data['name'] ] = $review['schema_data'][ $review['schema'] ][ $data['name'] ];
					}
				}
			}
		}
	}

	// Nested aggregateRating is required in some types ( SoftwareApplication, Recipe ).
	$force_user_rating = in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_user_rating_types', array( 'SoftwareApplication', 'Recipe' ) ) );
	if ( $force_user_rating ) {
		if ( $review['user_review'] || $review['comments_review'] ) {
			$aggregate_rating = wp_review_get_schema_nested_user_rating_args( $review );
			if ( ! empty( $aggregate_rating ) ) {
				$args['aggregateRating'] = $aggregate_rating;
			}
		}
		if ( 'author' === $review['rating_schema'] ) {
			$args['review'] = wp_review_get_schema_nested_review_args( $review );
		}
	} elseif ( $nested_rating ) {
		if ( in_array( $review['rating_schema'], array( 'visitors' ) ) ) {
			if ( $review['user_review'] || $review['comments_review'] ) {
				$aggregate_rating = wp_review_get_schema_nested_user_rating_args( $review );
				if ( ! empty( $aggregate_rating ) ) {
					$args['aggregateRating'] = $aggregate_rating;
				}
			}
		} else {
			$args['review'] = wp_review_get_schema_nested_review_args( $review );
		}
	}

	$args = apply_filters( 'wp_review_get_schema_type_args', $args, $review, $nested_rating );

	$output  = '<script type="application/ld+json">' . PHP_EOL;
	$output .= wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . PHP_EOL;
	$output .= '</script>' . PHP_EOL;

	return apply_filters( 'wp_review_get_schema_type', $output, $args, $review, $nested_rating );
}

/**
 * Gets schema review rating.
 *
 * @param array $review      Review data.
 * @param bool  $nested_item Is nested item or not.
 * @return mixed|void
 */
function wp_review_get_schema_review_rating( $review, $nested_item = false ) {

	if ( ! $nested_item && in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_user_rating_types', array( 'SoftwareApplication', 'Recipe' ) ) ) ) {
		return; // Requires nested aggregateRating.
	}

	global $wp_review_rating_types;

	if ( $nested_item ) {
		$item_reviewed = wp_review_get_schema_nested_item_args( $review );
	} else {
		$item_reviewed = array(
			'@type' => 'Thing',
			'name'  => esc_html( wp_review_get_reviewed_item_name( $review ) ),
		);
	}

	$args = array(
		'@context'     => 'http://schema.org',
		'@type'        => 'Review',
		'itemReviewed' => $item_reviewed,
		'reviewRating' => array(
			'@type'       => 'Rating',
			'ratingValue' => (string) wp_review_normalize_rating_value( $review['total'], $review['type'] ),
			'bestRating'  => $wp_review_rating_types[ $review['type'] ]['max'],
		),
		'author'       => array(
			'@type' => 'Person',
			'name'  => esc_html( $review['author'] ),
		),
		'reviewBody'   => esc_html( $review['desc'] ),
	);

	$args = apply_filters( 'wp_review_get_schema_review_rating_args', $args, $review );

	$output  = '<script type="application/ld+json">' . PHP_EOL;
	$output .= wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . PHP_EOL;
	$output .= '</script>' . PHP_EOL;

	return apply_filters( 'wp_review_get_schema_review_rating', $output, $args, $review );
}

/**
 * Gets schema user rating.
 *
 * @param array $review      Review data.
 * @param bool  $nested_item Is nested item or not.
 * @return mixed|void
 */
function wp_review_get_schema_user_rating( $review, $nested_item = false ) {

	if ( ! $nested_item && in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_user_rating_types', array( 'SoftwareApplication', 'Recipe' ) ) ) ) {
		return; // Requires nested aggregateRating.
	}

	global $wp_review_rating_types;

	if ( $nested_item ) {
		$item_reviewed = wp_review_get_schema_nested_item_args( $review );
	} else {
		$item_reviewed = array(
			'@type' => 'Thing',
			'name'  => esc_html( wp_review_get_reviewed_item_name( $review ) ),
		);
	}

	$total = $review['user_review_total'];
	$count = $review['user_review_count'];

	$args = array();
	if ( 0 < (int) $count ) {
		$args = array(
			'@context'     => 'http://schema.org',
			'@type'        => 'aggregateRating',
			'itemReviewed' => $item_reviewed,
			'ratingValue'  => (string) wp_review_normalize_rating_value( $total, $review['type'] ),
			'bestRating'   => $wp_review_rating_types[ $review['user_review_type'] ]['max'],
			'ratingCount'  => $count,
		);
	}

	$args = apply_filters( 'wp_review_get_schema_user_rating_args', $args, $review );

	$output = '';
	if ( ! empty( $args ) ) {
		$output .= '<script type="application/ld+json">' . PHP_EOL;
		$output .= wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . PHP_EOL;
		$output .= '</script>' . PHP_EOL;
	}

	return apply_filters( 'wp_review_get_schema_user_rating', $output, $args, $review );
}

/**
 * Gets reviewed item name.
 *
 * @param array $review Review data.
 * @return mixed|void
 */
function wp_review_get_reviewed_item_name( $review ) {

	$item_reviewed = empty( $review['heading'] ) ? get_the_title( $review['post_id'] ) : esc_html( $review['heading'] );

	if ( ! empty( $review['schema'] ) && 'Thing' !== $review['schema'] ) {

		if ( isset( $review['schema_data'][ $review['schema'] ]['name'] ) && ! empty( $review['schema_data'][ $review['schema'] ]['name'] ) ) {
			$item_reviewed = $review['schema_data'][ $review['schema'] ]['name'];
		}
	}

	return apply_filters( 'wp_review_get_reviewed_item_name', $item_reviewed, $review );
}

/**
 * Gets schema nested user rating args.
 *
 * @param array $review Review data.
 * @return array
 */
function wp_review_get_schema_nested_user_rating_args( $review ) {

	global $wp_review_rating_types;
	$args = array();
	if ( 0 < (int) $review['user_review_count'] ) {
		$args = array(
			'@type'       => 'aggregateRating',
			'ratingValue' => (string) wp_review_normalize_rating_value( $review['user_review_total'], $review['type'] ),
			'bestRating'  => $wp_review_rating_types[ $review['user_review_type'] ]['max'],
			'ratingCount' => $review['user_review_count'],
		);
	}

	return apply_filters( 'wp_review_get_schema_nested_user_rating_args', $args, $review );
}

/**
 * Gets schema nested review args.
 *
 * @param array $review Review data.
 * @return array
 */
function wp_review_get_schema_nested_review_args( $review ) {
	global $wp_review_rating_types;

	$args = array(
		'@type'        => 'Review',
		'reviewRating' => array(
			'@type'       => 'Rating',
			'ratingValue' => (string) wp_review_normalize_rating_value( $review['total'], $review['type'] ),
			'bestRating'  => $wp_review_rating_types[ $review['type'] ]['max'],
		),
		'author'       => array(
			'@type' => 'Person',
			'name'  => esc_html( $review['author'] ),
		),
		'reviewBody'   => esc_html( $review['desc'] ),
	);

	return apply_filters( 'wp_review_get_schema_nested_review_args', $args, $review );
}

/**
 * Gets schema nested item args.
 *
 * @param array $review Review data.
 * @return array
 */
function wp_review_get_schema_nested_item_args( $review ) {

	$args = array(
		'@type' => $review['schema'],
	);

	$schema_data = ! empty( $review['schema_data'][ $review['schema'] ] ) ? $review['schema_data'][ $review['schema'] ] : array();
	$ldjson_data = wp_review_get_ldjson_data( $review['schema'], $schema_data, $review );
	if ( $ldjson_data ) {
		$args += $ldjson_data;
	} else {
		$schemas = wp_review_schema_types();
		$fields  = isset( $schemas[ $review['schema'] ] ) && isset( $schemas[ $review['schema'] ]['fields'] ) ? $schemas[ $review['schema'] ]['fields'] : array();
		if ( is_array( $fields ) && ! empty( $fields ) ) {
			foreach ( $fields as $key => $data ) {
				if ( ! empty( $data['omit'] ) ) {
					continue;
				}
				if ( isset( $schema_data[ $data['name'] ] ) && ! empty( $schema_data[ $data['name'] ] ) ) {
					if ( isset( $data['multiline'] ) && $data['multiline'] ) {
						$schema_data[ $data['name'] ] = preg_split( '/\r\n|[\r\n]/', $schema_data[ $data['name'] ] );
					}
					if ( isset( $data['part_of'] ) ) {
						$args[ $data['part_of'] ]['@type'] = $data['@type'];
						if ( 'image' === $data['type'] ) {
							$args[ $data['part_of'] ][ $data['name'] ] = $schema_data[ $data['name'] ]['url'];
						} elseif ( in_array( $data['name'], apply_filters( 'wp_reviev_schema_ISO_8601_duration_items', array( 'prepTime', 'cookTime', 'totalTime', 'duration' ) ) ) ) { // phpcs:ignore
							$args[ $data['part_of'] ][ $data['name'] ] = 'PT' . $schema_data[ $data['name'] ];
						} else {
							$args[ $data['part_of'] ][ $data['name'] ] = $schema_data[ $data['name'] ];
						}
					} else {
						if ( 'image' === $data['type'] ) {
							$args[ $data['name'] ] = $schema_data[ $data['name'] ]['url'];
						} elseif ( in_array( $data['name'], apply_filters( 'wp_reviev_schema_ISO_8601_duration_items', array( 'prepTime', 'cookTime', 'totalTime', 'duration' ) ) ) ) { // phpcs:ignore
							$args[ $data['name'] ] = 'PT' . $schema_data[ $data['name'] ];
						} else {
							$args[ $data['name'] ] = $schema_data[ $data['name'] ];
						}
					}
				}
			}
		}
	}

	// Nested aggregateRating is recommended in some types ( SoftwareApplication, Recipe ).
	if ( in_array( $review['schema'], apply_filters( 'wp_review_schema_force_nested_user_rating_types', array( 'SoftwareApplication', 'Recipe' ) ) ) && ( $review['user_review'] || $review['comments_review'] ) ) {
		$aggregate_rating = wp_review_get_schema_nested_user_rating_args( $review );
		if ( ! empty( $aggregate_rating ) ) {
			$args['aggregateRating'] = $aggregate_rating;
		}
	}

	return apply_filters( 'wp_review_get_schema_nested_item_args', $args, $review );
}

/**
 * Gets schema ld-json data.
 *
 * @since 3.0.0
 *
 * @param  string $type   Schema type.
 * @param  array  $data   Schema data.
 * @param  array  $review Review data.
 * @return array
 */
function wp_review_get_ldjson_data( $type, $data, $review ) {
	$ldjson_data = array();
	$post_id     = $review['post_id'];

	switch ( $type ) {
		case 'Article':
			$ldjson_data = array(
				'mainEntityOfPage' => array(
					'@type' => 'Webpage',
					'@id'   => get_permalink( $post_id ),
				),
				'headline'         => ! empty( $data['headline'] ) ? $data['headline'] : '',
				'image'            => array(
					'@type' => 'ImageObject',
					'url'   => ! empty( $data['image']['url'] ) ? esc_url( $data['image']['url'] ) : '',
				),
				'datePublished'    => get_the_time( 'Y-m-d H:i:s', $post_id ),
				'dateModified'     => get_the_modified_time( 'Y-m-d H:i:s', $post_id ),
				'author'           => array(
					'@type' => 'Person',
					'name'  => ! empty( $data['author'] ) ? $data['author'] : '',
				),
				'publisher'        => array(
					'@type' => 'Organization',
					'name'  => ! empty( $data['publisher'] ) ? $data['publisher'] : '',
					'logo'  => array(
						'@type' => 'ImageObject',
						'url'   => ! empty( $data['publisher_logo']['url'] ) ? esc_url( $data['publisher_logo']['url'] ) : '',
					),
				),
				'description'      => ! empty( $data['description'] ) ? $data['description'] : '',
			);
			break;

		case 'Thing':
			$ldjson_data['name'] = empty( $review['heading'] ) ? get_the_title( $review['post_id'] ) : esc_html( $review['heading'] );
			break;
	}

	return $ldjson_data;
}


/**
 * Gets rating icon.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wp_review_get_rating_icon() {
	return 'mts-icon-star';
}

/**
 * Gets rating image.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wp_review_get_rating_image() {

	$rating_image = wp_review_option( 'rating_image', apply_filters( 'wp_review_default_rating_image', '' ) );
	if ( $rating_image ) {
		$rating_img_src = wp_get_attachment_image_src( $rating_image );

		if ( ! empty( $rating_img_src ) ) {
			$rating_image = $rating_img_src[0];
		}
	}

	return $rating_image;
}


/**
 * Checks if review is enable.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return bool
 */
function wp_review_is_enable( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return ! ! wp_review_get_post_review_type( $post_id );
}


/**
 * Gets post rating schema.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return string
 */
function wp_review_get_rating_schema( $post_id ) {
	$value = get_post_meta( $post_id, 'wp_review_rating_schema', true );
	if ( '' === $value ) {
		$value = 'author';
	}
	if ( wp_review_get_user_rating_setup( $post_id ) == WP_REVIEW_REVIEW_DISABLED ) {
		$value = 'author';
	}
	return $value;
}


/**
 * Checks if review description is hidden.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return bool
 */
function wp_review_is_hidden_desc( $post_id ) {
	$hide_desc = get_post_meta( $post_id, 'wp_review_hide_desc', true );

	return $hide_desc;
}


/**
 * Gets product price.
 *
 * @since 3.0.0
 *
 * @param  int $post_id Post ID.
 * @return float
 */
function wp_review_get_product_price( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return get_post_meta( $post_id, 'wp_review_product_price', true );
}


/**
 * Shows product price.
 *
 * @since 3.0.0
 *
 * @param int $post_id Post ID.
 */
function wp_review_product_price( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	echo esc_html( wp_review_get_product_price( $post_id ) );
}


/**
 * Gets review total.
 *
 * @since 3.0.0
 *
 * @param int $post_id Post ID.
 * @return float
 */
function wp_review_get_review_total( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return floatval( get_post_meta( $post_id, 'wp_review_total', true ) );
}


/**
 * Gets review items.
 *
 * @since 3.0.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function wp_review_get_review_items( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$items = get_post_meta( $post_id, 'wp_review_item', true );
	if ( ! $items || ! is_array( $items ) ) {
		return '';
	}

	$global_colors       = wp_review_get_global_colors();
	$custom_colors       = get_post_meta( $post_id, 'wp_review_custom_colors', true );
	$post_color          = get_post_meta( $post_id, 'wp_review_color', true );
	$post_inactive_color = get_post_meta( $post_id, 'wp_review_inactive_color', true );

	$default_color    = $custom_colors && $post_color ? $post_color : $global_colors['color'];
	$default_inactive = $custom_colors && $post_inactive_color ? $post_inactive_color : $global_colors['inactive_color'];
	foreach ( $items as $index => $item ) {
		if ( empty( $item['id'] ) || is_numeric( $item['id'] ) ) {
			$items[ $index ]['id'] = sanitize_title( $item['wp_review_item_title'] ) . '_' . wp_generate_password( 6 );
		}
	}
	update_post_meta( $post_id, 'wp_review_item', $items );

	foreach ( $items as $index => $item ) {
		$items[ $item['id'] ] = $items[ $index ];
		unset( $items[ $index ] );

		if ( empty( $item['wp_review_item_color'] ) ) {
			$items[ $item['id'] ]['wp_review_item_color'] = $default_color;
		}

		if ( empty( $item['wp_review_item_inactive_color'] ) ) {
			$items[ $item['id'] ]['wp_review_item_inactive_color'] = $default_inactive;
		}
	}

	return $items;
}


/**
 * Gets review links.
 *
 * @since 3.0.0
 *
 * @param int $post_id Post ID.
 * @return array
 */
function wp_review_get_review_links( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$links = get_post_meta( $post_id, 'wp_review_links', true );
	$links = $links ? (array) $links : array();

	$return_links = array();
	foreach ( $links as $review_link ) {
		if ( ! empty( $review_link['text'] ) ) {
			$return_links[] = $review_link;
		}
	}
	return $return_links;
}


/**
 * Gets review box template.
 *
 * @since 3.0.0
 *
 * @param int $post_id Post ID.
 * @return float
 */
function wp_review_get_box_template( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$template = get_post_meta( $post_id, 'wp_review_box_template', true );
	$custom   = get_post_meta( $post_id, 'wp_review_custom_colors', true );
	if ( ! $custom || ! $template ) {
		$template = wp_review_option( 'box_template', 'default' );
	}
	return $template;
}


/**
 * Gets transient expired time.
 *
 * @since 3.0.0
 *
 * @return int
 */
function wp_review_transient_expired_time() {
	return apply_filters( 'wp_review_transient_expired_time', WEEK_IN_SECONDS );
}


/**
 * Clears cache.
 *
 * @since 3.0.0
 */
function wp_review_clear_cache() {
	// phpcs:disable
	// delete_transient( 'wp_review_recent_reviews_query' );
	// delete_transient( 'wp_review_toprated_reviews_query' );
	// delete_transient( 'wp_review_mostvoted_reviews_query' );
	// delete_transient( 'wp_review_custom_reviews_query' );
	// phpcs:enable
}


/**
 * Adds an option to clear cache via URL.
 *
 * @since 3.0.0
 */
function wp_review_clear_cache_via_url() {
	if ( ! isset( $_GET['clear'] ) ) {
		return;
	}
	wp_review_clear_cache();
}
add_action( 'template_redirect', 'wp_review_clear_cache_via_url' );


/**
 * Gets reviews query.
 *
 * @since 3.0.0
 *
 * @param  string $type    Type of the query.
 * @param  array  $options Query options.
 * @return WP_Query
 */
function wp_review_get_reviews_query( $type, $options ) {
	$key = 'wp_review_' . md5(
		sprintf(
			'%1$s_%2$s_reviews_query',
			$type,
			serialize( $options )
		)
	);

	if ( ! empty( $options['clear_cache'] ) ) {
		delete_transient( $key );
	}

	$query = get_transient( $key );
	if ( $query && empty( $options['no_cache'] ) ) {
		return $query;
	}

	$options['review_type'] = $options['review_type'] ? (array) $options['review_type'] : array();
	$options['cat']         = $options['cat'] ? (array) $options['cat'] : array();

	switch ( $type ) {
		case 'mostvoted':
			$query_args = array(
				'orderby'  => 'meta_value_num',
				'meta_key' => 'wp_review_review_count',
				'order'    => 'desc',
			);
			break;

		case 'toprated':
			$query_args = array(
				'orderby'  => 'meta_value_num',
				'meta_key' => ! empty( $options['toprated_key'] ) ? $options['toprated_key'] : 'wp_review_total',
				'order'    => 'desc',
			);
			break;

		case 'cat':
			$query_args = array(
				'orderby'      => 'date',
				'order'        => 'desc',
				'category__in' => $options['cat'],
			);
			break;

		case 'custom':
			$query_args = array(
				'post__in' => $options['ids'],
				'orderby'  => 'post__in',
			);
			break;

		default:
			$query_args = array(
				'orderby' => 'date',
				'order'   => 'desc',
			);
	}

	$query_args['ignore_sticky_posts'] = true;
	$query_args['post_type']           = isset( $options['post_type'] ) ? $options['post_type'] : 'post';
	$query_args['post_status']         = 'publish';
	$query_args['posts_per_page']      = intval( $options['post_num'] );
	$query_args['paged']               = intval( $options['page'] );

	if ( ! empty( $options['number_of_days'] ) && intval( $options['number_of_days'] ) ) {
		$date_str                 = $options['number_of_days'] > 1 ? '%s days ago' : '%s day ago';
		$query_args['date_query'] = array(
			array(
				'after' => sprintf( $date_str, intval( $options['number_of_days'] ) ),
			),
		);
	}

	$meta_query = array();

	// If specific review type.
	if ( ! empty( $options['review_type'] ) ) {
		$meta_query[] = array(
			'key'     => 'wp_review_type',
			'compare' => 'IN',
			'value'   => $options['review_type'],
		);

		if ( wp_review_option( 'review_type' ) === $options['review_type'] ) {
			// Is post setting is not set and default review type is not none.
			$meta_query[] = array(
				'key'     => 'wp_review_type',
				'value'   => '1', // See https://core.trac.wordpress.org/ticket/23268 for more information.
				'compare' => 'NOT EXISTS',
			);
		}
	} else {
		$meta_query[] = array(
			'key'     => 'wp_review_type',
			'compare' => '!=',
			'value'   => 'none',
		);

		if ( 'none' === wp_review_option( 'review_type' ) || ! wp_review_option( 'review_type' ) ) {
			$meta_query[] = array(
				'key'     => 'wp_review_type',
				'compare' => 'EXISTS',
			);
		}
	}

	if ( $meta_query ) {
		$query_args['meta_query'] = $meta_query;
	}

	/**
	 * Filters reviews query args.
	 *
	 * @since 3.0.0
	 *
	 * @param array $query_args Query args.
	 * @param array $options    Options.
	 */
	$query_args = apply_filters( 'wp_review_reviews_query_args', $query_args, $options );

	$query = new WP_Query( $query_args );

	if ( empty( $options['no_cache'] ) ) {
		set_transient( $key, $query, wp_review_transient_expired_time() );
	}

	return $query;
}


/**
 * Shows ajax pagination for reviews.
 *
 * @since 3.0.0
 *
 * @param int $page      Current page.
 * @param int $last_page Last page.
 */
function wp_review_ajax_pagination( $page, $last_page ) {
	if ( 1 == $last_page ) {
		return;
	}
	?>
	<div class="reviews-pagination" data-page="<?php echo intval( $page ); ?>">
		<?php if ( $page > 1 ) : ?>
			<a href="#" class="previous"><span><?php esc_html_e( '&laquo; Previous', 'wp-review' ); ?></span></a>
		<?php endif; ?>

		<?php if ( $page != $last_page ) : ?>
			<a href="#" class="next"><span><?php esc_html_e( 'Next &raquo;', 'wp-review' ); ?></span></a>
		<?php endif; ?>
	</div>
	<?php
}


/**
 * Shows star rating.
 *
 * @since 3.0.0
 *
 * @param float $value Rating value.
 * @param array $args  Custom attributes.
 */
function wp_review_star_rating( $value, $args = array() ) {
	$rating = array(
		'value' => floatval( $value ),
		'args'  => $args,
		'color' => '#1e73be',
	);

	$template = mts_get_template_path( 'star', 'star-output' );
	include $template;
}


/**
 * Shows spinner icon.
 *
 * @since 3.0.0
 */
function wp_review_spinner() {
	echo '<span class="animate-spin fa fa-spinner"></span>';
}


/**
 * Rates a post by visitor
 * Should be used in AJAX handler.
 *
 * @param int   $post_id     Post ID.
 * @param array $rating_data {
 *     Rating data.
 *
 *     @type float  $total    Rating total.
 *     @type string $type     Rating type.
 *     @type array  $features Features rating data, key is feature ID and value is rating value.
 * }
 */
function wp_review_visitor_rate( $post_id, $rating_data ) {
	$rating_data = wp_parse_args(
		$rating_data,
		array(
			'total'    => '',
			'type'     => '',
			'features' => array(),
		)
	);

	$output = array(
		'status'       => '',
		'html'         => '',
		'rating_total' => '',
		'rating_count' => '',
	);

	if ( ! $rating_data['total'] ) {
		echo wp_json_encode( $output );
		exit;
	}

	$type        = wp_review_get_post_user_review_type( $post_id );
	$rating_type = wp_review_get_rating_type_data( $type );
	if ( ! $rating_type ) {
		echo wp_json_encode( $output );
		exit;
	}

	if ( $rating_data['type'] && $type !== $rating_data['type'] ) {
		echo wp_json_encode( $output );
		exit;
	}

	if ( ! empty( $rating_data['features'] ) && is_array( $rating_data['features'] ) ) {
		foreach ( $rating_data['features'] as $key => $value ) {
			if ( $value < 0 ) {
				$value = 0;
			}
			$rating_data['features'][ $key ] = $value;
		}
	}

	$user_id = is_user_logged_in() ? get_current_user_id() : 0;
	$review  = $rating_data['total'];
	$uip     = wp_review_get_user_ip();

	if ( ! function_exists( 'wp_review_comment_duplicate_trigger' ) ) {
		/**
		 * Shows comment duplicate message.
		 *
		 * @param array $commentdata Comment data.
		 */
		function wp_review_comment_duplicate_trigger( $commentdata ) {
			$post_reviews           = mts_get_post_reviews( $commentdata['comment_post_ID'] );
			$output['status']       = 'fail';
			$output['error']        = 'duplicate';
			$output['rating_total'] = $post_reviews['rating'];
			$output['rating_count'] = $post_reviews['count'];
			$output['html']         = wp_review_rating( $post_reviews['rating'], $commentdata['comment_post_ID'], array( 'user_rating' => true ) );
			echo wp_json_encode( $output );
			exit;
		}
	}
	add_action( 'comment_duplicate_trigger', 'wp_review_comment_duplicate_trigger' );

	// Don't allow higher rating than max.
	if ( $review > $rating_type['max'] ) {
		$review = $rating_type['max'];
	}

	if (
		$review &&
		! wp_review_has_reviewed( $post_id, $user_id, $uip, WP_REVIEW_COMMENT_TYPE_VISITOR ) &&
		( $user_id || ! wp_review_option( 'registered_only' ) )
	) {
		if ( $review < 0 ) {
			$review = 0;
		}
		// Translators: rating value text.
		$comment_content = sprintf( __( 'Visitor Rating: %s', 'wp-review' ), sprintf( $rating_type['value_text'], $review ) );

		if ( ! empty( $rating_data['features'] ) && is_array( $rating_data['features'] ) ) {
			$features = wp_review_get_review_items( $post_id );
			foreach ( $rating_data['features'] as $feature_id => $value ) {
				$comment_content .= sprintf(
					"\n%s: %s",
					! empty( $features[ $feature_id ] ) ? esc_html( $features[ $feature_id ]['wp_review_item_title'] ) : '',
					floatval( $value )
				);
			}
		}

		$approve_comment = wp_review_option( 'approve_ratings', true );
		$approve_comment = 'false' !== $approve_comment;
		if ( is_user_logged_in() ) {
			$approve_comment = true;
		}

		$insert = wp_insert_comment(
			array(
				'user_id'           => $user_id,
				'comment_type'      => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'comment_post_ID'   => $post_id,
				'comment_parent'    => 0,
				'comment_author_IP' => $uip,
				'comment_content'   => $comment_content,
				'comment_agent'     => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
				'comment_date'      => current_time( 'mysql' ),
				'comment_date_gmt'  => current_time( 'mysql', 1 ),
				'comment_approved'  => $approve_comment,
			)
		);

		if ( $insert ) {
			if ( update_comment_meta( $insert, WP_REVIEW_VISITOR_RATING_METAKEY, $review ) ) {
				if ( ! empty( $rating_data['features'] ) ) {
					update_comment_meta( $insert, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, $rating_data['features'] );
				}

				$output['status'] = 'ok';

				if ( ! empty( $rating_data['features'] ) ) {
					$reviews           = wp_review_get_post_feature_reviews( $post_id, true );
					$output['html']    = wp_review_get_review_box( $post_id );
					$output['reviews'] = $reviews;
				} else {
					$post_reviews           = mts_get_post_reviews( $post_id, true );
					$output['html']         = wp_review_rating( $post_reviews['rating'], $post_id, array( 'user_rating' => true ) );
					$output['rating_total'] = $post_reviews['rating'];
					$output['rating_count'] = $post_reviews['count'];
				}

				if ( ! $approve_comment ) {
					$output['awaiting_moderation'] = __( 'Your rating is awaiting moderation.', 'wp-review' );
				}
				echo wp_json_encode( $output );
				exit;
			} else {
				wp_delete_comment( $insert );
			}
		}
	} // End if().

	$post_reviews           = mts_get_post_reviews( $post_id );
	$output['status']       = 'fail';
	$output['error']        = 'db_error';
	$output['rating_total'] = $post_reviews['rating'];
	$output['rating_count'] = $post_reviews['count'];
	$output['html']         = wp_review_rating( $post_reviews['rating'], $post_id, array( 'user_rating' => true ) );
	echo wp_json_encode( $output );
	exit;
}


/**
 * Gets schema types.
 *
 * @return array
 */
function wp_review_schema_types() {
	$default = include WP_REVIEW_INCLUDES . 'schemas.php';
	return apply_filters( 'wp_review_schema_types', $default );
}


/**
 * Gets schema type data.
 *
 * @since 3.0.0
 *
 * @param  string $type Schema type name.
 * @return array|false
 */
function wp_review_get_schema_type_data( $type ) {
	$types = wp_review_schema_types();
	if ( ! isset( $types ) ) {
		return false;
	}
	return $types[ $type ];
}


/**
 * Gets schema fields.
 *
 * @since 3.0.0
 *
 * @param array $schema Schema data.
 * @return array
 */
function wp_review_get_schema_fields( $schema ) {
	if ( empty( $schema['fields'] ) ) {
		return array();
	}
	return (array) $schema['fields'];
}


/**
 * Converts multiline text to list.
 *
 * @since 3.0.0
 *
 * @param  string $str Multiline string.
 * @return string
 */
function wp_review_nl2list( $str ) {
	$lines = explode( "\n", $str );
	return '<li>' . implode( '</li><li>', $lines ) . '</li>';
}


/**
 * Normalizes the rating value base on rating type.
 *
 * @since 3.0.6
 *
 * @param  float  $value Rating value.
 * @param  string $type  Rating type.
 * @return float
 */
function wp_review_normalize_rating_value( $value, $type ) {
	$rating_type = wp_review_get_rating_type_data( $type );
	if ( ! $rating_type ) {
		return $value;
	}
	return round( floatval( $value ), $rating_type['decimals'] );
}


/**
 * Enqueues rating type scripts.
 *
 * @since 3.0.8
 *
 * @param string $type         Type of script. Accepts `output` or `input`.
 * @param array  $rating_types Rating types.
 */
function wp_review_enqueue_rating_type_scripts( $type = 'output', array $rating_types = array() ) {
	if ( ! $rating_types ) {
		$rating_types = wp_review_get_rating_types();
		$rating_types = array_keys( $rating_types );
	}
	if ( ! empty( $rating_types ) && is_array( $rating_types ) ) {
		foreach ( $rating_types as $rating_type ) {
			wp_enqueue_script( "wp-review-{$rating_type}-{$type}" );
		}
	}
}

// GDPR Compliant - Export User Information.
if ( ! function_exists( 'wp_review_data_exporter' ) ) {
	/**
	 * Exports review data.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array
	 */
	function wp_review_data_exporter( $email_address, $page = 1 ) {
		// Limit us to 500 comments at a time to avoid timing out.
		$number         = 500;
		$page           = (int) $page;
		$data_to_export = array();
		$comments       = get_comments(
			array(
				'author_email'              => $email_address,
				'number'                    => $number,
				'paged'                     => $page,
				'order_by'                  => 'comment_ID',
				'order'                     => 'ASC',
				'update_comment_meta_cache' => false,
			)
		);

		$comment_prop_to_export = array(
			'comment_rating'  => __( 'Comment Rating', 'wp-review' ),
			'features_rating' => __( 'Features Ratings', 'wp-review' ),
			'comment_title'   => __( 'Comment Title', 'wp-review' ),
		);

		foreach ( (array) $comments as $comment ) {
			$comment_data_to_export = array();
			foreach ( $comment_prop_to_export as $key => $name ) {
				$comment_id = $comment->comment_ID;
				$value      = get_comment_meta( $comment_id, 'wp_review_' . $key, true );
				if ( ! empty( $value ) ) {
					if ( 'features_rating' !== $key ) {
						$comment_data_to_export[] = array(
							'name'  => $name,
							'value' => $value,
						);
					} else {
						$post_id      = $comment->comment_post_ID;
						$type         = wp_review_get_post_user_review_type( $post_id );
						$rating_type  = wp_review_get_rating_type_data( $type );
						$items        = wp_review_get_review_items( $post_id );
						$rating_items = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );

						$review_ratings = array();
						foreach ( $items as $item_id => $item ) {
							if ( isset( $rating_items[ $item_id ] ) ) {
								$review_ratings[ $item['wp_review_item_title'] ] = $rating_items[ $item_id ] . ' of ' . $rating_type['max'];
							}
						}
						if ( ! empty( $review_ratings ) ) {
							foreach ( $review_ratings as $review_title => $review_value ) {
								$comment_data_to_export[] = array(
									'name'  => $review_title,
									'value' => $review_value,
								);
							}
						}
					}
				}
			}
			$data_to_export[] = array(
				'group_id'    => 'comments',
				'group_label' => __( 'Comments', 'wp-review' ),
				'item_id'     => "comment-{$comment->comment_ID}",
				'data'        => $comment_data_to_export,
			);
		}
		$done = count( $comments ) < $number;
		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}
}

// Filter function to register data exporter.
if ( ! function_exists( 'wp_review_register_data_exporter' ) ) {
	/**
	 * Registers data exporter.
	 *
	 * @param array $exporters Exporters.
	 * @return mixed
	 */
	function wp_review_register_data_exporter( $exporters ) {
		$exporters['wp-review'] = array(
			'exporter_friendly_name' => apply_filters( 'wp_review_exporter_friendly_name', __( 'WordPress Comments', 'wp-review' ) ),
			'callback'               => 'wp_review_data_exporter',
		);
		return $exporters;
	}
}

add_filter( 'wp_privacy_personal_data_exporters', 'wp_review_register_data_exporter', 9 );

/**
 * Switches to the main network site.
 * Function to switch to Network site, if global option is disabled in sub-site.
 *
 * @param string $option Option name.
 * @return bool
 */
function wp_review_switch_to_main( $option = '' ) {
	$value = false;
	if ( is_multisite() && ! is_main_site() ) {
		$site_id = get_current_blog_id();
		switch_to_blog( get_network()->site_id );
		$options      = get_option( 'wp_review_options' );
		$hide_options = isset( $options[ 'hide_global_options_' . $site_id ] ) ? $options[ 'hide_global_options_' . $site_id ] : false;
		if ( $hide_options ) {
			$value = true;
		} elseif ( $option ) {
			$hide_options = isset( $options[ $option . $site_id ] ) ? $options[ $option . $site_id ] : false;
			if ( $hide_options ) {
				$value = true;
			}
		}
		if ( $value ) {
			restore_current_blog();
		}
	}
	return $value;
}

/**
 * Gets network option.
 * Function to get option value from main-network site.
 *
 * @param string $key Option key.
 * @return mixed
 */
function wp_review_network_option( $key ) {
	$value = false;
	if ( is_multisite() && ! is_main_site() ) {
		$site_id = get_current_blog_id();
		switch_to_blog( get_network()->site_id );
		$options = get_option( 'wp_review_options' );
		$value   = isset( $options[ $key . $site_id ] ) ? $options[ $key . $site_id ] : '';
		restore_current_blog();
	}
	return $value;
}

/**
 * Gets capabilities.
 *
 * @return array
 */
function wp_review_get_capabilities() {
	return array(
		'wp_review_global_options'        => esc_html__( 'Global Options', 'wp-review' ),
		'wp_review_import_reviews'        => esc_html__( 'Import Reviews', 'wp-review' ),
		'wp_review_single_page'           => esc_html__( 'Single Page Settings', 'wp-review' ),
		'wp_review_features'              => esc_html__( 'Review Features', 'wp-review' ),
		'wp_review_links'                 => esc_html__( 'Review Links', 'wp-review' ),
		'wp_review_description'           => esc_html__( 'Review Description, Pros/Cons and Total Rating', 'wp-review' ),
		'wp_review_user_reviews'          => esc_html__( 'User Reviews', 'wp-review' ),
		'wp_review_purge_visitor_ratings' => esc_html__( 'Purge Visitor Ratings', 'wp-review' ),
		'wp_review_purge_comment_ratings' => esc_html__( 'Purge Comment Ratings', 'wp-review' ),
	);
}

add_filter(
	'option_page_capability_wpreview-settings-group',
	function( $cap ) {
		return 'wp_review_global_options';
	}
);

add_action(
	'members_register_cap_groups',
	function() {
		members_register_cap_group(
			'wp_review',
			array(
				'label'    => __( 'WP Review', 'wp-review' ),
				'caps'     => array(),
				'icon'     => 'dashicons-star-filled',
				'priority' => 10,
			)
		);
	}
);

add_action(
	'members_register_caps',
	function() {
		foreach ( wp_review_get_capabilities() as $key => $cap ) {
			members_register_cap(
				$key,
				array(
					'label' => $cap,
					'group' => 'wp_review',
				)
			);
		}
	}
);

add_action(
	'admin_init',
	function() {
		$wpr_compatibility = get_option( 'wp_review_compatibility' );
		if ( ! $wpr_compatibility ) {
			$role = get_role( 'administrator' );
			if ( $role ) {
				foreach ( wp_review_get_capabilities() as $key => $cap ) {
					$role->add_cap( $key );
				}
			}
			$role        = get_role( 'editor' );
			$editor_caps = array(
				'wp_review_notification_bar',
				'wp_review_single_page',
				'wp_review_features',
				'wp_review_links',
				'wp_review_description',
				'wp_review_user_reviews',
				'wp_review_purge_visitor_ratings',
				'wp_review_purge_comment_ratings',
			);
			foreach ( $editor_caps as $cap ) {
				$role->add_cap( $cap );
			}
			update_option( 'wp_review_compatibility', true );
		}
	}
);

/**
 * Checks if is in amp page.
 *
 * @since 3.2.4
 *
 * @return bool
 */
function wp_review_is_amp_page() {
	if ( function_exists( 'is_amp_endpoint' ) ) {
		return is_amp_endpoint();
	}
	if ( function_exists( 'ampforwp_is_amp_endpoint' ) ) {
		return ampforwp_is_amp_endpoint();
	}
	return false;
}

/**
 * Gets the current URL.
 *
 * @since 3.2.4
 *
 * @return string
 */
function wp_review_get_current_url() {
	global $wp;
	return home_url( $wp->request );
}

/**
 * Gets the current non-AMP URL.
 *
 * @since 3.2.4
 *
 * @return string
 */
function wp_review_get_current_non_amp_url() {
	$current_url = wp_review_get_current_url();
	if ( function_exists( 'amp_remove_endpoint' ) ) {
		return amp_remove_endpoint( $current_url );
	}
	return $current_url;
}

/**
 * Adds AMP CSS.
 *
 * @since 3.2.4
 */
function wp_review_add_amp_css() {
	if ( file_exists( WP_REVIEW_DIR . 'public/css/amp.css' ) ) {
		echo file_get_contents( WP_REVIEW_DIR . 'public/css/amp.css' );
	}
}
add_action( 'amp_post_template_css', 'wp_review_add_amp_css' );

/**
 * Adds AMP template data.
 *
 * @since 3.2.4
 *
 * @param array $data Template data.
 * @return array
 */
function wp_review_add_amp_template_data( $data ) {
	$data['font_urls']['FontAwesome'] = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
	return $data;
}
add_filter( 'amp_post_template_data', 'wp_review_add_amp_template_data' );

/**
 * Adds custom styles for better-amp plugin.
 *
 * @since 3.2.4
 */
function wp_review_add_better_amp_custom_styles() {
	if ( ! file_exists( WP_REVIEW_DIR . 'public/css/amp.css' ) ) {
		return;
	}
	better_amp_add_inline_style( file_get_contents( WP_REVIEW_DIR . 'public/css/amp.css' ), 'wp_review_css' );
}
add_action( 'better-amp/template/enqueue-scripts', 'wp_review_add_better_amp_custom_styles', 100 );

/**
 * Adds custom styles for weeblramp plugin.
 *
 * @since 3.2.4
 */
function wp_review_weeblramp_theme_css( $css ) {
	if ( ! file_exists( WP_REVIEW_DIR . 'public/css/amp.css' ) ) {
		return $css;
	}
	$css .= file_get_contents( WP_REVIEW_DIR . 'public/css/amp.css' );
	return $css;
}
add_filter( 'weeblramp_theme_css', 'wp_review_weeblramp_theme_css' );
add_filter( 'weeblramp_the_content', 'wp_review_inject_data' );
add_filter( 'weeblramp_wpautop_function', '__return_false' );

/**
 * Adds amp data for weeblramp plugin.
 *
 * @since 3.2.4
 */
function wp_review_weeblramp_get_request_data( $data ) {
	$data['custom_links'] .= '<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
	remove_filter( 'the_content', 'wp_review_inject_data' );
	return $data;
}
add_filter( 'weeblramp_get_request_data', 'wp_review_weeblramp_get_request_data' );
