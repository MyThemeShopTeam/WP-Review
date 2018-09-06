<?php
/**
 * Notification bar
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Gets notification bar option.
 *
 * @param  string $name    Option name.
 * @param  mixed  $default Default value.
 * @return mixed
 */
function wp_review_hello_bar_option( $name = '', $default = null ) {
	static $options = null;
	if ( ! is_array( $options ) ) {
		$options = get_option( 'wp_review_hello_bar', array() );
	}

	if ( ! $name ) {
		return $options;
	}

	$value = isset( $options[ $name ] ) ? $options[ $name ] : $default;
	$value = apply_filters( 'wp_review_hello_bar_option_' . $name, $value );
	$value = apply_filters( 'wp_review_hello_bar_option', $value, $name );
	return $value;
}


/**
 * Gets notification bar default values.
 *
 * @return array
 */
function wp_review_hello_bar_defaults() {
	return apply_filters( 'wp_review_hello_bar_defaults', array(
		'enable'            => false,
		'text'              => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc a odio nec justo rutrum faucibus.',
		'star_rating'       => 5,
		'price'             => '$20',
		'button_label'      => 'Buy Now',
		'button_url'        => '#',
		'target_blank'      => false,
		'location'          => 'top',
		'floating'          => false,
		'max_container'     => '1010px',
		'bg_color'          => '#FF5722',
		'bg_image'          => array(),
		'text_color'        => '#fff',
		'star_color'        => '#fff00a',
		'button_bg_color'   => '#48342c',
		'button_text_color' => '#fff',
	) );
}


/**
 * Gets post notification bar config.
 *
 * @param  int  $post_id       Post ID.
 * @param  bool $merge_default Merge with default value or not.
 * @return array
 */
function wp_review_get_post_hello_bar( $post_id, $merge_default = true ) {
	$config = (array) get_post_meta( $post_id, 'wp_review_hello_bar', true );
	if ( $merge_default ) {
		$config = wp_parse_args( $config, wp_review_hello_bar_defaults() );
	}
	return apply_filters( 'wp_review_post_hello_bar_config', $config, $post_id );
}


/**
 * Gets hello bar config.
 *
 * @return array
 */
function wp_review_get_hello_bar_config() {

	$config = wp_review_hello_bar_option();
	$config = wp_parse_args( $config, wp_review_hello_bar_defaults() );

	if(is_multisite()) {
		if(wp_review_switch_to_main('hide_general_bar_')) {
			$config['enable'] = false;
		}
		restore_current_blog();
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$post_config = wp_review_get_post_hello_bar( $post_id, false );
		if ( empty( $post_config['enable'] ) ) {
			$post_config['enable'] = 'default';
		}
		if ( 'none' === $post_config['enable'] ) {
			return;
		}

		if ( 'custom' === $post_config['enable'] ) {
			$config = array_merge( $config, $post_config );
			$config['enable'] = true;
		}
	}
	return apply_filters( 'wp_review_get_hello_bar_config', $config );
}


/**
 * Shows notification bar.
 */
function wp_review_show_hello_bar() {

	$config = wp_review_get_hello_bar_config();
	if ( ! $config['enable'] ) {
		return;
	}

	wp_review_hello_bar( $config );
}
add_action( 'wp_footer', 'wp_review_show_hello_bar' );


/**
 * Notification bar template.
 *
 * @param array $config Notification bar config data.
 * @param bool  $echo   Show hello bar.
 * @return string
 */
function wp_review_hello_bar( $config, $echo = true ) {

	$classes = array( 'hello-bar' );
	$classes[] = 'hello-bar--' . $config['location'];

	if ( $config['floating'] || 'bottom' === $config['location'] ) {
		$classes[] = 'hello-bar--floating';
	}

	$classes = implode( ' ', $classes );

	ob_start();
	wp_review_load_template( 'hello-bar/hello-bar.php', compact( 'config', 'classes' ) );
	wp_review_load_template( 'hello-bar/style.php', compact( 'config' ) );
	$output = ob_get_clean();

	/**
	 * Filters notification bar output.
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Notification bar output.
	 * @param array  $config Notification bar config.
	 */
	$output = apply_filters( 'wp_review_hello_bar', $output, $config );

	if ( ! $echo ) {
		return $output;
	}

	echo $output;
}
