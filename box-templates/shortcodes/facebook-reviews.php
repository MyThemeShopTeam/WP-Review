<?php
/**
 * Template for shortcode [wp-review-facebook-reviews]
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var object $response
 * @var object $page
 * @var array  $atts
 */

$reviews = $response->data;
if ( ! $response->data ) {
	esc_html_e( 'This page doesn\' have any reviews', 'wp-review' );
	return;
}
wp_review_facebook_page_schema( $page );
?>
<div class="wpr-place-reviews">
	<?php foreach ( $reviews as $review ) : ?>
		<div class="place-review">
			<?php if ( ! empty( $review->reviewer ) ) : ?>
				<div class="review-image">
					<a href="<?php echo esc_url( wp_review_fb_get_profile_url( $review->reviewer->id ) ); ?>">
						<?php wp_review_fb_user_avatar( $review->reviewer->id ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="review-data">
				<?php if ( ! empty( $review->reviewer ) ) : ?>
					<div class="reviewer-name">
						<a href="<?php echo esc_url( wp_review_fb_get_profile_url( $review->reviewer->id ) ); ?>"><?php echo esc_html( $review->reviewer->name ); ?></a>
					</div>
				<?php endif; ?>

				<div class="review-rating">
					<?php wp_review_star_rating( $review->rating ); ?>
				</div>
			</div>

			<div class="review-text">
				<?php echo wp_kses_post( nl2br( $review->review_text ) ); ?>
			</div>
		</div>

		<?php wp_review_facebook_page_review_schema( $review, $page ); ?>
	<?php endforeach; ?>
</div>
