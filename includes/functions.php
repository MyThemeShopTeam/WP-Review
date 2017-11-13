<?php
ob_start();
/**
 * WP Review
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

$options = get_option('wp_review_options');

/* Display the meta box data below 'the_content' hook. */
add_filter( 'the_content', 'wp_review_inject_data' );

/* Get review with Ajax */
//add_action('wp_ajax_mts_review_get_review', 'mts_review_get_review');
//add_action('wp_ajax_nopriv_mts_review_get_review', 'mts_review_get_review');
add_action( 'wp_ajax_wp_review_rate', 'wp_review_ajax_rate' );
add_action( 'wp_ajax_nopriv_wp_review_rate', 'wp_review_ajax_rate' );
add_action( 'wp_ajax_mts_review_feedback', 'mts_review_feedback' );
add_action( 'wp_ajax_nopriv_mts_review_feedback', 'mts_review_feedback' );
add_action( 'wp_ajax_wp_review_migrate_ratings', 'wp_review_ajax_migrate_ratings' );

/* Show with shortcode */
add_shortcode( 'wp-review', 'wp_review_shortcode' );
add_shortcode( 'wp-review-total', 'wp_review_total_shortcode' );
add_shortcode( 'wp-review-visitor-rating', 'wp_review_visitor_rating_shortcode' );
// aliases
add_shortcode( 'wp_review', 'wp_review_shortcode');
add_shortcode( 'wp_review_total', 'wp_review_total_shortcode');
add_shortcode( 'wp_review_visitor_rating', 'wp_review_visitor_rating_shortcode' );

// image sizes for the widgets
add_image_size( 'wp_review_large', 320, 200, true ); 
add_image_size( 'wp_review_small', 65, 65, true );

if (!empty($options['show_on_thumbnails'])) {
	add_filter( 'post_thumbnail_html', 'wp_review_image_html', 10, 5 );

	function wp_review_image_html( $html, $post_id, $post_image_id, $size, $attr ) {
		$options = get_option('wp_review_options');
		if (!empty($options['image_sizes']) && is_array($options['image_sizes']) && in_array($size, $options['image_sizes'])) {
			$html = '<div class="wp-review-thumbnail-wrapper">' . $html . wp_review_show_total(false, 'wp-review-on-thumbnail') . '</div>';
		}
		return $html;
	}
}

/**
 * Get the meta box data.
 * Replaced by wp_review_get_review_box() in v2.0
 *
 * @since 1.0
 * 
 */

function wp_review_get_data( $post_id = null ) {
	return wp_review_get_review_box( $post_id );
}


function wp_review_inject_data( $content ) {
    global $post;
    $options = get_option('wp_review_options');
	$custom_location = get_post_meta( $post->ID, 'wp_review_custom_location', true );
	$location = get_post_meta( $post->ID, 'wp_review_location', true );
	if (!$custom_location && !empty($options['review_location'])) {
		$location = $options['review_location'];
	}
    
	$location = apply_filters('wp_review_location', $location, $post->ID);

    if ( empty($location) || $location == 'custom' || ! is_main_query() || ! in_the_loop() || ! is_singular() ) {
        return $content;
    }
    $review = wp_review_get_review_box();
    if ( 'bottom' == $location ) {
        global $multipage, $numpages, $page;
        if( $multipage ) {
            if ($page == $numpages) {
                return $content .= $review;
            } else {
                return $content;
            }
        } else {
            return $content .= $review;
        }
	} elseif ( 'top' == $location ) {
		return $review .= $content;
	} else {
        return $content;
	}
}

/**
 * Retrieve only total rating.
 * To be used on archive pages, etc.
 *
 * @since 1.0
 * 
 */
function wp_review_show_total($echo = true, $class = 'review-total-only', $post_id = null, $args = array()) {
    global $post, $wp_review_rating_types;

    if (empty($post_id))
    	$post_id = $post->ID;
	
	$type = wp_review_get_post_review_type( $post_id );
	$user_type = wp_review_get_post_user_review_type( $post_id );
	if ( ! $type && ! $user_type )
		return '';

	wp_enqueue_style( 'wp_review-style' );

	$options = get_option('wp_review_options');
    $show_on_thumbnails_type = isset( $options['show_on_thumbnails_type'] ) ? $options['show_on_thumbnails_type'] : 'author';
    $show_on_thumbnails_type = apply_filters( 'wp_review_thumbnails_total', $show_on_thumbnails_type, $post_id, $args );// will override option

    $rating = $total = '';
    switch ( $show_on_thumbnails_type ) {
    	case 'author':
    		$total = get_post_meta( $post_id, 'wp_review_total', true );

		    if ( $type == 'point' || $type == 'percentage' ) {
		    	$rating = sprintf( $wp_review_rating_types[$type]['value_text'], $total );
		    } else {
		    	$rating = wp_review_rating( $total, $post_id );
		    }
    	break;
    	case 'visitors':
    		$total = get_post_meta( $post_id, 'wp_review_user_reviews', true );

		    if ( $user_type == 'point' || $user_type == 'percentage' ) {
		    	$rating = sprintf( $wp_review_rating_types[$user_type]['value_text'], $total );
		    } else {
		    	$rating = wp_review_user_rating( $post_id );
		    }
    	break;
    }
	
    $review = '';
    if ( !empty( $rating ) && !empty( $total ) ) {
		$review .= '<div class="review-type-'.$type.' '.esc_attr($class).' wp-review-show-total wp-review-total-'.$post_id.' wp-review-total-'.$type.'"> ';
		$review .= $rating;
		$review .= '</div>';
	}
    
    $review = apply_filters( 'wp_review_show_total', $review, $post_id, $type, $total );
    $review = apply_filters( 'wp_review_total_output', $review, $post_id, $type, $total, $class, $args );
    
    if ($echo)
        echo $review;
    else
        return $review;
}

function wp_review_total_shortcode($atts, $content) {
    $atts = shortcode_atts( array( 'id' => null, 'class' => 'review-total-only review-total-shortcode' ), $atts );
    $output = wp_review_show_total( false, $atts['class'], $atts['id'], array( 'shortcode' => true ) );

    return apply_filters( 'wp_review_total_shortcode', $output, $atts );
}

