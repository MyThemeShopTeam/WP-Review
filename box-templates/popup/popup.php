<?php
/**
 * Popup template
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array    $config
 * @var string   $classes
 * @var WP_Query $query
 */

$features = wp_review_get_review_items();
?>
<div id="wp-review-popup" class="<?php echo esc_attr( $classes ); ?>">
	<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="wpr-popup__item">
			<div class="wpr-popup__item-image">
				<a href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'wp_review_large' ); ?>
						<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( WP_REVIEW_ASSETS . 'images/largethumb.png' ); ?>" alt="<?php the_title(); ?>" class="wp-post-image">
						<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					<?php endif; ?>
				</a>
			</div>

			<?php the_title( '<div class="wpr-popup__item-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></div>' ); ?>
		</div>
	<?php endwhile; wp_reset_postdata(); ?>
</div>
