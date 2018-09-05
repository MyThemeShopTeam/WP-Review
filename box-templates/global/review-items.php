<?php
/**
 * Template for review items
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array $items
 * @var int   $post_id
 */

$types = wp_review_get_rating_types();
$type = wp_review_get_post_review_type( $post_id );
?>
<ul class="review-items">
	<?php foreach ( $items as $item ) :
		$item = wp_parse_args( $item, array(
			'wp_review_item_star'  => '',
			'wp_review_item_title' => '',
			'wp_review_item_color' => '',
			'wp_review_item_inactive_color' => '',
			'wp_review_item_positive'       => '',
			'wp_review_item_negative'       => '',
		) );

		$value_text = '';
		if ( 'star' !== $type ) {
			$value_text = ' - <span>' . sprintf( $types[ $type ]['value_text'], $item['wp_review_item_star'] ) . '</span>';
		}
		?>
		<li>
			<div><?php echo wp_kses_post( $item['wp_review_item_title'] ); ?><?php echo wp_kses_post( $value_text ); ?></div>
			<?php
			echo wp_review_rating(
				$item['wp_review_item_star'],
				$post_id,
				array(
					'color' => $item['wp_review_item_color'],
					'inactive_color' => $item['wp_review_item_inactive_color'],
					'positive_count' => $item['wp_review_item_positive'],
					'negative_count' => $item['wp_review_item_negative'],
				)
			);
			?>
		</li>
	<?php endforeach; ?>
</ul>
