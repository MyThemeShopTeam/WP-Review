<?php
/**
 * Template for shortcode [wp-review-yelp-business]
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array $response
 * @var array $atts
 */

$business = $response;
wp_review_yelp_schema( $business );
?>
<div class="wpr-businesses">
	<div class="business business-<?php echo esc_attr( $business['id'] ); ?>">
		<div class="business-image">
			<a href="<?php echo esc_url( $business['url'] ); ?>">
				<img src="<?php echo esc_url( wp_review_yelp_get_image_size_url( $business['image_url'], 'ms' ) ); ?>" alt="">
			</a>
		</div>

		<div class="business-data">
			<div class="business-name">
				<a href="<?php echo esc_url( $business['url'] ); ?>" target="_blank"><?php echo esc_html( $business['name'] ); ?></a>
			</div>

			<div class="business-rating">
				<?php wp_review_yelp_rating_image( $business['rating'] ); ?>
				<span class="review-count">
					<?php
					/* translators: review count. */
					printf( esc_html__( '%s reviews', 'wp-review' ), intval( $business['review_count'] ) );
					?>
				</span>
			</div>
		</div>
	</div>

	<?php wp_review_yelp_logo(); ?>
</div>