function wp_review_shortcode( $atts, $content = "") {
    $atts = shortcode_atts( array( 'id' => null ), $atts );
    $output = wp_review_get_data($atts['id']);

    return apply_filters( 'wp_review_shortcode', $output, $atts );
}

function wp_review_visitor_rating_shortcode( $atts, $content = "" ) {
	wp_enqueue_style( 'wp_review-style' );
	wp_enqueue_script( 'wp_review-js' );

    $atts = shortcode_atts( array( 'id' => null ), $atts );
	$id = empty( $atts['id'] ) ? get_the_ID() : absint( $atts['id'] );

	$text = '<div class="wp-review-' . $id . ' review-wrapper visitor-rating-shortcode">';
	$text .= wp_review_user_rating( $id );
	$text .= '</div>';

	return apply_filters( 'wp_review_visitor_rating_shortcode', $text, $atts );
}

function mts_get_post_reviews( $post_id, $force = false ){
	if ( ! $force && ( $post_reviews = get_post_meta( $post_id, 'wp_review_user_reviews', true ) ) && ( $review_count = get_post_meta( $post_id, 'wp_review_review_count', true ) ) ) {
		return array( 'rating' => $post_reviews, 'count' => $review_count );
	}

	if( is_numeric( $post_id ) && $post_id > 0 ){
		$comments = get_comments( array(
				'post_id' => $post_id,
				'type' => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'status' => 'approve'
		) );
		$rating = array_reduce( $comments, 'wpreview_comments_count_callback', 0 );

		$count = count( $comments );

		$post_reviews = array(
			'rating' => $count > 0 ? round( $rating / $count, 2 ) : 0,
			'count' => $count,
		);

		update_post_meta( $post_id, 'wp_review_user_reviews', $post_reviews['rating'] );
		update_post_meta( $post_id, 'wp_review_review_count', $post_reviews['count'] );

		return $post_reviews;
	}
}

function wpreview_comments_count_callback( $carry, $comment ) {
	$rating = get_comment_meta( $comment->comment_ID, WP_REVIEW_VISITOR_RATING_METAKEY, true );
	$carry += (int) $rating;
	return $carry;
}

/**
 *Check if user has reviewed this post previously
 *
 * @param $post_id
 * @param $user_id
 * @param $ip
 * @param string $type
 *
 * @return bool
 */
function hasPreviousReview( $post_id, $user_id, $ip, $type = 'any' ){
	if( is_numeric( $post_id ) && $post_id > 0 ){
		$args = array( 'post_id' => $post_id, 'count' => true, 'user_id' => 0 );
		set_query_var( 'wp_review_commenttype', $type );
		add_filter( 'pre_get_comments', 'wp_review_add_comment_type_to_query' );
		if ( $user_id ) {
			$args['user_id'] = $user_id;
		} else {
			set_query_var( 'wp_review_ip', $ip );
			add_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
		}
		$count = intval( get_comments( $args ) );
		remove_filter( 'pre_get_comments', 'wp_review_add_comment_type_to_query' );
		remove_filter( 'comments_clauses', 'wp_review_filter_comment_by_ip' );
		return $count > 0;
	}
	return false;
}

/**
 * Add the comment type to comment query.
 *
 * @param WP_Comment_Query $query
 *
 * @return WP_Comment_Query
 */
function wp_review_add_comment_type_to_query( \WP_Comment_Query $query ) {
	$commenttype = get_query_var( 'wp_review_commenttype' );
	if ( 'any' === $commenttype ) {
		$query->query_vars['type__in'] = array( WP_REVIEW_COMMENT_TYPE_VISITOR );
	} else {
		$query->query_vars['type'] = $commenttype;
	}
	return $query;
}

/**
 * Add a conditional to filter the comment query by IP.
 *
 * @param array $clauses
 *
 * @return array
 */
function wp_review_filter_comment_by_ip( array $clauses ) {
	global $wpdb;
	$clauses['where'] .= $wpdb->prepare( ' AND comment_author_IP = %s', get_query_var( 'wp_review_ip' ) );
	return $clauses;
}

