<?php
/**
 * Template for comparison table shortcode
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var WP_Query $query
 * @var array    $atts
 */

?>
<div class="comparison-table-wrapper">
	<table class="comparison-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Product', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Features', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Overall', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Price', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Buy Now', 'wp-review' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php while ( $query->have_posts() ) : $query->the_post();
				if ( ! wp_review_is_enable() ) {
					continue;
				}
				$post_id = get_the_ID();
				?>
				<tr>
					<td class="product-col">
						<a href="<?php the_permalink(); ?>" class="product-title">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							<?php endif; ?>

							<p><?php the_title(); ?></p>
						</a>
					</td>

					<td class="features-col">
						<?php wp_review_review_items( $post_id ); ?>
					</td>

					<td class="review-total-col">
						<?php echo do_shortcode( '[wp-review-total id="' . $post_id . '"]' ); ?>
					</td>

					<td class="price-col">
						<?php wp_review_product_price(); ?>
					</td>

					<td class="button-col">
						<?php
						$review_links = wp_review_get_review_links();
						if ( $review_links ) {
							foreach ( $review_links as $link ) {
								$link = wp_parse_args( $link, array(
									'url'  => '#',
									'text' => '',
								) );

								if ( ! $link['text'] ) {
									continue;
								}

								printf(
									'<a href="%1$s" target="_blank" class="review-link">%2$s</a>',
									esc_url( $link['url'] ),
									wp_kses_post( $link['text'] )
								);
							}
						}
						?>
					</td>
				</tr>
			<?php endwhile; wp_reset_postdata(); ?>
		</tbody>
	</table>
</div>
