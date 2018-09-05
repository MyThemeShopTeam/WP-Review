<?php
/**
 * Popup feature
 *
 * @package WP_Review
 *
 * @since 3.0.0
 */

/**
 * Gets popup option.
 *
 * @param  string $name    Option name.
 * @param  mixed  $default Default value.
 * @return mixed
 */
function wp_review_popup_option( $name = '', $default = null ) {
	static $options = null;
	if ( ! is_array( $options ) ) {
		$options = get_option( 'wp_review_popup', array() );
	}

	if ( ! $name ) {
		return $options;
	}

	$value = isset( $options[ $name ] ) ? $options[ $name ] : $default;
	$value = apply_filters( 'wp_review_popup_option_' . $name, $value );
	$value = apply_filters( 'wp_review_popup_option', $value, $name );
	return $value;
}


/**
 * Gets popup default values.
 *
 * @return array
 */
function wp_review_popup_defaults() {
	return apply_filters( 'wp_review_popup_defaults', array(
		'enable'          => false,
		'width'           => '800px',
		'animation_in'    => 'bounceIn',
		'animation_out'   => 'bounceOut',
		'overlay_color'   => '#0b0b0b',
		'overlay_opacity' => 0.8,
		'post_type'       => 'post',
		'queryby'         => 'category',
		'orderby'         => 'random',
		'category'        => 0,
		'tag'             => 0,
		'review_type'     => 0,
		'limit'           => 6,
		'expiration'      => 30, // Number of days which cookie expired.
		'cookie_name'     => 'wpr-popup',
		'delay'           => 0,
		'show_on_load'    => false,
		'show_on_reach_bottom' => false,
		'exit_intent'          => true,
		'screen_size_check'    => false,
		'screen_width'         => '',
	) );
}


/**
 * Gets post popup config.
 *
 * @param  int  $post_id       Post ID.
 * @param  bool $merge_default Merge with default value or not.
 * @return array
 */
function wp_review_get_post_popup( $post_id, $merge_default = true ) {
	$config = (array) get_post_meta( $post_id, 'wp_review_popup', true );
	if ( $merge_default ) {
		$default = wp_review_popup_defaults();
		$default['queryby'] = 'same_category';
		$config = wp_parse_args( $config, $default );
	}
	return apply_filters( 'wp_review_post_popup_config', $config, $post_id );
}


/**
 * Gets popup config.
 *
 * @return array
 */
function wp_review_get_popup_config() {
	$config = wp_review_popup_option();
	$config = wp_parse_args( $config, wp_review_popup_defaults() );
	if(is_multisite()) {
		if(wp_review_switch_to_main('hide_general_popup_')) {
			$config['enable'] = false;
		}
		restore_current_blog();
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$post_config = wp_review_get_post_popup( $post_id, false );
		if ( empty( $post_config['enable'] ) ) {
			$post_config['enable'] = 'default';
		}
		if ( 'none' === $post_config['enable'] ) {
			$config['enable'] = false;
		} elseif ( 'custom' === $post_config['enable'] ) {
			$config = array_merge( $config, $post_config );
			$config['enable'] = true;
		}

	}

	return apply_filters( 'wp_review_get_popup_config', $config );
}


/**
 * Shows popup.
 */
function wp_review_show_popup() {
	$config = wp_review_get_popup_config();

	if ( ! $config['enable'] ) {
		return;
	}
	wp_review_popup( $config );

}
add_action( 'wp_footer', 'wp_review_show_popup' );


/**
 * Gets popup query object.
 *
 * @param  array $config Popup config.
 * @return WP_Query
 */
