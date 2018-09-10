<?php
/**
 * WP Review: Aqua
 * Description: Aqua Review Box template for WP Review
 * Version: 1.0.0
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 *
 * @package   WP_Review
 * @since     3.0.0
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

?>

<link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
<style type="text/css">
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper { font-family: 'Comfortaa', cursive; }
</style>

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

	<?php if ( ! empty( $review['total'] && ! $review['hide_desc'] ) ) :
		$total_text = $review['total'];
		if ( 'star' != $review['type'] ) {
			$total_text = sprintf( $rating_types[ $review['type'] ]['value_text'], $total_text );
		}
		?>
		<div class="review-total-wrapper">
			<span class="review-total-box">
				<h5><?php esc_html_e( 'Overall', 'wp-review' ); ?></h5>
				<div><?php echo $total_text; ?></div>
			</span>
			<?php
			echo wp_review_rating( $review['total'], $review['post_id'], array(
				'review_total' => true,
				'class'        => 'review-total',
			) );
			?>
		</div>
	<?php endif; ?>

	<?php if ( $review['items'] && is_array( $review['items'] ) ) : ?>
		<ul class="review-list">
			<?php foreach ( $review['items'] as $item ) :
				$item = wp_parse_args( $item, array(
					'wp_review_item_star'  => '',
					'wp_review_item_title' => '',
					'wp_review_item_color' => '',
					'wp_review_item_inactive_color' => '',
				) );
				$value_text = '';
				if ( 'star' != $review['type'] ) {
					$value_text = ' - <span>' . sprintf( $rating_types[ $review['type'] ]['value_text'], $item['wp_review_item_star'] ) . '</span>';
				}
				?>
				<li>
					<span><?php echo wp_kses_post( $item['wp_review_item_title'] ); ?><?php echo $value_text; ?></span>
					<?php
					echo wp_review_rating(
						$item['wp_review_item_star'],
						$review['post_id'],
						array(
							'color' => $item['wp_review_item_color'],
							'inactive_color' => $item['wp_review_item_inactive_color'],
						)
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>
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

	<?php if ( ! $review['hide_desc'] ) : ?>

		<?php if ( $review['desc'] ) : ?>
			<div class="review-desc">
				<p class="review-summary-title"><strong><?php echo $review['desc_title']; ?></strong></p>
				<?php // echo do_shortcode( shortcode_unautop( wp_kses_post( wpautop( $review['desc'] ) ) ) ); ?>
				<?php echo apply_filters( 'wp_review_desc', $review['desc'], $review['post_id'] ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php wp_review_load_template( 'global/partials/review-links.php', compact( 'review' ) ); ?>
</div>

<?php
if ( ! function_exists( 'color_luminance' ) ) {
	/**
	 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.
	 *
	 * @param string $hex     Colour as hexadecimal (with or without hash).
	 * @param float  $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() ).
	 * @return string         Lightened/Darkend colour as hexadecimal (with hash);
	 */
	function color_luminance( $hex, $percent ) {
		// Validate hex string.
		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
		$new_hex = '#';

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}

		// Convert to decimal and change luminosity.
		for ( $i = 0; $i < 3; $i++ ) {
			$dec = hexdec( substr( $hex, $i * 2, 2 ) );
			$dec = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ), 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}
}
$colors = $review['colors'];
$dark_color = color_luminance( $colors['color'], '-0.2' );

