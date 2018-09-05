<?php
/**
 * Template for embed review
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var WP_Post $post
 */

$GLOBALS['post'] = $post;
setup_postdata( $post );

$show_title = wp_review_option( 'embed_show_title', 1 );
$show_thumbnail = wp_review_option( 'embed_show_thumbnail', 1 );
$show_excerpt = wp_review_option( 'embed_show_excerpt', 1 );
$show_rating_box = wp_review_option( 'embed_show_rating_box', 1 );
$show_credit = wp_review_option( 'embed_show_credit', 1 );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'wpr-embed-page' ); ?>>
	<div class="wpr-embed">
		<?php if ( has_post_thumbnail() && $show_thumbnail ) : ?>
			<div class="wpr-embed__thumbnail" style="margin-bottom: 10px;">
				<?php the_post_thumbnail( 'full' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_title ) : ?>
			<h3 style="margin-bottom: 10px;"><?php the_title( '<div class="wp-embed__title">', '</div>' ); ?></h3>
		<?php endif; ?>

		<?php if ( $show_excerpt ) : ?>
			<div class="wpr-embed__excerpt"><?php the_excerpt(); ?></div>
		<?php endif; ?>

		<?php if ( $show_rating_box ) : ?>
			<?php echo wp_review_get_review_box(); ?>
		<?php endif; ?>

		<?php if ( $show_credit ) : ?>
			<a href="<?php esc_url( home_url( '/' ) ); ?>" class="wpr-embed__credit"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
	</div>
	<?php wp_reset_postdata(); ?>

	<?php wp_footer(); ?>
</body>
</html>
