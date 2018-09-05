<?php
/**
 * Thumbs rating type input template
 *
 * @package   WP_Review
 * @since     3.0.0
 * @version   3.0.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// For now, enqueue in footer.
wp_enqueue_script( 'wp-review-thumbs-input', trailingslashit( WP_REVIEW_URI ) . 'rating-types/thumbs-input.js', array( 'jquery' ) );

$class = 'wp-review-rating-input rating-wrapper review-thumbs rating-thumbs-input';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$up_color = ! empty( $rating['color'] ) ? $rating['color'] : '';
$down_color = ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '';
$positive = ! empty( $rating['args']['positive_count'] ) ? intval( $rating['args']['positive_count'] ) : 0;
$negative = ! empty( $rating['args']['negative_count'] ) ? intval( $rating['args']['negative_count'] ) : 0;
$show_number = false; // Can extend this value in the future.
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="wp-review-loading-msg">
		<?php wp_review_spinner(); ?>
		<?php esc_html_e( 'Sending', 'wp-review' ); ?>
	</div>

	<div class="review-result-wrapper">
		<div class="wpr-thumbs-button wpr-thumbs-down-button">
			<?php if ( $show_number ) : ?>
				<span class="wpr-thumbs-rating-value"><?php echo intval( $negative ); ?></span>
			<?php endif; ?>
			<span class="wpr-thumbs-icon wpr-thumbs-down-icon"><i class="fa fa-thumbs-down" style="color: <?php echo esc_attr( $up_color ); ?>; opacity: 0.4"></i></span>
		</div>

		<div class="wpr-thumbs-button wpr-thumbs-up-button">
			<?php if ( $show_number ) : ?>
				<span class="wpr-thumbs-rating-value"><?php echo intval( $positive ); ?></span>
			<?php endif; ?>
			<span class="wpr-thumbs-icon wpr-thumbs-up-icon"><i class="fa fa-thumbs-up" style="color: <?php echo esc_attr( $up_color ); ?>; opacity: 0.4"></i></span>
		</div>
	</div>

	<input type="hidden" class="wp-review-user-rating-val" name="wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" />
	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div>
