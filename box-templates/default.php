<?php
/**
 * WP Review: Default
 * Description: Default Review Box template for WP Review
 * Version: 1.0.1
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
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
		'total', 
		'colors', 
		'schema', 
		'links',
		'user_review',
		'user_review_type',
		'user_review_total',
		'user_review_count',
		'user_has_reviewed',
		'add_backlink'
 * 
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $wp_review_rating_types;

$classes = array();
if ( isset( $review['width'] ) && $review['width'] < 100 ) {
	$classes[] = 'wp-review-box-floating';
	if ( isset( $review['align'] ) && $review['align'] == 'right' ) {
		$classes[] = 'wp-review-box-float-right';
	} else {
		$classes[] = 'wp-review-box-float-left';
	}
} else {
	$classes[] = 'wp-review-box-full-width';
}
if ($review['add_backlink']) {
	$classes[] = 'wp-review-box-with-backlink';
} else {
	$classes[] = 'wp-review-box-no-backlink';
}
?>
<div itemscope itemtype="http://schema.org/Review" id="review" class="review-wrapper wp-review-<?php echo $review['post_id']; ?> wp-review-<?php echo $review['type']; ?>-type <?php echo join(' ', $classes); ?>" >
	<?php if ( empty( $review['heading'] ) ) : ?>
		<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/<?php echo urlencode( $review['schema'] ); ?>">
			<meta itemprop="name" content="<?php echo esc_attr( get_the_title( $review['post_id'] ) ); ?>">
		</span>
	<?php else: ?>
		<h5 itemprop="itemReviewed" itemscope itemtype="http://schema.org/<?php echo urlencode( $review['schema'] ); ?>" class="review-title"><span itemprop="name"><?php echo esc_html( $review['heading'] ); ?></span></h5>
	<?php endif; ?>
	<span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="<?php echo esc_attr( $review['author'] ); ?>"></span>
	<?php if ( $review['items'] ) : ?>
		<ul class="review-list">
			<?php foreach ( $review['items'] as $item ) :
				$value_text = '';//' - <span>'.$item['wp_review_item_star'].'</span>';
				if ($review['type'] != 'star') {
					$value_text = ' - <span>'.sprintf($wp_review_rating_types[$review['type']]['value_text'], $item['wp_review_item_star']).'</span>';
				}
			 ?>
				<li>
					<span><?php echo wp_kses_post( $item['wp_review_item_title'] ); ?><?php echo $value_text; ?></span>
					<?php echo wp_review_rating( $item['wp_review_item_star'] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ( ! $review['hide_desc'] ) : ?>
		<?php if ( $review['desc'] ) : ?>
			<div class="review-desc" itemprop="description">
				<p class="review-summary-title"><strong><?php echo $review['desc_title']; ?></strong></p>
				<?php echo do_shortcode( shortcode_unautop( wp_kses_post( wpautop( $review['desc'] ) ) ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $review['total'] ) ) :
			$total_text = $review['total'];
			if ( $review['type'] != 'star' ) {
				$total_text = sprintf( $wp_review_rating_types[$review['type']]['value_text'], $total_text );
			}
		 ?>
			<div class="review-total-wrapper">
				<span class="review-total-box"><?php echo $total_text; ?></span>
				<?php if ($review['type'] != 'point' && $review['type'] != 'percentage') : ?>
					<?php echo wp_review_rating( $review['total'], $review['post_id'], array('class' => 'review-total') ); ?>
				<?php endif; ?>
			</div>
			<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
				<meta itemprop="ratingValue" content="<?php echo esc_attr( $review['total'] ); ?>">
				<meta itemprop="bestRating" content="<?php echo $wp_review_rating_types[$review['type']]['max']; ?>">
			</span>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( $review['user_review'] ) : ?>
		<div class="user-review-area visitors-review-area">
			<div class="user-total-wrapper">
				<span class="user-review-title"><?php _e( 'User Rating', 'wp-review' ); ?></span>
				<span class="review-total-box">
					<?php 
					$usertotal_text = $review['user_review_total'];
					if ($review['user_review_type'] != 'star') {
						$usertotal_text = sprintf( $wp_review_rating_types[$review['user_review_type']]['value_text'], $review['user_review_total'] );
					}
					?>
					<span class="wp-review-user-rating-total"><?php echo esc_html( $usertotal_text ); ?></span>
					<small>(<span class="wp-review-user-rating-counter"><?php echo esc_html( $review['user_review_count'] ); ?></span> <?php echo _n( 'vote', 'votes', $review['user_review_count'], 'wp-review' ); ?>)</small>
				</span>
			</div>
			<?php echo wp_review_user_rating( $review['post_id'] ); ?>
		</div>
	<?php endif; // $review['user_review'] ?>
</div>
<?php if ($review['add_backlink']) : ?>
	<?php echo wp_review_get_backlink(); ?>
<?php endif; ?>
<?php 
$colors = $review['colors'];
$color_output = <<<EOD

<style type="text/css">
	.wp-review-{$review['post_id']}.review-wrapper { width: {$review['width']}%; float: {$review['align']} }
	.wp-review-{$review['post_id']}.review-wrapper, .wp-review-{$review['post_id']} .review-title, .wp-review-{$review['post_id']} .review-desc p { color: {$colors['fontcolor']};}
	.wp-review-{$review['post_id']} .review-links a { color: {$colors['color']};}
	.wp-review-{$review['post_id']} .review-links a:hover { color: {$colors['fontcolor']};}
	.wp-review-{$review['post_id']} .review-list li, .wp-review-{$review['post_id']}.review-wrapper{ background: {$colors['bgcolor2']};}
	.wp-review-{$review['post_id']} .review-title, .wp-review-{$review['post_id']} .review-list li:nth-child(2n){background: {$colors['bgcolor1']};}
	.wp-review-{$review['post_id']}.review-wrapper, .wp-review-{$review['post_id']} .review-title, .wp-review-{$review['post_id']} .review-list li, .wp-review-{$review['post_id']} .review-list li:last-child, .wp-review-{$review['post_id']} .user-review-area{border-color: {$colors['bordercolor']};}
</style>

EOD;

// Apply legacy filter
echo apply_filters( 'wp_review_color_output', $color_output, $review['post_id'], $review['colors'] );
