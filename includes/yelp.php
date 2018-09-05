<?php
/**
 * Yelp functions
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Yelp API class.
 */
require_once( WP_REVIEW_INCLUDES . 'class-wp-review-yelp-api.php' );

/**
 * Shows Yelp logo. Required for copyright.
 *
 * @param array $args Custom arguments.
 */
function wp_review_yelp_logo( array $args = array() ) {
	$attrs = '';
	if ( ! empty( $args['width'] ) ) {
		$attrs .= ' width="' . $args['width'] . '"';
	}
	if ( ! empty( $args['height'] ) ) {
		$attrs .= ' height="' . $args['height'] . '"';
	}
	?>
	<img src="<?php echo esc_url( WP_REVIEW_ASSETS . 'images/yelp-logo.png' ); ?>" class="yelp-logo" alt=""<?php echo $attrs; // WPCS: xss ok. ?>>
	<?php
}


/**
 * Gets resized image url.
 *
 * @param  string $url  Yelp image url.
 * @param  string $size Image size. Accepts `o`, `full`, `60`, `90`, `120`, `168`, `180`, `m`.
 * @return string
 */
function wp_review_yelp_get_resized_image_url( $url, $size = 'o' ) {
	if ( ! in_array( $size, array( 'o', 'full', '60', '90', '120', '168', '180', 'm' ) ) ) {
		return $url;
	}
	$replace = ( 'o' === $size || 'full' === $size ) ? '/o.jpg' : "/{$size}s.jpg";
	return preg_replace( '/\/[0-9a-z]*\.jpg/', $replace, $url );
}


/**
 * Shows Yelp rating stars.
 *
 * @param float $value Rating value.
 */
function wp_review_yelp_rating_image( $value ) {
	switch ( $value ) {
		case 5:
			$image_file = 'small_5.png';
			break;
		case 4.5:
			$image_file = 'small_4_half.png';
			break;
		case 4:
			$image_file = 'small_4.png';
			break;
		case 3.5:
			$image_file = 'small_3_half.png';
			break;
		case 3:
			$image_file = 'small_3.png';
			break;
		case 2.5:
			$image_file = 'small_2_half.png';
			break;
		case 2:
			$image_file = 'small_2.png';
			break;
		case 1.5:
			$image_file = 'small_1_half.png';
			break;
		case 1:
			$image_file = 'small_1.png';
			break;
		default:
			$image_file = 'small_0.png';
	}

	printf(
		/* translators: 1: rating stars url, 2: rating value */
		'<img src="%1$s" alt="%2$s" class="yelp-rating-stars">',
		esc_url( WP_REVIEW_ASSETS . 'images/yelp-stars/' . $image_file ),
		esc_attr( $value )
	);
}


/**
 * Gets image size url.
 *
 * @param  string $original_url Original URL.
 * @param  string $size         Image size. Accepts: 'ms', 'l', 'o', 's'.
 * @return string
 */
function wp_review_yelp_get_image_size_url( $original_url, $size ) {
	return str_replace( 'o.', $size . '.', $original_url );
}
