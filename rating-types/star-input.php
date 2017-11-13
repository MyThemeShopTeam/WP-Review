<?php
/**
 * Star rating type output template
 * 
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// For now, enqueue in footer
wp_enqueue_script( 'wp-review-star-input', trailingslashit( WP_REVIEW_URI ) . 'rating-types/star-input.js', array( 'jquery' ) );

$class = '';
if (!empty($rating['args']['class']))
	$class .= sanitize_html_class( $rating['args']['class'] );

?>
<div class="review-total-star <?php echo $class; ?>" data-post-id="<?php echo esc_attr( $rating['post_id'] ); ?>" data-token="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>">
	<div class="wp-review-loading-msg"><span class="animate-spin mts-icon-loader"></span><?php _e( 'Sending', 'wp-review' ); ?></div>
	<div class="review-result-wrapper">
		<span data-input-value="1" title="1/5"><i class="mts-icon-star"></i></span>
		<span data-input-value="2" title="2/5"><i class="mts-icon-star"></i></span>
		<span data-input-value="3" title="3/5"><i class="mts-icon-star"></i></span>
		<span data-input-value="4" title="4/5"><i class="mts-icon-star"></i></span>
		<span data-input-value="5" title="5/5"><i class="mts-icon-star"></i></span>
		<div class="review-result" style="width:<?php echo esc_attr( $rating['value'] * 20 ); ?>%;">
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
		</div>
	</div>
	<input type="hidden" class="wp-review-user-rating-val" name="wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" />
	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div>

<?php 
$color_output = <<<EOD

<style type="text/css">
	.wp-review-{$rating['post_id']} .review-result-wrapper .review-result i { color: {$rating['color']}; opacity: 1; filter: alpha(opacity=100); }
	.wp-review-{$rating['post_id']} .review-result-wrapper i { color: {$rating['color']}; opacity: 0.50; filter: alpha(opacity=50); }
	.wp-review-{$rating['post_id']} .mts-user-review-star-container .selected i, .wp-review-{$rating['post_id']} .user-review-area .review-result i { color: {$rating['color']}; opacity: 1; filter: alpha(opacity=100); }
</style>

EOD;

echo $color_output;