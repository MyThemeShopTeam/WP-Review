<?php
/**
 * Template for shortcode [wp-review-yelp-business-reviews]
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array $response
 * @var array $business
 * @var array $reviews
 * @var array $atts
 */

wp_review_yelp_schema( $business );
?>
<div class="wpr-place-reviews wpr-yelp-business-reviews">
	<?php if ( ! empty( $atts['title'] ) ) : ?>
		<h4 class="place-name"><?php echo esc_html( $atts['title'] ); ?></h4>
	<?php endif; ?>

	<?php foreach ( $reviews as $review ) : ?>
		<div class="place-review">
			<?php if ( ! empty( $review['user']['image_url'] ) ) : ?>
				<div class="review-image">
					<img src="<?php echo esc_url( wp_review_yelp_get_resized_image_url( $review['user']['image_url'], 90 ) ); ?>" alt="" class="reviewer-avatar">
				</div>
			<?php endif; ?>

			<div class="review-data">
				<div class="reviewer-name">
					<?php echo esc_html( $review['user']['name'] ); ?>
				</div>

				<div class="review-rating">
					<?php wp_review_yelp_rating_image( $review['rating'] ); ?>
				</div>
			</div>

			<div class="review-text">
				<?php echo wp_kses_post( nl2br( $review['text'] ) ); ?>
			</div>
		</div>

		<?php wp_review_yelp_review_schema( $review, $business ); ?>
	<?php endforeach; ?>

	<?php wp_review_yelp_logo( array( 'width' => 100 ) ); ?>
</div>