function wp_review_popup_get_query( $config ) {
	$query_args = array(
		'posts_per_page' => intval( $config['limit'] ),
		'post_type'      => empty( $config['post_type'] ) ? 'any' : $config['post_type'],
		'ignore_sticky_posts' => true,
	);

	$meta_query = array();

	if ( ! wp_review_option( 'global_user_rating' ) ) {
		$meta_query[] = array(
			'key'     => 'wp_review_type',
			'value'   => 'none',
			'compare' => '!=',
		);
	}

	if ( 'none' === wp_review_option( 'review_type' ) || ! wp_review_option( 'review_type' ) ) {
		$meta_query[] = array(
			'key'     => 'wp_review_type',
			// 'value'   => 1,
			'compare' => 'EXISTS',
		);
	}

	$meta_query[] = array(
		'relation' => 'OR',
		array(
			'key'     => 'wp_review_total', // Has author review.
			'value'   => 0,
			'compare' => '>',
		),
		array(
			'key'     => 'wp_review_review_count', // Has visitor review.
			'value'   => 0,
			'compare' => '>',
		),
		array(
			'key'     => 'wp_review_comments_rating_count', // Has comment review.
			'value'   => 0,
			'compare' => '>',
		),
	);

	switch ( $config['queryby'] ) {
		case 'category':
			$query_args['cat'] = intval( $config['category'] );
			break;

		case 'tag':
			$query_args['tag_id'] = intval( $config['tag'] );
			break;

		case 'review_type':
			$meta_query[] = array(
				'key'   => 'wp_review_type',
				'value' => $config['review_type'],
			);
			break;

		case 'same_category':
			if ( is_singular() ) {
				$post_id = get_queried_object_id();
				$categories = get_the_category( $post_id );
				$categories = wp_list_pluck( $categories, 'term_id' );
				$query_args['category__in'] = $categories;
				$query_args['post__not_in'] = array( $post_id );
			}
			break;

		case 'same_tag':
			if ( is_singular() ) {
				$post_id = get_queried_object_id();
				$tags = get_the_tags( $post_id );
				if ( $tags ) {
					$tags = wp_list_pluck( $tags, 'term_id' );
					$query_args['tag__in '] = $tags;
					$query_args['post__not_in'] = array( $post_id );
				}
			}
			break;

		case 'same_review_type':
			if ( is_singular() ) {
				$post_id = get_queried_object_id();
				$review_type = wp_review_get_post_review_type( $post_id );
				$meta_query[] = array(
					'key'   => 'wp_review_type',
					'value' => $review_type,
				);
				$query_args['post__not_in'] = array( $post_id );
			}
			break;
	}

	switch ( $config['orderby'] ) {
		case 'random':
			$query_args['orderby'] = 'rand';
			break;

		case 'rated':
			$query_args['meta_key'] = 'wp_review_total';
			$query_args['orderby'] = 'meta_value_num';
			$query_args['order'] = 'desc';
			break;

		case 'popular':
			$query_args['meta_key'] = 'wp_review_review_count';
			$query_args['orderby'] = 'meta_value_num';
			$query_args['order'] = 'desc';
			break;
	}

	if ( $meta_query ) {
		$query_args['meta_query'] = $meta_query;
	}

	if ( is_singular() ) {
		$query_args['post__not_in'] = array( get_queried_object_id() );
	}

	/**
	 * Filters popup query args.
	 *
	 * @since 3.0.0
	 *
	 * @param array $query_args Popup query args.
	 * @param array $config     Popup config.
	 */
	$query_args = apply_filters( 'wp_review_popup_query_args', $query_args, $config );

	return new WP_Query( $query_args );
}


/**
 * Popup template.
 *
 * @param array $config Popup config data.
 * @param bool  $echo   Show popup.
 * @return string
 */
function wp_review_popup( $config, $echo = true ) {
	if ( wp_review_is_embed() ) {
		return;
	}

	$classes = array( 'wpr-popup', 'mfp-hide' );

	$classes = implode( ' ', $classes );

	$query = wp_review_popup_get_query( $config );
	if ( ! $query->have_posts() ) {
		return;
	}

	ob_start();
	wp_review_load_template( 'popup/popup.php', compact( 'config', 'classes', 'query' ) );
	wp_review_load_template( 'popup/style.php', compact( 'config' ) );
	$output = ob_get_clean();

	/**
	 * Filters popup output.
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Popup output.
	 * @param array  $config Popup config.
	 */
	$output = apply_filters( 'wp_review_popup', $output, $config );

	if ( ! $echo ) {
		return $output;
	}

	echo $output;
}


/**
 * Adds the catch element to the bottom of content.
 *
 * @param string $content Post content.
 */
function wp_review_add_catch_element( $content ) {
	$config = wp_review_get_popup_config();

	if ( ! $config['enable'] || ! $config['show_on_reach_bottom'] ) {
		return $content;
	}

	if ( ( is_single() || is_page() ) && is_main_query() && in_the_loop() && ! ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ) {
		$content .= '<div id="wp-review-content-bottom"></div>';
	}

	return $content;
}
add_action( 'the_content', 'wp_review_add_catch_element' );
