<?php
/**
 * Facebook functions
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Facebook API class.
 */
require_once( WP_REVIEW_INCLUDES . 'class-wp-review-facebook-api.php' );

/**
 * Gets facebook user avatar url from user ID.
 *
 * @link https://developers.facebook.com/docs/graph-api/reference/user/picture/
 *
 * @param string $user_id User ID.
 * @param array  $args    Custom attributes.
 */
function wp_review_fb_user_avatar_url( $user_id, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'width' => 150,
	) );
	return add_query_arg( $args, "https://graph.facebook.com/{$user_id}/picture" );
}

/**
 * Shows user avatar from user ID.
 *
 * @link https://developers.facebook.com/docs/graph-api/reference/user/picture/
 *
 * @param string $user_id User ID.
 * @param array  $args    Custom attributes.
 */
function wp_review_fb_user_avatar( $user_id, $args = array() ) {
	$url = wp_review_fb_user_avatar_url( $user_id, $args );
	?>
	<img src="<?php echo esc_url( $url ); ?>" alt="" class="reviewer-avatar">
	<?php
}


/**
 * Gets user profile URL from user ID.
 *
 * @param  string $user_id User ID.
 * @return string
 */
function wp_review_fb_get_profile_url( $user_id ) {
	return sprintf( 'https://www.facebook.com/app_scoped_user_id/%s', $user_id );
}
