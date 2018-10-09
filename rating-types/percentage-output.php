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

$percentage   = wp_review_get_rating_type_data( 'percentage' );
$rating_value = isset( $rating['value'] ) ? $rating['value'] : 0;
$value        = wp_review_normalize_rating_value( $rating_value, 'percentage' );

$class = 'review-percentage';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$inactive_color = ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '';
$bar_text_color = ! empty( $rating['colors']['bar_text_color'] ) ? $rating['colors']['bar_text_color'] : $rating['colors']['bgcolor1'];
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="review-result-wrapper"<?php if ( $inactive_color ) echo " style=\"background-color: {$inactive_color};\""; // phpcs:ignore ?>>
		<div class="review-result" style="width:<?php echo esc_attr( $value ); ?>%; background-color: <?php echo esc_attr( $rating['color'] ); ?>;"></div>
		<div class="review-result-text" style="color: <?php echo esc_attr( $bar_text_color ); ?>;"><?php echo sprintf( wp_kses_post( $percentage['value_text'] ), floatval( $value ) ); ?></div>
	</div>
</div><!-- .review-percentage -->
