<?php
/**
 * Circle rating type output template
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_script( 'jquery-knob' );
wp_enqueue_script( 'wp-review-circle-output' );


$circle = wp_review_get_rating_type_data( 'circle' );

$class = 'review-circle';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' '. sanitize_html_class( $rating['args']['class'] );
}

// Default small knob.
$knob_attrs = array(
	'width' => '32',
	'height' => '32',
	'displayInput' => 'false',
	'fgColor' => $rating['color'],
	'bgColor' => ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '',
);

// Total rating is large
if ( isset($rating['args']['class']) && $rating['args']['class'] == 'review-total' ) {
	$knob_attrs['width'] = '100';
	$knob_attrs['height'] = '100';
	$knob_attrs['displayInput'] = 'true';
}

// Comment rating field & admin column rating is slightly larger too
if ( ! empty( $rating['comment_rating'] ) || ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) ) {
	$knob_attrs['width'] = '32';
	$knob_attrs['height'] = '32';
	$knob_attrs['displayInput'] = 'true';
}

if ( ! empty( $rating['circle_width'] ) ) {
	$knob_attrs['width'] = $rating['circle_width'];
}
if ( ! empty( $rating['circle_height'] ) ) {
	$knob_attrs['height'] = $rating['circle_height'];
}
if ( isset( $rating['circle_display_input'] ) ) {
	$knob_attrs['displayInput'] = $rating['circle_display_input'];
}

if ( ! empty( $rating['args']['circle_width'] ) ) {
	$knob_attrs['width'] = $rating['args']['circle_width'];
}
if ( ! empty( $rating['args']['circle_height'] ) ) {
	$knob_attrs['height'] = $rating['args']['circle_height'];
}
if ( ! empty( $rating['args']['circle_size'] ) ) {
	$knob_attrs['width'] = $rating['args']['circle_size'];
	$knob_attrs['height'] = $rating['args']['circle_size'];
}
if ( isset( $rating['args']['circle_display_input'] ) ) {
	$knob_attrs['displayInput'] = $rating['args']['circle_display_input'];
}
$knob_attrs['step'] = 1 / pow( 10, $circle['decimals'] );

$knob_attrs_str = '';
foreach ($knob_attrs as $attr_name => $attr_value) {
	$knob_attrs_str .= 'data-' . $attr_name . '="' . $attr_value . '" ';
}
$rating_value = isset( $rating['value'] ) ? floatval( $rating['value'] ) : 0;
?>
<div class="<?php echo $class; ?>">
	<div class="review-result-wrapper">
		<input type="text" class="wp-review-circle-rating" value="<?php echo esc_attr( $rating_value ); ?>" readonly="readonly" <?php echo $knob_attrs_str; ?>/>
	</div>
</div><!-- .review-circle -->