function getPreviousReview( $post_id, $user_id, $ip, $type = 'any' ) {
	if( is_numeric( $post_id ) && $post_id > 0 ){
		$args = array( 'post_id' => $post_id, 'user_id' => 0 );
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
 * AJAX User review rating
 * Replaces mts_review_get_review()
 * @return null
 */
function wp_review_ajax_rate() {
	global $wp_review_rating_types;
    check_ajax_referer( 'wp-review-security', 'nonce' );
    $options = get_option('wp_review_options');
	$post_id = intval($_POST['post_id']);
	$user_id = is_user_logged_in() ? get_current_user_id() : 0;
	$review = round( abs( filter_input( INPUT_POST, 'review' ) ), 2 );
	$type = wp_review_get_post_user_review_type( $post_id );
    $uip = wp_review_get_user_ip();

    $output = array('status' => '', 'html' => '', 'rating_total' => '', 'rating_count' => '');
    
    if ( empty( $type ) ) {
    	// No user reviews allowed
    	echo wp_json_encode( $output );
		exit;
    }

	add_action( 'comment_duplicate_trigger', 'wp_review_comment_duplicate_trigger' );

	// don't allow higher rating than max
	if ($review > $wp_review_rating_types[$type]['max']) {
		$review = $wp_review_rating_types[$type]['max'];
	}

	if ( $review > 0 &&
		! hasPreviousReview( $post_id, $user_id, $uip, WP_REVIEW_COMMENT_TYPE_VISITOR ) &&
	     (
	        ( is_user_logged_in() && ! empty( $options['registered_only'] ) ) ||
            ( is_user_logged_in() && empty( $options['registered_only'] ) ) ||
            ( ! is_user_logged_in() && empty( $options['registered_only'] ) )
	     )
	) {
		if ( $insert = wp_insert_comment( array(
				'user_id' => $user_id,
				'comment_type' => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'comment_post_ID' => $post_id,
				'comment_parent' => 0,
				'comment_author_IP' => $uip,
				'comment_content' => sprintf( __('Visitor Rating: %s', 'wp-review'), sprintf( $wp_review_rating_types[$type]['value_text'], $review ) ),
				'comment_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']: '',
				'comment_date' => current_time('mysql'),
				'comment_date_gmt' => current_time( 'mysql', 1 ),
				'comment_approved' => 1
		) ) ) {
			if ( update_comment_meta( $insert, WP_REVIEW_VISITOR_RATING_METAKEY, $review ) ) {
				$post_reviews = mts_get_post_reviews( $post_id, true );

				// "lock" user review type when the first rating comes in
				// to prevent issues with changing types
				// update_post_meta( $post_id, 'wp_review_user_review_type_locked', 1 );

				//echo $post_reviews['rating'] . '|' . $post_reviews['count'];
				$output['status'] = 'ok';
				$output['html'] = wp_review_rating( $post_reviews['rating'], $post_id, array( 'user_rating' => true ) );
				$output['rating_total'] = $post_reviews['rating'];
				$output['rating_count'] = $post_reviews['count'];
				echo wp_json_encode( $output );
				exit;
			} else {
				wp_delete_comment( $insert );
			}
		}
	}
	$post_reviews = mts_get_post_reviews( $post_id );
	$output['status'] = 'fail';
	$output['error'] = 'db_error';
	$output['rating_total'] = $post_reviews['rating'];
	$output['rating_count'] = $post_reviews['count'];
	$output['html'] = wp_review_rating( $post_reviews['rating'], $post_id, array( 'user_rating' => true ) );
	echo wp_json_encode( $output );
	exit;
}

function wp_review_comment_duplicate_trigger( $commentdata ) {
    $post_reviews = mts_get_post_reviews( $commentdata['comment_post_ID'] );
    $output['status'] = 'fail';
    $output['error'] = 'duplicate';
    $output['rating_total'] = $post_reviews['rating'];
    $output['rating_count'] = $post_reviews['count'];
    $output['html'] = wp_review_rating( $post_reviews['rating'], $commentdata['comment_post_ID'], array( 'user_rating' => true ) );
    echo wp_json_encode( $output );
    exit;
}

/*
 * Get review with Ajax
 */
function mts_review_get_review() {
    // security
    check_ajax_referer( 'wp-review-security', 'nonce' );

	$options = get_option('wp_review_options');
	$post_id = intval($_POST['post_id']);
	$user_id = is_user_logged_in() ? get_current_user_id() : 0;
	$review = round( abs( filter_input( INPUT_POST, 'review' ) ), 2 );
	
	$review_text = $review;

    $uip = wp_review_get_user_ip();

	add_action( 'comment_duplicate_trigger', 'wp_review_comment_duplicate_trigger' );

	if ( $review > 0 &&
		! hasPreviousReview( $post_id, $user_id, $uip, WP_REVIEW_COMMENT_TYPE_VISITOR ) &&
	     (
	        ( is_user_logged_in() && ! empty( $options['registered_only'] ) ) ||
            ( is_user_logged_in() && empty( $options['registered_only'] ) ) ||
            ( ! is_user_logged_in() && empty( $options['registered_only'] ) )
	     )
	) {
		if ( $insert = wp_insert_comment( array(
				'user_id' => $user_id,
				'comment_type' => WP_REVIEW_COMMENT_TYPE_VISITOR,
				'comment_post_ID' => $post_id,
				'comment_parent' => 0,
				'comment_author_IP' => $uip,
				'comment_content' => sprintf(__('Visitor Rating: %s', 'wp-review'), $review_text),
				'comment_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']: '',
				'comment_date' => current_time('mysql'),
				'comment_date_gmt' => current_time( 'mysql', 1 ),
				'comment_approved' => 1
		) ) ) {
			if ( update_comment_meta( $insert, WP_REVIEW_VISITOR_RATING_METAKEY, $review ) ) {
				$post_reviews = mts_get_post_reviews( $post_id, true );

				echo $post_reviews['rating'] . '|' . $post_reviews['count'];
				exit;
			} else {
				wp_delete_comment( $insert );
			}
		}
	}

	echo 'MTS_REVIEW_DB_ERROR';
	exit;
}


function wp_review_theme_defaults($new_options, $force_change = false) {
	global $pagenow;
	$opt_name = 'wp_review_options_'.wp_get_theme();
	$options = get_option('wp_review_options');
	if (empty($options)) $options = array();
	$options_updated = get_option( $opt_name );
	// if the theme was just activated OR options weren't updated yet
	if ( empty( $options_updated ) || $options_updated != $new_options || $force_change || ( isset( $_GET['activated'] ) && $pagenow == 'themes.php' )) {
		update_option( 'wp_review_options', array_merge($options, $new_options) );
		update_option( $opt_name, $new_options );
	}
}

/**
 * Exclude review-type comments from being included in the comment query.
 *
 * @param WP_Comment_Query $query
 */
