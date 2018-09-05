<?php
/**
 * Circle rating type input template
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

wp_enqueue_script( 'jquery-knob' );
wp_enqueue_script( 'wp-review-circle-input' );

$circle = wp_review_get_rating_type_data( 'circle' );

$class = 'wp-review-rating-input review-circle';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$knob_attrs = array(
	'width'           => '50',
	'height'          => '50',
	'displayInput'    => 'true',
	'displayPrevious' => 'true',
	'fgColor'         => $rating['color'],
	'bgColor'         => ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '',
	'step'            => 1 / pow( 10, $circle['decimals'] ),
);

$knob_attrs_str = '';
foreach ( $knob_attrs as $attr_name => $attr_value ) {
	$knob_attrs_str .= 'data-' . $attr_name . '="' . $attr_value . '" ';
}
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="review-result-wrapper">
		<input type="text" class="wp-review-circle-rating-user wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" <?php echo $knob_attrs_str; ?> name="wp-review-user-rating-val" />
	</div>

	<?php if ( empty( $rating['args']['hide_button'] ) ) : ?>
		<button type="button" class="wpr-rating-accept-btn" style="display: none;"><?php esc_html_e( 'Submit Rating', 'wp-review' ); ?></button>
	<?php endif; ?>

	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div><!-- .review-circle -->

<style type="text/css">
.wp-review-<?php echo $rating['post_id']; ?> .wp-review-circle-rating-send {
	color: <?php echo $rating['color']; ?>;
}
.wp-review-<?php echo $rating['post_id']; ?> .wp-review-circle-rating-send:hover {
	color: <?php echo $rating['colors']['fontcolor']; ?>;
}
</style>
