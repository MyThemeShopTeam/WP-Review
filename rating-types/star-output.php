<?php
/**
 * Star rating type output template
 *
 * @since     2.1
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @package   WP_Review
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class = 'review-star';
if ( ! empty( $rating['args']['class'] ) ) {
	$class .= ' ' . sanitize_html_class( $rating['args']['class'] );
}

$rating_icon    = wp_review_get_rating_icon();
$rating_image   = wp_review_get_rating_image();
$inactive_color = ! empty( $rating['colors']['inactive_color'] ) ? $rating['colors']['inactive_color'] : '';
$rating_value   = isset( $rating['value'] ) ? floatval( $rating['value'] ) : 0;
$rating_color   = isset( $rating['color'] ) ? $rating['color'] : '';
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<div class="review-result-wrapper"<?php if ( $inactive_color ) echo " style=\"color: {$inactive_color};\""; // phpcs:ignore ?>>
		<?php
		for ( $i = 1; $i <= 5; $i++ ) :
			if ( $rating_image ) {
				?>
				<img src="<?php echo esc_url( $rating_image ); ?>" class="wp-review-image" />
				<?php
			} else {
				?>
				<i class="<?php echo esc_attr( $rating_icon ); ?>"></i>
				<?php
			}
		endfor;
		?>

		<div class="review-result" style="width:<?php echo floatval( ( $rating_value * 20 ) ); ?>%; color:<?php echo esc_attr( $rating_color ); ?>;">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) :
				if ( $rating_image ) {
					?>
					<img src="<?php echo esc_url( $rating_image ); ?>" class="wp-review-image" />
					<?php
				} else {
					?>
					<i class="<?php echo esc_attr( $rating_icon ); ?>"></i>
					<?php
				}
			endfor;
			?>
		</div><!-- .review-result -->

	</div><!-- .review-result-wrapper -->

</div><!-- .review-star -->