function wp_review_exclude_review_comments(\WP_Comment_Query $query) {
	if ( ! is_admin() && ( WP_REVIEW_COMMENT_TYPE_VISITOR !== $query->query_vars['type'] && ! in_array( WP_REVIEW_COMMENT_TYPE_VISITOR, (array) $query->query_vars['type__in'] )) ) {
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
 * @param array $comment_types
 *
 * @return mixed
 */
function wp_review_add_to_comment_table_dropdown( $comment_types ) {
	$comment_types[ WP_REVIEW_COMMENT_TYPE_VISITOR ] = __( 'Visitor Reviews', 'wp-review' );

	return $comment_types;
}
add_filter( 'admin_comment_types_dropdown', 'wp_review_add_to_comment_table_dropdown' );

/**
 * @param int $post_id
 *
 * @return string
 *
 * 0 - Disabled
 * 2 - Visitor Rating Only
 */
function wp_review_get_user_rating_setup( $post_id ) {
	/* Retrieve an existing value from the database. */
	$options = get_option( 'wp_review_options' );
	//$default = empty( $options['default_user_review_type'] ) ? WP_REVIEW_REVIEW_DISABLED : $options['default_user_review_type'];
	$default = WP_REVIEW_REVIEW_DISABLED;
	$userReviews = get_post_meta( $post_id, 'wp_review_userReview', true );
	$enabled = empty( $userReviews ) ? $default : $userReviews;
	if ( is_array( $userReviews ) ) {
		$enabled = $userReviews[0];
	}

	// Compatibility with the old option.
	if ( '1' === $enabled ) {
		$enabled = WP_REVIEW_REVIEW_VISITOR_ONLY;
	}

	return $enabled;
}

/**
 * Exclude visitor ratings when updating a post's comment count.
 * @param $post_id
 * @param $new
 * @param $old
 *
 * @internal param $comment_id
 * @internal param $comment
 */
function wp_review_exclude_visitor_review_count( $post_id, $new, $old ) {
	global $wpdb;
	$count = get_comments( array(
			'type__not_in' => array( WP_REVIEW_COMMENT_TYPE_VISITOR ),
			'post_id' => $post_id,
			'count' => true
	) );
	$wpdb->update( $wpdb->posts, array( 'comment_count' => $count ), array( 'ID' => $post_id ) );

	// Update user review count.
	mts_get_post_reviews( $post_id, true );

	clean_post_cache( $post_id );
}
add_action( 'wp_update_comment_count', 'wp_review_exclude_visitor_review_count', 10, 3 );

/**
 * Get the schema type of a review.
 * @param int $post_id
 *
 * @return mixed|string
 */
function wp_review_get_review_schema( $post_id ) {
	return 'Thing';
}

/**
 * Get the IP of the current user.
 *
 * @return string
 */
function wp_review_get_user_ip() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

/**
 * Get the HTML for user reviews in review box.
 *
 * @param $post_id
 * @param bool $votable
 * @param bool $force_display
 *
 * @return string
 */
function wp_review_user_review( $post_id, $votable = true, $force_display = false ) {
	$review = '';

	if ( $force_display || in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_VISITOR_ONLY ) ) ) {
		$allowedClass      = 'allowed-to-rate';
		$hasNotRatedClass  = ' has-not-rated-yet';
		$postReviews       = mts_get_post_reviews( $post_id );
		$userTotal         = $postReviews['rating'];
		$usersReviewsCount = $postReviews['count'];
		$total             = get_post_meta( $post_id, 'wp_review_total', true );
		$type = get_post_meta( $post_id, 'wp_review_user_review_type', true );

		$options = get_option('wp_review_options');
		$custom_colors = get_post_meta( $post_id, 'wp_review_custom_colors', true );
		
		$colors['color'] = get_post_meta( $post_id, 'wp_review_color', true );
		if( empty($colors['color']) ) $colors['color'] = '#333333';
		$colors['type']  = get_post_meta( $post_id, 'wp_review_type', true );
		$colors['fontcolor'] = get_post_meta( $post_id, 'wp_review_fontcolor', true );
		$colors['bgcolor1']  = get_post_meta( $post_id, 'wp_review_bgcolor1', true );
		$colors['bgcolor2']  = get_post_meta( $post_id, 'wp_review_bgcolor2', true );
		$colors['bordercolor']  = get_post_meta( $post_id, 'wp_review_bordercolor', true );
		if ( ! $custom_colors && ! empty($options['colors'] ) && is_array($options['colors'] ) ) {
			$colors = array_merge($colors, $options['colors']);
		}
	    $colors = apply_filters('wp_review_colors', $colors, $post_id);
	    $color = $colors['color'];

		$user_id = '';
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}


		if ( $userTotal == '' ) {
			$userTotal = '0.0';
		}
		$value = $userTotal;

		if ( ! $votable || hasPreviousReview( $post_id, $user_id, wp_review_get_user_ip(), WP_REVIEW_COMMENT_TYPE_VISITOR ) || ( ! is_user_logged_in() && ! empty( $options['registered_only'] ) ) ) {
			$hasNotRatedClass = '';
		}

		$class = $allowedClass . $hasNotRatedClass;

		$template = mts_get_template_path( $type, 'star-output' );
		set_query_var( 'rating', compact( 'value', 'usersReviewsCount', 'user_id', 'class', 'post_id', 'color', 'colors' ) );
		ob_start();
		load_template( $template, false );
		$review = ob_get_contents();
		ob_end_clean();



		if ( $userTotal !== '0.0' && $total === '' ) {// dont'show if no user ratings and there is review
			$review .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                <meta itemprop="ratingValue" content="' . $userTotal . '" />
                                <meta itemprop="reviewCount" content="' . $usersReviewsCount . '" />
                            </div>';
		}
	}

	return $review;
}

/**
 * Get available review types/templates.
 *
 * @param string $type
 *
 * @return array
 */
/* 
function wp_review_get_review_types( $type = '' ) {
	$dirs = array(
			WP_REVIEW_DIR . 'templates/',
			get_stylesheet_directory() . '/wp-review/',
	);
	$types = array();

	foreach ( $dirs as $dir ) {
		if ( is_dir( $dir ) ) {
			$handle = opendir( $dir );
			while ( $file = readdir( $handle ) ) {
				if ( '..' === $file || '.' === $file ) {
					continue;
				}
				$key = basename( $file, '.php' );

				if ( ! empty( $type )  && substr( $key, -strlen( $type ) ) !== $type ) {
					continue;
				}
				if ( empty( $type ) and strpos( $key, '-' ) !== false ) {
					continue;
				}

				$template_data = implode( '', file( $dir . $file ) );
				if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
					$types[ $key ] = $name[1];
				} else {
					$keys_arr = explode( '-', $key );
					$types[ $key ] = ucfirst( reset( $keys_arr ) );
				}
			}
		}
	}

	return $types;
}
*/

