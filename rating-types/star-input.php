<?php
/**
 * Star rating type output template
 *
 * @package   WP_Review
 * @since     2.1
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

// For now, enqueue in footer.
wp_enqueue_script( 'wp-review-star-input', trailingslashit( WP_REVIEW_URI ) . 'rating-types/star-input.js', array( 'jquery' ) );

// $feature_selector = empty( $rating['feature_id'] ) ? '' : "[data-feature-id=\"{$rating['feature_id']}\"]";
$class = 'wp-review-rating-input review-star';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= sanitize_html_class( $rating['args']['class'] );
}

$rating_icon      = wp_review_get_rating_icon();
$rating_image     = wp_review_get_rating_image();
$id               = 'wp-review-star-rating-' . mt_rand( 1000, 9999 );
$wrapper_selector = '#' . $id;
?>
<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" data-post-id="<?php echo esc_attr( $rating['post_id'] ); ?>" data-token="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>">
	<div class="wp-review-loading-msg">
		<?php wp_review_spinner(); ?>
		<?php esc_html_e( 'Sending', 'wp-review' ); ?>
	</div>

	<div class="review-result-wrapper">
		<?php
		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $rating_image ) {
				printf(
					'<span class="wpr-has-image" data-input-value="%1$s" title="%1$s/5"><img src="%2$s" class="wp-review-image" /></span>',
					esc_attr( $i ),
					$rating_image
				); // WPCS: xss ok.
			} else {
				printf(
					'<span data-input-value="%1$s" title="%1$s/5"><i class="%2$s"></i></span>',
					esc_attr( $i ),
					esc_attr( $rating_icon )
				);
			}
		}
		?>
		<div class="review-result" style="width:<?php echo esc_attr( $rating['value'] * 20 ); ?>%;">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) :
				if ( $rating_image ) {
					echo '<img src="' . esc_url( $rating_image ) . '" class="wp-review-image" />';
				} else {
					echo '<i class="' . esc_attr( $rating_icon ) . '"></i>';
				}
			endfor;
			?>
		</div>
	</div>
	<input type="hidden" class="wp-review-user-rating-val" name="wp-review-user-rating-val" value="<?php echo esc_attr( $rating['value'] ); ?>" />
	<input type="hidden" class="wp-review-user-rating-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-review-security' ) ); ?>" />
	<input type="hidden" class="wp-review-user-rating-postid" value="<?php echo esc_attr( $rating['post_id'] ); ?>" />
</div>

<?php
$bg_color = 'currentColor';
if ( ! empty( $rating['colors']['inactive_color'] ) ) {
	$bg_color = $rating['colors']['inactive_color'];
}
// phpcs:disable
?>
<style type="text/css">
	.wp-review-comment-rating <?php echo $wrapper_selector; ?> .review-result-wrapper i {
		color: <?php echo $bg_color; ?>;
	}
	.wp-review-<?php echo $rating['post_id']; ?> <?php echo $wrapper_selector; ?> .review-result-wrapper .review-result i {
		color: <?php echo $rating['color']; ?>;
		opacity: 1;
		filter: alpha(opacity=100);
	}
	.wp-review-<?php echo $rating['post_id']; ?> <?php echo $wrapper_selector; ?> .review-result-wrapper i {
		color: <?php echo $bg_color; ?>;
	}
	.wp-review-<?php echo $rating['post_id']; ?> .mts-user-review-star-container <?php echo $wrapper_selector; ?> .selected i,
	.wp-review-<?php echo $rating['post_id']; ?> .user-review-area <?php echo $wrapper_selector; ?> .review-result i,
	.wp-review-comment-field <?php echo $wrapper_selector; ?> .review-result i,
	.wp-review-comment-rating <?php echo $wrapper_selector; ?> .review-result i,
	.wp-review-user-rating <?php echo $wrapper_selector; ?> .review-result-wrapper:hover span i,
	.wp-review-comment-rating <?php echo $wrapper_selector; ?> .review-result-wrapper:hover span i {
		color: <?php echo $rating['color']; ?>;
		opacity: 1;
		filter: alpha(opacity=100);
	}
	.wp-review-user-rating <?php echo $wrapper_selector; ?> .review-result-wrapper span:hover ~ span i,
	.wp-review-comment-rating <?php echo $wrapper_selector; ?> .review-result-wrapper span:hover ~ span i {
		opacity: 1;
		color: <?php echo $bg_color; ?>;
	}
</style>
