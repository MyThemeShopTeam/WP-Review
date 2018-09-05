<?php
/**
 * Notification bar template
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array  $config
 * @var string $classes
 */

$rating_icon = wp_review_get_rating_icon();
$rating_image = wp_review_get_rating_image();
$target = $config['target_blank'] ? 'target="_blank"' : '';
$rating_struct = '<i class="'.esc_attr( $rating_icon ).'"></i>';
if($rating_image) {
	$rating_struct = '<img src="'.$rating_image.'" class="wp-review-image" />';
}
?>
<div id="hello-bar" class="<?php echo esc_attr( $classes ); ?>">
	<div class="hello-bar__container">

		<div class="hello-bar__text"><?php echo wp_kses_post( $config['text'] ); ?></div>

		<div class="hello-bar__right">
			<?php if ( ! empty( $config['price'] ) ) : ?>
				<div class="hello-bar__price"><?php echo esc_html( $config['price'] ); ?></div>
			<?php endif; ?>

			<div class="hello-bar__star-rating">
				<div class="review-star">
					<div class="review-result-wrapper">
						<?php for ( $i = 1; $i <= 5; $i++ ) :
										echo $rating_struct;
									endfor;
						?>

						<div class="review-result" style="width: <?php echo floatval( $config['star_rating'] * 20 ); ?>%;">
							<?php for ( $i = 1; $i <= 5; $i++ ) :
											echo $rating_struct;
										endfor;
							?>
						</div><!-- .review-result -->
					</div><!-- .review-result-wrapper -->
				</div><!-- .review-star -->
			</div>

			<?php if ( ! empty( $config['button_label'] ) ) : ?>
				<a href="<?php echo esc_url( $config['button_url'] ); ?>" <?php echo $target; ?> class="hello-bar__button"><?php echo esc_html( $config['button_label'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