/**
 * Get the path to a template prioritizing theme directory first.
 *
 * @param $type
 * @param string $default
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

function wp_review_ajax_migrate_ratings() {
	$start = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;
	$limit = 100;

	if ( get_option( 'wp_review_has_migrated', false ) ) {
		return;
	}

	global $wpdb;

	$current_blog_id = get_current_blog_id();

	$query = $wpdb->get_results( 'SELECT * from '.$wpdb->base_prefix.'mts_wp_reviews WHERE blog_id = '.$current_blog_id.' LIMIT '.$limit.' OFFSET '.$start );

	foreach ( $query as $review ) {
		
		if ($review->rate == 0)
			continue; // skip 0-star ratings

		if ( $insert = wp_insert_comment( array(
			'user_id' => $review->user_id,
			'comment_type' => WP_REVIEW_COMMENT_TYPE_VISITOR,
			'comment_post_ID' => $review->post_id,
			'comment_parent' => 0,
			'comment_content' => sprintf(__('Visitor Rating: %s', 'wp-review'), sprintf( __( '%s Stars' , 'wp-review' ), $review->rate ) ),
			'comment_author_IP' => $review->user_ip,
			'comment_date' => gmdate( 'Y-m-d H:i:s', ( strtotime( $review->date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ),
			'comment_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( $review->date ) ),
			'comment_approved' => 1
		) ) ) {
			if ( update_comment_meta( $insert, WP_REVIEW_VISITOR_RATING_METAKEY, $review->rate ) ) {
				// Purge cache.
				mts_get_post_reviews( $review->post_id, true );
			} else {
				wp_delete_comment( $insert );
			}
		}
		
	}

	$end = $start + count($query);//$wpdb->num_rows;
	//$migrated_rows = get_option( 'wp_review_migrated_rows', 0 );
	update_option( 'wp_review_migrated_rows', $end );

	$total_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM '.$wpdb->base_prefix.'mts_wp_reviews WHERE blog_id = '.$current_blog_id );
	$migration_finished = 0;
	if ( $total_rows == $end ) {
		update_option( 'wp_review_has_migrated', 1 );
		$migration_finished = 1;
	}

	echo wp_json_encode( array( 'start' => $start, 'lastrow' => $end, 'rowsleft' => $total_rows - $end, 'finished' => $migration_finished ) );


	die();
}

/*
 * Custom Rating Types
 * 
 */
$wp_review_rating_types = array();

function wp_review_register_rating_type( $rating_type, $args ) {
	global $wp_review_rating_types;
	

	if (empty($args['output_template']) && empty($args['template']))
		return false;

	// If it has combined 'template' 
	// or 'input_template' (for user rating)
	if (!empty($args['template']) || !empty($args['input_template']))
		$args['user_rating'] = true;
	else
		$args['user_rating'] = false;

	$wp_review_rating_types[$rating_type] = $args;
	
	return true;
}
add_action( 'init', 'wp_review_register_default_rating_types' );

function wp_review_register_default_rating_types() {
	wp_review_register_rating_type( 'star', array(
		'label' => __('Star', 'wp-review'),
		'max' => 5,
		'decimals' => 1,
		'value_text' => __('%s Stars', 'wp-review'),
		'value_text_singular' => __('%s Star', 'wp-review'),
		'input_template' => WP_REVIEW_DIR . 'rating-types/star-input.php',
		'output_template' => WP_REVIEW_DIR . 'rating-types/star-output.php',
	) );

	wp_review_register_rating_type( 'point', array(
		'label' => __('Point', 'wp-review'),
		'max' => 10,
		'decimals' => 1,
		'value_text' => __('%s/10', 'wp-review'),
		'value_text_singular' => __('%s/10', 'wp-review'),
		'input_template' => WP_REVIEW_DIR . 'rating-types/point-input.php',
		'output_template' => WP_REVIEW_DIR . 'rating-types/point-output.php',
	) );

	wp_review_register_rating_type( 'percentage', array(
		'label' => __('Percentage', 'wp-review'),
		'max' => 100,
		'decimals' => 0,
		'value_text' => __('%s%%', 'wp-review'),
		'value_text_singular' => __('%s%%', 'wp-review'),
		'input_template' => WP_REVIEW_DIR . 'rating-types/percentage-input.php',
		'output_template' => WP_REVIEW_DIR . 'rating-types/percentage-output.php',
	) );
}

function wp_review_get_post_review_type( $post_id = null ) {
	global $post, $wp_review_rating_types;
	
	if ( empty( $post_id ) ) {
		if ( is_a( $post, 'WP_Post') )
			$post_id = $post->ID;
		else
			return '';
	}

	$type = get_post_meta( $post_id, 'wp_review_type', true );
	if ( empty( $type ) )
		return ''; // not a review

	$output = '';
	if ( isset( $wp_review_rating_types[$type] ) )
		$output = $type;
	else
		$output = 'star'; // fallback if specific $type is not available anymore

	return apply_filters( 'wp_review_get_review_type', $output, $post_id );
}

function wp_review_get_post_user_review_type( $post_id = null ) {
	global $post, $wp_review_rating_types;
	
	if ( empty( $post_id ) )
		$post_id = $post->ID;

	$type = wp_review_get_post_review_type( $post_id );
	if ( empty( $type ) )
		return ''; // not a review

	$userreview_type = get_post_meta( $post_id, 'wp_review_user_review_type', true );
	if ( empty( $userreview_type ) )
		$userreview_type = 'star';

	$userreviews = in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_VISITOR_ONLY ) );
	if ( ! $userreviews )
		return ''; // user ratings not enabled

	$output = '';
	if ( isset( $wp_review_rating_types[$userreview_type] ) && $wp_review_rating_types[$userreview_type]['user_rating'] )
		$output = $userreview_type;
	else
		$output = 'star'; // fallback if specific $type is not available
	
	return apply_filters( 'wp_review_get_user_review_type', $output, $post_id );
}

/*
 * Custom Box Templates
 * 
 */
function wp_review_get_post_box_template( $post_id ) {
	global $post, $wp_review_rating_types;
	
	if ( empty( $post_id ) )
		$post_id = $post->ID;

	$type = wp_review_get_post_review_type( $post_id );
	if ( empty( $type ) )
		return ''; // not a review

	$template = get_post_meta( $post_id, 'wp_review_box_template', true );
	if ( empty( $template ) || ! wp_review_locate_box_template( $template ) )
		$template = 'default.php'; // fallback to default.php
	
	return apply_filters( 'wp_review_get_box_template', $template, $post_id );
}

