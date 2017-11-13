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
wp_enqueue_script( 'wp-review-point-input', trailingslashit( WP_REVIEW_URI ) . 'rating-types/point-input.js', array( 'jquery' ) );

$class = 'review-point';
if (!empty($rating['args']['class']))
	$class .= ' '.sanitize_html_class( $rating['args']['class'] );

?>
<div class="<?php echo $class; ?>">
	<div class="wp-review-loading-msg"><span class="animate-spin mts-icon-loader"></span><?php _e( 'Sending', 'wp-review' ); ?></div>
	<div class="review-result-wrapper" data-originalrating="<?php echo esc_attr( $rating['value'] ); ?>">
		<div class="review-result" style="width:<?php echo esc_attr( $rating['value'] * 10 ); ?>%; background-color: <?php echo esc_attr( $rating['color'] ); ?>;"></div>
	</div>
	<div class="wp-review-your-rating" style="background-color: <?php echo esc_attr( $rating['colors']['bgcolor1'] ); ?>; color: <?php echo esc_attr( $rating['color'] ); ?>;"><?php printf(__('Your rating: %s', 'wp-review'), '<span class="wp-review-your-rating-value"></span>'); ?></div>

	<input type="hidden" class="wp-review-user-rating-val" name="wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" />
	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div>