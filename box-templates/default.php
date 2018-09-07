<?php
/**
 * WP Review: Default
 * Description: Default Review Box template for WP Review
 * Version: 1.0.2
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 *
 * @package   WP_Review
 * @since     2.0
 * @copyright Copyright (c) 2017, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/*
 *	Available items in $review array:
 *
 *		'post_id',
		'type',
		'heading',
		'author',
		'items',
		'hide_desc',
		'desc',
		'desc_title',
		'pros',
		'cons',
		'total',
		'colors',
		'width',
		'align',
		'schema',
		'schema_data',
		'show_schema_data',
		'rating_schema',
		'links',
		'user_review',
		'user_review_type',
		'user_review_total',
		'user_review_count',
		'user_has_reviewed',
		'comments_review'
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rating_types = wp_review_get_rating_types();

$classes = implode( ' ', $review['css_classes'] );

if ( ! empty( $review['fontfamily'] ) ) : ?>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
	<style type="text/css">
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper { font-family: 'Open Sans', sans-serif; }
	</style>
<?php endif; ?>

<div id="review" class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( empty( $review['heading'] ) ) : ?>
		<?php echo esc_html( apply_filters( 'wp_review_item_title_fallback', '' ) ); ?>
	<?php else : ?>
		<div class="review-heading">
			<h5 class="review-title">
				<?php echo esc_html( $review['heading'] ); ?>

				<?php if ( ! empty( $review['product_price'] ) ) : ?>
					<span class="review-price"><?php echo esc_html( $review['product_price'] ); ?></span>
				<?php endif; ?>
			</h5>
		</div>
	<?php endif; ?>

	<?php wp_review_load_template( 'global/partials/review-schema.php', compact( 'review' ) ); ?>

	<?php if ( $review['items'] && is_array( $review['items'] ) && empty( $review['disable_features'] ) ) : ?>
		<ul class="review-list">
			<?php foreach ( $review['items'] as $item ) :
				$item = wp_parse_args( $item, array(
					'wp_review_item_star'  => '',
					'wp_review_item_title' => '',
					'wp_review_item_color' => '',
					'wp_review_item_inactive_color' => '',
					'wp_review_item_positive'       => '',
					'wp_review_item_negative'       => '',
				) );
				$value_text = '';
				if ( 'star' != $review['type'] ) {
					$value_text = ' - <span>' . sprintf( $rating_types[ $review['type'] ]['value_text'], $item['wp_review_item_star'] ) . '</span>';
				}
				?>
				<li>
					<?php
					echo wp_review_rating(
						$item['wp_review_item_star'],
						$review['post_id'],
						array(
							'color' => $item['wp_review_item_color'],
							'inactive_color' => $item['wp_review_item_inactive_color'],
							'positive_count' => $item['wp_review_item_positive'],
							'negative_count' => $item['wp_review_item_negative'],
						)
					);
					?>
					<span><?php echo wp_kses_post( $item['wp_review_item_title'] ); ?><?php echo $value_text; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( ! $review['hide_desc'] ) : ?>
		<?php if ( ! empty( $review['total'] ) ) :
			$total_text = $review['total'];
			if ( 'star' != $review['type'] ) {
				$total_text = sprintf( $rating_types[ $review['type'] ]['value_text'], $total_text );
			}
			?>
			<div class="review-total-wrapper">
				<span class="review-total-box"><?php echo $total_text; ?></span>
				<?php if ( 'point' != $review['type'] && 'percentage' != $review['type'] ) :
					echo wp_review_rating( $review['total'], $review['post_id'], array(
						'review_total' => true,
						'class'        => 'review-total',
					) );
				endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $review['desc'] ) : ?>
			<div class="review-desc">
				<p class="review-summary-title"><strong><?php echo $review['desc_title']; ?></strong></p>
				<?php // echo do_shortcode( shortcode_unautop( wp_kses_post( wpautop( $review['desc'] ) ) ) ); ?>
				<?php echo apply_filters( 'wp_review_desc', $review['desc'], $review['post_id'] ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $review['user_review'] ) : ?>
		<div class="user-review-area visitors-review-area">
			<?php echo wp_review_user_rating( $review['post_id'] ); ?>
			<div class="user-total-wrapper">
				<h5 class="user-review-title"><?php esc_html_e( 'User Review', 'wp-review' ); ?></h5>
				<span class="review-total-box">
					<?php
					$usertotal_text = $review['user_review_total'];
					if ( 'star' != $review['user_review_type'] ) {
						$usertotal_text = sprintf( $rating_types[ $review['user_review_type'] ]['value_text'], $review['user_review_total'] );
					}
					?>
					<span class="wp-review-user-rating-total"><?php echo esc_html( $usertotal_text ); ?></span>
					<small>(<span class="wp-review-user-rating-counter"><?php echo esc_html( $review['user_review_count'] ); ?></span> <?php echo esc_html( _n( 'vote', 'votes', $review['user_review_count'], 'wp-review' ) ); ?>)</small>
				</span>
			</div>
		</div>
	<?php endif; // $review['user_review'] ?>

	<?php if ( $review['comments_review'] && ! $review['hide_comments_rating'] ) : ?>
		<div class="user-review-area comments-review-area">
			<?php echo wp_review_user_comments_rating( $review['post_id'] ); ?>
			<div class="user-total-wrapper">
				<span class="user-review-title"><?php esc_html_e( 'Comments Rating', 'wp-review' ); ?></span>
				<span class="review-total-box">
					<?php
					$comment_reviews       = mts_get_post_comments_reviews( $review['post_id'] );
					$comments_review_total = $comment_reviews['rating'];
					$comments_review_count = $comment_reviews['count'];
					$comments_total_text  = $comments_review_total;
					if ( 'star' != $review['user_review_type'] ) {
						$comments_total_text = sprintf( $rating_types[ $review['user_review_type'] ]['value_text'], $comments_review_total );
					}
					?>
					<span class="wp-review-user-rating-total"><?php echo esc_html( $comments_total_text ); ?></span>
					<small>(<span class="wp-review-user-rating-counter"><?php echo esc_html( $comments_review_count ); ?></span> <?php echo esc_html( _n( 'review', 'reviews', $comments_review_count, 'wp-review' ) ); ?>)</small>
					<br />
					<small class="awaiting-response-wrapper"></small>
				</span>
			</div>
		</div>
	<?php endif; // $review['comments_review'] ?>

	<?php wp_review_load_template( 'global/partials/review-links.php', compact( 'review' ) ); ?>
</div>

<?php
$colors = $review['colors'];
ob_start();
// phpcs:disable
?>
<style type="text/css">
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper {
		width: <?php echo $review['width']; ?>%;
		float: <?php echo $review['align']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper,
	.wp-review-<?php echo $review['post_id']; ?> .review-title,
	.wp-review-<?php echo $review['post_id']; ?> .review-desc p,
	.wp-review-<?php echo $review['post_id']; ?> .reviewed-item p {
		color: <?php echo $colors['fontcolor']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-title {
		padding-top: 15px;
		font-weight: bold;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-links a {
		color: <?php echo $colors['color'] ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-links a:hover {
		background: <?php echo $colors['color']; ?>;
		color: #fff;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-list li,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper {
		background: <?php echo $colors['bgcolor2'] ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-title,
	.wp-review-<?php echo $review['post_id']; ?> .review-list li:nth-child(2n),
	.wp-review-<?php echo $review['post_id']; ?> .wpr-user-features-rating .user-review-title {
		background: <?php echo $colors['bgcolor1']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper,
	.wp-review-<?php echo $review['post_id']; ?> .review-title,
	.wp-review-<?php echo $review['post_id']; ?> .review-list li,
	.wp-review-<?php echo $review['post_id']; ?> .review-list li:last-child,
	.wp-review-<?php echo $review['post_id']; ?> .user-review-area,
	.wp-review-<?php echo $review['post_id']; ?> .reviewed-item,
	.wp-review-<?php echo $review['post_id']; ?> .review-links,
	.wp-review-<?php echo $review['post_id']; ?> .wpr-user-features-rating {
		border-color: <?php echo $colors['bordercolor']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .wpr-rating-accept-btn {
		background: <?php echo $colors['color']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-title {
		color: inherit;
	}
</style>
<?php
$color_output = ob_get_clean();

// Apply legacy filter.
$color_output = apply_filters( 'wp_review_color_output', $color_output, $review['post_id'], $colors );

/**
 * Filters style output of default template.
 *
 * @since 3.0.0
 *
 * @param string $style   Style output (include <style> tag).
 * @param int    $post_id Current post ID.
 * @param array  $colors  Color data.
 */
$color_output = apply_filters( 'wp_review_box_template_default_style', $color_output, $review['post_id'], $colors );

echo $color_output;

// Schema json-dl.
echo wp_review_get_schema( $review );
// phpcs:enable