function wp_review_locate_box_template( $template_name, $return_full_path = true ) {
	// We look for box templates in:
	// 1. plugins_dir/box-templates
	// 2. theme_dir/wp-review
	// 3. childtheme_dir/wp-review
	// 4... Use filter to add more
	$default_paths = array(
		WP_REVIEW_DIR.'box-templates', 
		get_template_directory().'/wp-review',
		get_stylesheet_directory().'/wp-review',
	);
	$template_paths = apply_filters( 'wp_review_box_template_paths', $default_paths );


	$paths = array_reverse($template_paths);
	$located = '';
	$path_partial = '';
	foreach ($paths as $path) {
		if (file_exists($full_path = trailingslashit($path).$template_name)) {
			$located = $full_path;
			$path_partial = $path;
			break;
		}
	}
	return ($return_full_path ? $located : $path_partial);
}

/**
 * Shows rating using output template
 * 
 * @param  mixed $value rating value
 * @param  mixed $post_id  optional post ID
 * @return [type]        [description]
 */
function wp_review_rating( $value, $post_id = null, $args = array() ) {
	global $wp_review_rating_types, $post;

	if ( ! empty( $args['user_rating'] ) ) {
		$type = wp_review_get_post_user_review_type( $post_id );
	} else {
		$type = wp_review_get_post_review_type( $post_id );
	}
	
	if ( empty( $type ) )
		return '';
	
	if (empty($post_id))
		$post_id = $post->ID;

	$options = get_option('wp_review_options');
	$custom_colors = get_post_meta( $post_id, 'wp_review_custom_colors', true );
	$colors['color'] = get_post_meta( $post_id, 'wp_review_color', true );
	if( empty($colors['color']) ) $colors['color'] = '#333333';
	$colors['type']  = get_post_meta( $post_id, 'wp_review_type', true );
	$colors['fontcolor'] = get_post_meta( $post_id, 'wp_review_fontcolor', true );
	$colors['bgcolor1']  = get_post_meta( $post_id, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']  = get_post_meta( $post_id, 'wp_review_bgcolor2', true );
	$colors['bordercolor']  = get_post_meta( $post_id, 'wp_review_bordercolor', true );
	if ( ! $custom_colors && ! empty($options['colors'] ) && is_array($options['colors'] ) ) {
		$colors = array_merge($colors, $options['colors']);
	}
    $colors = apply_filters('wp_review_colors', $colors, $post_id);

    // Override colors if is_admin()
    if (is_admin() && !defined('DOING_AJAX')) {
    	$admin_colors = array(
    		'color' => '#444444',
    		'bgcolor1' => '#ffffff',
		);
		$colors = array_merge($colors, $admin_colors);
    }
    $color = $colors['color'];

    // don't allow higher rating than max
	if ($value > $wp_review_rating_types[$type]['max']) {
		$value = $wp_review_rating_types[$type]['max'];
	}
	
	$template = $wp_review_rating_types[$type]['output_template'];
	$comment_rating = false;
	set_query_var( 'rating', compact( 'value', 'post_id', 'type', 'args', 'comment_rating', 'color', 'colors' ) );
	ob_start();
	load_template( $template, false );
	$review = ob_get_contents();
	ob_end_clean();
	return $review;
}

function wp_review_user_rating( $post_id = null, $args = array() ) {
	global $wp_review_rating_types;
	$type = wp_review_get_post_user_review_type( $post_id );
	
	if ( empty( $type ) )
		return '';
	
	$review = '';

	$postReviews = mts_get_post_reviews( $post_id );
	$value     	 = $postReviews['rating'];
	$count 		 = $postReviews['count'];
	$total       = get_post_meta( $post_id, 'wp_review_total', true );

	$user_id = '';
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( $value == '' ) {
		$value = '0.0';
	}

	if ( hasPreviousReview( $post_id, $user_id, wp_review_get_user_ip(), WP_REVIEW_COMMENT_TYPE_VISITOR ) || ( ! is_user_logged_in() && ! empty( $options['registered_only'] ) ) ) {
		return wp_review_rating( $value, $post_id, array( 'user_rating' => true ) ); // return just output template
	}

	$options = get_option('wp_review_options');
	$custom_colors = get_post_meta( $post_id, 'wp_review_custom_colors', true );
	$colors['color'] = get_post_meta( $post_id, 'wp_review_color', true );
	if( empty($colors['color']) ) $colors['color'] = '#333333';
	$colors['type']  = get_post_meta( $post_id, 'wp_review_type', true );
	$colors['fontcolor'] = get_post_meta( $post_id, 'wp_review_fontcolor', true );
	$colors['bgcolor1']  = get_post_meta( $post_id, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']  = get_post_meta( $post_id, 'wp_review_bgcolor2', true );
	$colors['bordercolor']  = get_post_meta( $post_id, 'wp_review_bordercolor', true );
	if ( ! $custom_colors && ! empty($options['colors'] ) && is_array($options['colors'] ) ) {
		$colors = array_merge($colors, $options['colors']);
	}
    $colors = apply_filters('wp_review_colors', $colors, $post_id);
    $color = $colors['color'];

	$rating_type_template = $wp_review_rating_types[$type]['input_template'];
	$comment_rating = false;
	set_query_var( 'rating', compact( 'value', 'post_id', 'comment_rating', 'args', 'color', 'colors' ) );
	ob_start();
	load_template( $rating_type_template, false );
	$review = '<div class="wp-review-user-rating wp-review-user-rating-'.$type.'">'.ob_get_contents().'</div>';
	ob_end_clean();

	if ( $value !== '0.0' && $total === '' ) {// dont'show if no user ratings and there is review
		$review .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                            <meta itemprop="ratingValue" content="' . $userTotal . '" />
                            <meta itemprop="reviewCount" content="' . $usersReviewsCount . '" />
                        </div>';
	}
	return $review;
}

/**
 * Returns WP Review box html using the box template chosen for the review
 * Replaces wp_review_get_data()
 * 
 * @param  [type] $post_id [description]
 * @return [type]          [description]
 */
function wp_review_get_review_box( $post_id = null ) {
	global $post;
	if (empty($post_id))
	    $post_id = $post->ID;
	$type = wp_review_get_post_review_type( $post_id );
	if ( ! $type )
		return '';

	// Load variables
	$options = get_option('wp_review_options');

	/* Retrieve the meta box data. */
	$heading     = get_post_meta( $post_id, 'wp_review_heading', true );
	$desc_title  = get_post_meta( $post_id, 'wp_review_desc_title', true );
	if ( ! $desc_title ) $desc_title = __('Summary', 'wp-review');
	$desc        = get_post_meta( $post_id, 'wp_review_desc', true );
	$items       = get_post_meta( $post_id, 'wp_review_item', true );
	$type        = get_post_meta( $post_id, 'wp_review_type', true );
	$total       = get_post_meta( $post_id, 'wp_review_total', true );
	$hide_desc   = get_post_meta( $post_id, 'wp_review_hide_desc', true );
	$schema      = wp_review_get_review_schema( $post_id );
	$links       = get_post_meta( $post_id, 'wp_review_links', true );

	$custom_author = get_post_meta( $post_id, 'wp_review_custom_author', true );
	$author_field  = get_post_meta( $post_id, 'wp_review_author', true );
	
	$author = ( !$author_field || empty( $author_field ) || !$custom_author ) ? get_the_author() : $author_field;
	$add_backlink = ! empty( $options['add_backlink'] ) ? true : false;

	$colors = array();
	$colors['custom_colors'] = get_post_meta( $post_id, 'wp_review_custom_colors', true );
	$colors['custom_location'] = get_post_meta( $post_id, 'wp_review_custom_location', true );
	$colors['custom_width'] = get_post_meta( $post_id, 'wp_review_custom_width', true );

	$colors['color'] = get_post_meta( $post_id, 'wp_review_color', true );
	if( empty($colors['color']) ) $colors['color'] = '#333333';
	$colors['type']  = get_post_meta( $post_id, 'wp_review_type', true );
	$colors['fontcolor'] = get_post_meta( $post_id, 'wp_review_fontcolor', true );
	$colors['bgcolor1']  = get_post_meta( $post_id, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']  = get_post_meta( $post_id, 'wp_review_bgcolor2', true );
	$colors['bordercolor']  = get_post_meta( $post_id, 'wp_review_bordercolor', true );
	$colors['total'] = get_post_meta( $post_id, 'wp_review_total', true );
    
    if ( ! $colors['custom_colors'] && ! empty($options['colors'] ) && is_array($options['colors'] ) ) {
		$colors = array_merge($colors, $options['colors']);
	}
    $colors = apply_filters('wp_review_colors', $colors, $post_id);

	$width = get_post_meta( $post_id, 'wp_review_width', true );
	if (empty($width)) $width = 100;
	$align = get_post_meta( $post_id, 'wp_review_align', true );
	if (empty($align)) $align = 'left';

    if (!$colors['custom_width']) {
		$width = ! empty($options['width']) ? $options['width'] : 100;
		$align = ! empty($options['align']) ? $options['align'] : 'left';
	}

    $post_types = get_post_types( array('public' => true), 'names' );
    $excluded_post_types = apply_filters('wp_review_excluded_post_types', array('attachment'));
    $allowed_post_types = array_diff($post_types, $excluded_post_types);

	$user_review = in_array( wp_review_get_user_rating_setup( $post_id ), array( WP_REVIEW_REVIEW_VISITOR_ONLY ) );
    $user_review_type = '';
    $user_review_total = '';
    $user_review_count = 0;
    $user_has_reviewed = false;
    if ( $user_review ) {
    	$user_review_type = wp_review_get_post_user_review_type( $post_id );
    }
    if ( $user_review ) {
		$postReviews       = mts_get_post_reviews( $post_id );
		$user_review_total = $postReviews['rating'];
		$user_review_count = $postReviews['count'];
		$user_id = is_user_logged_in() ? get_current_user_id() : 0;
		$uip = wp_review_get_user_ip();
		if ( hasPreviousReview( $post_id, $user_id, $uip, WP_REVIEW_COMMENT_TYPE_VISITOR ) )
			$user_has_reviewed = true;
    }

    

    $template = wp_review_get_post_box_template( $post_id );
    $box_template_path = wp_review_locate_box_template( $template );
    // pass variables to template
	set_query_var( 'review', compact( 
		'post_id', 
		'type',
		'heading', 
		'author', 
		'items', 
		'hide_desc', 
		'desc', 
		'desc_title', 
		'total', 
		'colors', 
		'width',
		'align',
		'schema', 
		'links',
		'user_review',
		'user_review_type',
		'user_review_total',
		'user_review_count',
		'user_has_reviewed',
		'add_backlink'
	) );
	ob_start();
	load_template( $box_template_path, false );
	$review = ob_get_contents();
	ob_end_clean();
	$review = apply_filters('wp_review_get_data', $review, $post_id, $type, $total, $items);
	return $review;// . wp_review_color_output( $post_id ); // add color CSS to output
}


function wp_review_get_box_template_info( $template = false ) {
	$default_template_headers = array(
		'Name' => 'WP Review',
		'TemplateURI' => 'Template URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI'
	);

	if ( ! $template )
		$template = wp_review_get_post_box_template();

	$path = wp_review_locate_box_template( $template );
	
	if ( $path )
		return get_file_data( $path, $default_template_headers );
	else
		return array( $default_template_headers );
}

/**
 *  Returns absolute path to template directory.
 *  @return string path
 */
function wp_review_get_box_template_directory() {
		$template = wp_review_get_post_box_template();
		if ( ! $template )
			return '';

		$current_template_directory = wp_review_locate_box_template( $template );

		return dirname($current_template_directory);
	}

/**
 *  Returns template directory URI. To be used in template file.
 *  @return string path
 */
function wp_review_get_box_template_directory_uri() {
	// let's hope this will work in most cases
	return get_bloginfo( 'url' ).'/'.str_replace(ABSPATH, '', wp_review_get_box_template_directory());
}


function wp_review_get_box_templates_list() {

	$default_paths = array(
		WP_REVIEW_DIR.'box-templates', 
		get_template_directory().'/wp-review',
		get_stylesheet_directory().'/wp-review',
	);
	$paths = apply_filters( 'wp_review_box_template_paths', $default_paths );

	$templates = array();
	
	foreach ($paths as $path) {
		$path = trailingslashit( $path );
		// Look for files containing our header 'Launcher template'
		$files = (array) wp_review_scandir( $path, 'php', 2 );
		foreach ( $files as $file => $full_path ) {//echo ' <br> '.$file.' - '.$full_path;
			if ( ! $full_path || ! preg_match( '|WP Review:(.*)$|mi', file_get_contents( $full_path ), $header ) )
				continue;
			
			$templates[ $file ] = wp_review_get_box_template_info( $file );
			$templates[ $file ]['path'] = $path;
		}
	}
	return $templates;
}

function wp_review_get_rating_types() {
	global $wp_review_rating_types;
	return $wp_review_rating_types;
}

function wp_review_scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
	if ( ! is_dir( $path ) )
		return false;
	if ( $extensions ) {
		$extensions = (array) $extensions;
		$_extensions = implode( '|', $extensions );
	}
	$relative_path = trailingslashit( $relative_path );
	if ( '/' == $relative_path )
		$relative_path = '';
	$results = scandir( $path );
	$files = array();
	foreach ( $results as $result ) {
		if ( '.' == $result[0] )
			continue;
		if ( is_dir( $path . '/' . $result ) ) {
			if ( ! $depth || 'CVS' == $result )
				continue;
			$found = wp_review_scandir( $path . '/' . $result, $extensions, $depth - 1 , $relative_path . $result );
			$files = array_merge_recursive( $files, $found );
		} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
			$files[ $relative_path . $result ] = $path . '/' . $result;
		}
	}
	return $files;
}


add_action( 'init', 'wp_review_add_admin_columns' );
function wp_review_add_admin_columns() {
	$post_types = get_post_types( array('public' => true), 'names' );
	$excluded_post_types = apply_filters('wp_review_excluded_post_types', array('attachment'));
	$allowed_post_types = array_diff($post_types, $excluded_post_types);
	foreach ($allowed_post_types as $key => $value) {
		// Add post list table column
		add_filter('manage_'.$value.'_posts_columns', 'wp_review_post_list_column');
		// Post list table column content
		add_action('manage_'.$value.'_posts_custom_column', 'wp_review_post_list_column_content', 10, 2);
	}
}
function wp_review_post_list_column($defaults) {
	    $defaults['wp_review_rating'] = __('Rating', 'wp-review');
	    return $defaults;
}
function wp_review_post_list_column_content($column_name, $post_ID) {
    if ($column_name == 'wp_review_rating') {
    	$total = get_post_meta( $post_ID, 'wp_review_total', true );
    	if ( $total )
        	echo wp_review_rating($total, $post_ID);
        else
        	echo '<span class="no-rating">'.__( 'No Rating', 'wp-review' ).'</span>';
    }
}

function wp_review_get_backlink() {
	$backlink_text = '<span class="wp-review-plugin-backlink">'.sprintf(__('Powered by %s', 'wp-review'), '<a href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Link+CPC&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg" target="_blank">'.__('WP Review', 'wp-review').'</a>').'</span>';
	return $backlink_text;
}

// Notice about migrating ratings
add_action( 'admin_notices', 'wp_review_migrate_notice' );
add_action('admin_init', 'wp_review_migrate_notice_ignore');

function wp_review_migrate_notice_ignore() {
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if ( isset($_GET['wp_review_migrate_notice_ignore']) && '1' == $_GET['wp_review_migrate_notice_ignore'] ) {
         add_user_meta($user_id, 'wp_review_migrate_notice_ignore', 'true', true);
	}
}
function wp_review_migrate_notice() {
	// Migrate
	global $wpdb, $current_user;
	$user_id = $current_user->ID;
	if ( get_user_meta($user_id, 'wp_review_migrate_notice_ignore') )
		return;
	
	$has_migrated = get_option( 'wp_review_has_migrated', false );
	if ($has_migrated) 
		return;

	$current_blog_id = get_current_blog_id();
	$total_rows = 0;
	$rows_left = 0;
	$migrated_rows = get_option( 'wp_review_migrated_rows', 0 );
	if ( ! $has_migrated && $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->base_prefix}mts_wp_reviews'") == "{$wpdb->base_prefix}mts_wp_reviews") {
		// Table exists and not migrated (fully) yet
		$total_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM '.$wpdb->base_prefix.'mts_wp_reviews WHERE blog_id = '.$current_blog_id );
		$rows_left = $total_rows - $migrated_rows;
	}

	if (!$rows_left) 
		return;
    ?>
    <div class="updated notice-info wp-review-notice">
        <p><?php printf(__( 'Thank you for updating WP Review. Your existing user ratings will show up after importing them in %s.', 'wp-review' ), '<a href="'.admin_url( 'options-general.php?page=wp-review%2Fadmin%2Foptions.php#migrate' ).'">'.__('Settings &gt; WP Review &gt; Migrate Ratings', 'wp-review').'</a>'); ?></p><a class="notice-dismiss" href="<?php echo esc_url(add_query_arg('wp_review_migrate_notice_ignore', '1')); ?>"></a>
    </div>
    <?php
}

/* Display a notice*/

add_action('admin_notices', 'wp_review_admin_notice');

function wp_review_admin_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    /* Only show the notice 2 days after plugin activation */
    if ( ! get_user_meta($user_id, 'wp_review_ignore_notice') && time() >= (get_option( 'wp_review_activated', 0 ) + (2 * 24 * 60 * 60)) ) {
        echo '<div class="updated notice-info wp-review-notice" id="wpreview-notice" style="position:relative;">';
			printf(__('<p>Create Reviews Easily & Rank Higher In Search Engines - <a target="_blank" href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Notification+Link&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg"><strong>WP Review Pro Plugin</strong></a></p><a class="notice-dismiss" href="%1$s"></a>'), '?wp_review_admin_notice_ignore=0');
			echo "</div>";
    }
}

add_action('admin_init', 'wp_review_admin_notice_ignore');

function wp_review_admin_notice_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['wp_review_admin_notice_ignore']) && '0' == $_GET['wp_review_admin_notice_ignore'] ) {
             add_user_meta($user_id, 'wp_review_ignore_notice', 'true', true);
    }
}
