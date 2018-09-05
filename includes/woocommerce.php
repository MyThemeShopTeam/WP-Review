<?php
/**
 * WooCommerce integrate
 *
 * @package WP_Review
 */

/**
 * Removes WooCommerce hook.
 */
function wp_review_wc_remove_hook() {
	if ( wp_review_option( 'replace_wc_rating' ) ) {
		remove_action( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) );
	}
}
add_action( 'init', 'wp_review_wc_remove_hook' );


if ( wp_review_option( 'replace_wc_rating' ) ) {
	/**
	 * Output the product rating.
	 */
	function woocommerce_template_single_rating() {
		if ( post_type_supports( 'product', 'comments' ) ) {
			echo do_shortcode( '[wp-review-total id="' . get_the_ID() . '" class="wp-review-product-rating review-total-only review-total-shortcode" context="product-rating"]' );
		}
	}


	/**
	 * Uses our comment ratings count for product review count.
	 *
	 * @param  int    $count   Review count.
	 * @param  object $product Product object.
	 * @return int
	 */
	function wp_review_wc_product_review_count( $count, $product ) {
		$comment_ratings = mts_get_post_comments_reviews( $product->get_id() );
		return $comment_ratings['count'];
	}
	add_filter( 'woocommerce_product_get_review_count', 'wp_review_wc_product_review_count', 10, 2 );


	/**
	 * Uses our comment ratings total for product avg rating.
	 *
	 * @param  int    $value   Avg rating.
	 * @param  object $product Product object.
	 * @return int
	 */
	function wp_review_wc_product_average_rating( $value, $product ) {
		$comment_ratings = mts_get_post_comments_reviews( $product->get_id() );
		return $comment_ratings['rating'];
	}
	add_filter( 'woocommerce_product_get_average_rating', 'wp_review_wc_product_average_rating', 10, 2 );


	/**
	 * Adds our rating schema to product schema.
	 *
	 * @param  array  $markup  Product schema markup.
	 * @param  object $product Product object.
	 * @return array
	 */
	function wp_review_wc_product_schema( $markup, $product ) {
		$rating_schema = wp_review_get_rating_schema( $product->get_id() );
		switch ( $rating_schema ) {
			case 'author':
				$rating_value = floatval( get_post_meta( $product->get_id(), 'wp_review_total', true ) );
				$rating_type = wp_review_get_post_review_type( $product->get_id() );
				$rating_type = wp_review_get_rating_type_data( $rating_type );
				$best_rating = $rating_type['max'];

				if ( isset( $markup['aggregateRating']['reviewCount'] ) ) {
					unset( $markup['aggregateRating']['reviewCount'] );
				}
				$markup['aggregateRating']['ratingValue'] = get_post_meta( $product->get_id(), 'wp_review_total', true );
				break;

			case 'visitors':
				$visitors_rating = mts_get_post_reviews( $product->get_id() );
				$rating_value = $visitors_rating['rating'];
				$review_count = $visitors_rating['count'];
				$rating_type = wp_review_get_post_user_review_type( $product->get_id() );
				$rating_type = wp_review_get_rating_type_data( $rating_type );
				$best_rating = $rating_type['max'];
				break;

			case 'comments':
				$rating_type = wp_review_get_post_user_review_type( $product->get_id() );
				$rating_type = wp_review_get_rating_type_data( $rating_type );
				$markup['aggregateRating']['bestRating'] = $rating_type['max'];
				return $markup;
				break;
		}

		if ( ! $rating_value ) {
			if ( isset( $markup['aggregateRating'] ) ) {
				unset( $markup['aggregateRating'] );
			}
			return $markup;
		}

		$markup['aggregateRating']['ratingValue'] = $rating_value;
		$markup['aggregateRating']['bestRating'] = $best_rating;
		if ( isset( $review_count ) ) {
			$markup['aggregateRating']['reviewCount'] = $review_count;
		}

		return $markup;
	}
	add_filter( 'woocommerce_structured_data_product', 'wp_review_wc_product_schema', 10, 2 );


	/**
	 * Adds verified class to comment.
	 *
	 * @since 3.0.4
	 *
	 * @param array  $classes    An array of comment classes.
     * @param string $class      A comma-separated list of additional classes added to the list.
     * @param int    $comment_id The comment id.
	 * @return array
	 */
	function wp_review_wc_comment_class( $classes, $class, $comment_id ) {
		if ( intval( get_comment_meta( $comment_id, 'verified', true ) ) ) {
			$classes[] = 'verified';
		}
		return $classes;
	}
	add_filter( 'comment_class', 'wp_review_wc_comment_class', 10, 3 );
}


add_filter( 'woocommerce_admin_reports', 'add_admin_reports');

