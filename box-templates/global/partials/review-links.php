<?php
/**
 * Template for review links
 *
 * @since 3.0.0
 *
 * @package WP_Review
 * @var array $review
 */

if ( empty( $review['links'] ) || ! is_array( $review['links'] ) ) {
	return;
}
?>
<ul class="review-links">
	<?php foreach ( $review['links'] as $link ) :
		$link = wp_parse_args( $link, array(
			'url'  => '',
			'text' => '',
		));
		?>
		<li>
			<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank"><?php echo wp_kses_post( $link['text'] ); ?></a>
		</li>
	<?php endforeach; ?>
</ul>
