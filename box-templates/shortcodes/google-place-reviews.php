<?php
/**
 * Template for shortcode [wp-review-google-place-reviews]
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 * @var array $response
 * @var array $atts
 */

if ( empty( $response['result'] ) ) {
	return;
}
$place = $response['result'];
if ( empty( $place['reviews'] ) || ! is_array( $place['reviews'] ) ) {
	echo '<p>' . esc_html__( 'There is no review for this place.', 'wp-review' ) . '</p>';
	return;
}
$reviews = $place['reviews'];
if ( intval( $atts['review_num'] ) ) {
	$reviews = array_slice( $reviews, 0, intval( $atts['review_num'] ) );
}
?>
<div class="wpr-place-reviews">
	<h4 class="place-name"><?php echo esc_html( $place['name'] ); ?></h4>

	<?php foreach ( $reviews as $review ) : ?>
		<div class="place-review">
			<div class="review-image">
				<a href="<?php echo esc_url( $review['author_url'] ); ?>">
					<img src="<?php echo esc_url( $review['profile_photo_url'] ); ?>" alt="" class="reviewer-avatar">
				</a>
			</div>

			<div class="review-data">
				<div class="reviewer-name">
					<a href="<?php echo esc_url( $review['author_url'] ); ?>"><?php echo esc_html( $review['author_name'] ); ?></a>
				</div>

				<div class="review-rating">
					<?php wp_review_star_rating( $review['rating'] ); ?>
				</div>
			</div>

			<div class="review-text">
				<?php echo wp_kses_post( nl2br( $review['text'] ) ); ?>
			</div>
		</div>

		<?php wp_review_google_place_review_schema( $review, $place ); ?>
	<?php endforeach; ?>
</div>