function add_admin_reports( $reports ) {
	$reports['wp-reviews'] = array(
		'title'   => __( 'WP Reviews', 'wp-review' ),
		'reports' => array(
			'most_reviews' => array(
				'title'       => __( 'Most Reviews', 'wp-review' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => 'get_most_reviews_admin_report',
			),
			'highest_rating' => array(
				'title'       => __( 'Highest Rating', 'wp-review' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => 'get_most_reviews_admin_report',
			),
			'lowest_rating' => array(
				'title'       => __( 'Lowest Rating', 'wp-review' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => 'get_most_reviews_admin_report',
			),
		),
	);

	return $reports;
}

function get_most_reviews_admin_report() {
/**
 * HTML for product reviews report
 *
 * @type \WC_Product[] $products
 */
?>
<div id="poststuff" class="woocommerce-reports-wide wc-product-reviews-pro-report">
	<table class="wp-list-table widefat fixed product-reviews">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Product', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Reviews', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Highest Rating', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Lowest Rating', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Average Rating', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Rating Type', 'wp-review' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'wp-review' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php
			global $wpdb;
			$query = "
			  SELECT ID
			  FROM {$wpdb->prefix}posts
			  WHERE
			  {$wpdb->prefix}posts.post_type = 'product'
			  AND {$wpdb->prefix}posts.post_status = 'publish'
			  AND {$wpdb->prefix}posts.comment_count > 0
			  ORDER BY {$wpdb->prefix}posts.comment_count
			  DESC;
			";

			$products = $wpdb->get_results($query, ARRAY_A);
			$ratings_data = array();
			if ( ! empty( $products ) ) {

				foreach ( $products as $product ) {
					$product_id = $product['ID'];
					$comment_reviews = mts_get_post_comments_reviews( $product_id );
					$type = wp_review_get_post_user_review_type( $product_id );
					$rating_type = wp_review_get_rating_type_data( $type );

					if($comment_reviews['rating'] > 0) {
						$highest_rating_id = get_comments( array(
							'post_id' => $product_id,
							'type'    => WP_REVIEW_COMMENT_TYPE_COMMENT,
							'status'  => 'approve',
							'order'		=> 'DESC',
							'orderby' => 'meta_value_num',
							'meta_key' => 'wp_review_comment_rating',
							'fields' => 'ids',
							'number' => 1
						) );
						$lowest_rating_id = get_comments( array(
							'post_id' => $product_id,
							'type'    => WP_REVIEW_COMMENT_TYPE_COMMENT,
							'status'  => 'approve',
							'order'		=> 'ASC',
							'orderby' => 'meta_value_num',
							'meta_key' => 'wp_review_comment_rating',
							'fields' => 'ids',
							'number' => 1
						) );
						$highest_rating = '';
						$lowest_rating = '';
						if($highest_rating_id) {
							$highest_rating = get_comment_meta($highest_rating_id[0], 'wp_review_comment_rating', true);
						}
						if($lowest_rating_id) {
							$lowest_rating = get_comment_meta($lowest_rating_id[0], 'wp_review_comment_rating', true);
						}
						$ratings_data[$product_id] = array(
							'product_id' => $product_id,
							'title' => get_the_title( $product_id ),
							'total_reviews' => $comment_reviews['count'],
							'highest_rating' => $highest_rating,
							'lowest_rating' => $lowest_rating,
							'average' => $comment_reviews['rating'],
							'max' => $rating_type['max'],
							'rating_type' => $rating_type['label']
						);
					}
					}
				}
				

				if(!empty($ratings_data)) {

					if(isset($_GET['report']) && !empty($_GET['report'])) {
						usort($ratings_data, function($a, $b){
							$a = $a['average'];
							$b = $b['average'];
							if ($a == $b) return 0;
							if('highest_rating' === $_GET['report']) {
								return ($a > $b) ? -1 : 1;
							} else if('lowest_rating' === $_GET['report']) {
								return ($a < $b) ? -1 : 1;
							}
						});	
					}


				
					foreach($ratings_data as $rating_data) { ?>
							<tr>
								<td><?php echo esc_html( $rating_data['title'] ); ?></td>
								<td><?php echo $rating_data['total_reviews']; ?></td>
								<td><?php echo $rating_data['highest_rating']; ?></td>
								<td><?php echo $rating_data['lowest_rating']; ?></td>
								<td><strong><?php echo $rating_data['average'].'/'.$rating_data['max']; ?></strong></td>
								<td><?php echo $rating_data['rating_type']; ?></td>
								<td>
									<a href="<?php echo get_edit_post_link( $rating_data['product_id'] ); ?>">
										<?php esc_html_e( 'View Product', 'wp-review' ); ?>
									</a>
								</td>
							</tr>
			<?php }
					} ?>
		</tbody>
	</table>
</div>
<?php
}