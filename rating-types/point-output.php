<?php
/**
 * Star rating type output template
 *
 * @package   WP_Review
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rating_type  = wp_review_get_rating_type_data( 'point' );
$rating_value = isset( $rating['value'] ) ? $rating['value'] : 0;
$value        = wp_review_normalize_rating_value( $rating_value, 'point' );

$class = 'review-point';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$inactive_color = ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '';
$bar_text_color = ! empty( $rating['colors']['bar_text_color'] ) ? $rating['colors']['bar_text_color'] : $rating['colors']['bgcolor1'];
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="review-result-wrapper"<?php if ( $inactive_color ) echo " style=\"background-color: {$inactive_color};\""; // phpcs:ignore ?>>
		<div class="review-result" style="width:<?php echo esc_attr( $value * 10 ); ?>%; background-color: <?php echo esc_attr( $rating['color'] ); ?>;"></div>
		<div class="review-result-text" style="color: <?php echo esc_attr( $bar_text_color ); ?>;"><?php echo sprintf( $rating_type['value_text'], $value ); // WPCS: xss ok. ?></div>
	</div>
</div><!-- .review-percentage -->
