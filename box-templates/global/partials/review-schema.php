<?php
/**
 * Template for review schema
 *
 * @since 3.0.0
 * @package WP_Review
 * @var array $review
 */

if ( empty( $review['show_schema_data'] ) || empty( $review['schema'] ) || 'Thing' === $review['schema'] || 'none' === $review['schema'] ) {
	return;
}
$schema = wp_review_get_schema_type_data( $review['schema'] );
if ( ! $schema ) {
	return;
}
$fields      = wp_review_get_schema_fields( $schema );
$image       = $reviewed_item_data = $url = '';
$schema_data = ! empty( $review['schema_data'][ $review['schema'] ] ) ? (array) $review['schema_data'][ $review['schema'] ] : array();
?>
<div class="reviewed-item">

	<?php
	foreach ( $fields as $key => $data ) {
		if ( ! empty( $data['omit'] ) || empty( $schema_data[ $data['name'] ] ) ) {
			continue;
		}

		if ( ! empty( $data['multiline'] ) ) {
			$reviewed_item_data .= '<p><strong class="reviewed-item-data-label">' . $data['label'] . ':</strong> ' . preg_replace( '/\r\n|[\r\n]/', ', ', $schema_data[ $data['name'] ] ) . '</p>';
			continue;
		}

		if ( 'image' === $data['name'] && ! isset( $data['part_of'] ) ) {

			if ( ! empty( $schema_data['image']['id'] ) ) {
				$image = wp_get_attachment_image( $schema_data['image']['id'], apply_filters( 'wp_review_item_reviewed_image_size', 'medium' ) );
			}
			continue;
		}

		if ( 'image' === $data['type'] ) {
			$reviewed_item_data .= '<p><strong class="reviewed-item-data-label">' . $data['label'] . ':</strong> ' . wp_get_attachment_image( $schema_data[ $data['name'] ]['id'] ) . '</p>';
			continue;
		}

		if ( 'url' === $data['name'] && ! isset( $data['part_of'] ) ) {
			if ( ! empty( $schema_data['url'] ) ) {
				$more_text = ! empty( $schema_data['more_text'] ) ? $schema_data['more_text'] : __( '[ More ]', 'wp-review' );
				$link      = '<a href="' . esc_url( $schema_data['url'] ) . '" class="reviewed-item-link">' . esc_html( $more_text ) . '</a>';
				if ( ! empty( $schema_data['use_button_style'] ) ) {
					$url = '<ul class="review-links" style="padding-left: 0; padding-right: 0;"><li>' . $link . '</li></ul>';
				} else {
					$url = '<p>' . $link . '</p>';
				}
			}
			continue;
		}

		$reviewed_item_data .= '<p><strong class="reviewed-item-data-label">' . $data['label'] . ':</strong> ' . $schema_data[ $data['name'] ] . '</p>';
	}
	if ( ! empty( $image ) ) {
		echo '<div class="reviewed-item-image">' . $image . '</div>';
	}
	if ( ! empty( $reviewed_item_data ) ) {
		echo '<div class="reviewed-item-data">' . $reviewed_item_data . $url . '</div>';
	}
	?>
</div>