ob_start();
// phpcs:disable
?>
<style type="text/css">
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper {
		width: <?php echo $review['width']; ?>%;
		float: <?php echo $review['align']; ?>;
		border: 1px solid <?php echo $colors['bordercolor']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-desc {
		padding: 25px 30px 25px 30px;
		line-height: 26px;
		clear: both;
		border-bottom: 1px solid;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper,
	.wp-review-<?php echo $review['post_id']; ?> .review-title,
	.wp-review-<?php echo $review['post_id']; ?> .review-desc p,
	.wp-review-<?php echo $review['post_id']; ?> .reviewed-item p {
		color: <?php echo $colors['fontcolor']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-links a {
		background: <?php echo $colors['color']; ?>;
		padding: 9px 20px 6px 20px;
		box-shadow: 0 2px <?php echo $dark_color; ?>, inset 0 1px rgba(255,255,255,0.2);
		border: none;
		color: #fff;
		border: 1px solid <?php echo $dark_color; ?>;
		cursor: pointer;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-list li,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper {
		background: <?php echo $colors['bgcolor2']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .review-list li {
		padding: 30px 30px 20px 30px;
		width: 50%;
		float: left;
		border-right: 1px solid <?php echo $colors['bordercolor']; ?>;
		box-sizing: border-box;
	}
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-star-type .wpr-user-features-rating .review-list {
		width: 100%;
	}
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .review-list li,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .review-list li {
		width: 100%;
		padding: 15px 30px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .review-list li > span,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .review-list li > span {
		display: inline-block;
		position: absolute;
		z-index: 1;
		top: 23px;
		left: 45px;
		font-size: 14px;
		line-height: 1;
		color: <?php echo $colors['bgcolor2']; ?>;
		-webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
	}
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .wpr-user-features-rating .review-list li > span,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .wpr-user-features-rating .review-list li > span {
	    color: inherit;
	}
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .wpr-user-features-rating .review-list li .wp-review-input-set + span,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .wpr-user-features-rating .review-list li .wp-review-input-set + span,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .wpr-user-features-rating .review-list li .wp-review-user-rating:hover + span,
	.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .wpr-user-features-rating .review-list li .wp-review-user-rating:hover + span {
	    color: #fff;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-star-type .review-list li:nth-child(2n+1) { clear: left; border-right: 1px solid <?php echo $colors['bordercolor']; ?>; }
	.wp-review-<?php echo $review['post_id']; ?> .review-links {
		padding: 30px 30px 20px 30px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-point-type .review-result,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-percentage-type .review-result,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-point .review-result-wrapper,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-percentage .review-result-wrapper {
		height: 28px;
	}
	.wp-review-comment-<?php echo $review['post_id']; ?> .wp-review-comment-rating .review-point .review-result-wrapper .review-result,
	.wp-review-comment-<?php echo $review['post_id']; ?> .wp-review-comment-rating .review-percentage .review-result-wrapper .review-result {
	    height: 22px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-result-wrapper i {
		font-size: 18px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .mb-5 {
		text-transform: uppercase;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .wpr-user-features-rating {
		margin-top: -1px;
		clear: both;
		float: left;
		width: 100%;
	}
	.wp-review-<?php echo $review['post_id']; ?> .user-review-area {
		padding: 18px 30px;
		border-top: 1px solid;
		margin-top: -1px;
		float: left;
		width: 100%;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-title {
		letter-spacing: 1px;
		font-weight: 700;
		padding: 15px 30px;
		text-transform: none;
		background: <?php echo $colors['bgcolor1']; ?>;
		color: #fff;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-total-wrapper {
		width: 40%;
		margin: 0;
		padding: 42px 0;
		color: #fff;
		text-align: center;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list {
		clear: none;
		width: 60%;
	}
	<?php if( $review['hide_desc'] ) { ?>
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list {
		width: 100%;
	}
	<?php } ?>
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .review-star,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-feature-rating-star {
		float: left;
		display: block;
		margin: 10px 0 0 0;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-feature-rating-star + span { clear: left; display: block; }
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-rating.wp-review-user-feature-rating-star,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-rating.wp-review-user-feature-rating-star .review-star {
	    margin: 0;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-rating.wp-review-user-feature-rating-star .review-result-wrapper {
	    margin-left: -5px;
        margin-bottom: 6px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list .wp-review-user-feature-rating-star .review-result { letter-spacing: -2.2px; }
	.wp-review-<?php echo $review['post_id']; ?> .review-total-wrapper .review-point.review-total,
	.wp-review-<?php echo $review['post_id']; ?> .review-total-wrapper .review-percentage.review-total {
		width: 70%;
		display: inline-block;
		margin: 20px auto 0 auto;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-total-wrapper span.review-total-box {
		float: left;
		text-align: center;
		padding: 0;
		color: <?php echo $colors['fontcolor']; ?>;
		line-height: 1.5;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-total-wrapper span.review-total-box h5 {
		margin-top: 6px;
		color: inherit;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-point-type .review-total-wrapper span.review-total-box,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-percentage-type .review-total-wrapper span.review-total-box,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .wpr-user-features-rating .review-list {
		width: 100%;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .wpr-user-features-rating .review-list li {
	    border-right: 0;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-star.review-total {
		color: #fff;
		margin-top: 10px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-title {
		color: inherit;
		padding: 18px 30px 16px;
		margin: 0;
		border-bottom: 1px solid;
		border-top: 1px solid;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-total-wrapper .user-review-title {
		display: inline-block;
		color: inherit;
		text-transform: uppercase;
		letter-spacing: 1px;
		padding: 0;
		border: 0;
		margin-top: 3px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .reviewed-item {
		padding: 30px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-area .review-percentage,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-area .review-point {
		width: 20%;
		float: right;
		margin-top: -2px;
	}
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper,
	.wp-review-<?php echo $review['post_id']; ?> .review-title,
	.wp-review-<?php echo $review['post_id']; ?> .review-list li,
	.wp-review-<?php echo $review['post_id']; ?> .review-list li:last-child,
	.wp-review-<?php echo $review['post_id']; ?> .user-review-area,
	.wp-review-<?php echo $review['post_id']; ?> .reviewed-item,
	.wp-review-<?php echo $review['post_id']; ?> .review-links,
	.wp-review-<?php echo $review['post_id']; ?> .wpr-user-features-rating,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-title,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-total-wrapper,
	.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-desc {
		border-color: <?php echo $colors['bordercolor']; ?>;
	}
	.wp-review-<?php echo $review['post_id']; ?> .wpr-rating-accept-btn {
		background: <?php echo $colors['color']; ?>;
		margin: 10px 30px;
		width: -moz-calc(100% - 60px);
		width: -webkit-calc(100% - 60px);
		width: -o-calc(100% - 60px);
		width: calc(100% - 60px);
		border-radius: 3px;
	}
	@media screen and (max-width:570px) {
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list {
			width: 100%;
		}
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-total-wrapper {
			width: 100%;
			border-bottom: 1px solid <?php echo $colors['bordercolor']; ?>;
			border-left: 0;
			padding: 15px 0;
		}
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper.wp-review-star-type .review-list li:nth-child(2n+1) { clear: none; border-right: 0; }
	}
	@media screen and (max-width:480px) {
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-title,
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .reviewed-item,
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-desc,
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-area { padding: 15px; }
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-list li,
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-review-title { padding: 12px 15px; border-right: 0; }
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .ui-tabs-nav { padding: 0 15px; }
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .review-links { padding: 15px 15px 5px; }
		.wp-review-<?php echo $review['post_id']; ?>.review-wrapper .user-total-wrapper { max-width: 60%; font-size: 14px; }
		.wp-review-<?php echo $review['post_id']; ?>.wp-review-point-type .review-list li > span,
		.wp-review-<?php echo $review['post_id']; ?>.wp-review-percentage-type .review-list li > span {
    		top: 12px;
    		left: 30px;
    	}
	}
</style>
<?php
$color_output = ob_get_clean();

// Apply legacy filter.
$color_output = apply_filters( 'wp_review_color_output', $color_output, $review['post_id'], $colors );

/**
 * Filters style output of aqua template.
 *
 * @since 3.0.0
 *
 * @param string $style   Style output (include <style> tag).
 * @param int    $post_id Current post ID.
 * @param array  $colors  Color data.
 */
$color_output = apply_filters( 'wp_review_box_template_aqua_style', $color_output, $review['post_id'], $colors );

echo $color_output;

// Schema json-dl.
echo wp_review_get_schema( $review );
// phpcs:enable
