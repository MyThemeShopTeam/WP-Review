<?php
/**
 * Star rating type output template
 *
 * @package   WP_Review
 * @since     2.0
 * @version   3.0.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( wp_review_is_amp_page() ) {
	echo '<div class="wp-review-rating-input"><a href="' . esc_url( wp_review_get_current_non_amp_url() ) . '#review">' . esc_html__( 'Add rating', 'wp-review' ) . '</a></div>';
	return;
}

global $wp_review_rating_types;

// For now, enqueue in footer.
wp_enqueue_script( 'wp-review-percentage-input', trailingslashit( WP_REVIEW_URI ) . 'rating-types/percentage-input.js', array( 'jquery' ) );

$class = 'wp-review-rating-input review-percentage';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$bg_color = '';

if ( ! empty( $rating['colors']['inactive_color'] ) ) {
	$inactive_color = $rating['colors']['inactive_color'];
	$bg_color       = "background-color: {$inactive_color};";
}
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="wp-review-loading-msg">
		<?php wp_review_spinner(); ?>
		<?php esc_html_e( 'Sending', 'wp-review' ); ?>
	</div>

	<div class="review-result-wrapper" data-originalrating="<?php echo esc_attr( $rating['value'] ); ?>" style="<?php echo esc_attr( $bg_color ); ?>">
		<div class="review-result" style="width:<?php echo esc_attr( $rating['value'] ); ?>%; background-color: <?php echo esc_attr( $rating['color'] ); ?>; display: block; transition: none;"></div>
	</div>

	<div class="wp-review-your-rating" style="background-color: <?php echo esc_attr( $rating['colors']['color'] ); ?>; color: <?php echo esc_attr( $rating['colors']['inactive_color'] ); ?>;">
		<?php
		// Translators: rating value.
		printf( esc_html__( '%s', 'wp-review' ), '<span class="wp-review-your-rating-value"></span>' ); // phpcs:ignore
		?>
	</div>

	<input type="hidden" class="wp-review-user-rating-val" name="wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" />
	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div>
