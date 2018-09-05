<?php
/**
 * Thumbs rating type output template
 *
 * @since     3.0.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @package   WP_Review
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $rating['args']['review_total'] ) ) {
	return;
} // Don't show thubms rating on total.

$class = 'rating-wrapper review-thumbs rating-thumbs-output';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$up_color = ! empty( $rating['color'] ) ? $rating['color'] : '';
$down_color = ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '';
$positive = ! empty( $rating['args']['positive_count'] ) ? intval( $rating['args']['positive_count'] ) : 0;
$negative = ! empty( $rating['args']['negative_count'] ) ? intval( $rating['args']['negative_count'] ) : 0;
$show_one = ! empty( $rating['args']['show_one'] );
$value = floatval( $rating['value'] );
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="review-result-wrapper">
		<?php if ( ! $show_one || 0 == $value ) : ?>
			<div class="wpr-thumbs-button wpr-thumbs-down-button">
				<?php if ( ! $show_one ) : ?>
					<span class="wpr-thumbs-rating-value"><?php echo intval( $negative ); ?></span>
				<?php endif; ?>
				<span class="wpr-thumbs-icon wpr-thumbs-down-icon"><i class="fa fa-thumbs-down" style="color: <?php echo esc_attr( $down_color ); ?>;"></i></span>
			</div>
		<?php endif; ?>

		<?php if ( ! $show_one || 100 == $value ) : ?>
			<div class="wpr-thumbs-button wpr-thumbs-up-button">
				<?php if ( ! $show_one ) : ?>
					<span class="wpr-thumbs-rating-value"><?php echo intval( $positive ); ?></span>
				<?php endif; ?>
				<span class="wpr-thumbs-icon wpr-thumbs-up-icon"><i class="fa fa-thumbs-up" style="color: <?php echo esc_attr( $up_color ); ?>;"></i></span>
			</div>
		<?php endif; ?>
	</div><!-- .review-result-wrapper -->
</div><!-- .rating-thumbs -->
