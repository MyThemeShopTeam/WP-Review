<?php
/**
 * Comments related functions
 *
 * @package WP_Review
 */

/**
 * Overrides comments count.
 */
function wp_review_override_comments_count() {
	remove_filter( 'get_comments_number', 'mts_comment_count', 0 );
	add_filter( 'get_comments_number', 'wp_review_comment_count', 0 );
}
add_action( 'after_setup_theme', 'wp_review_override_comments_count', 30 );

/**
 * Filters comment count.
 *
 * @param int $count Comment count.
 * @return int
 */
function wp_review_comment_count( $count ) {
	if ( ! is_admin() ) {
		$comments         = get_comments( 'status=approve&post_id=' . get_the_ID() );
		$comments_by_type = separate_comments( $comments );
		if ( isset( $comments_by_type['comment'] ) ) {
			$wp_review_comments_count = isset( $comments_by_type['wp_review_comment'] ) ? count( $comments_by_type['wp_review_comment'] ) : 0;
			return count( $comments_by_type['comment'] ) + $wp_review_comments_count;
		} else {
			return $count;
		}
	}

	return $count;
}


/**
 * Add the title to our admin area, for editing, etc
 */
function wp_review_comment_add_meta_box() {
	global $wp_review_rating_types, $comment;
	$type = wp_review_get_post_user_review_type( $comment->comment_post_ID );
	if ( ! $type ) {
		$type = 'star';
	}
	add_meta_box(
		'wp-review-comment-rating',
		// translators: rating label.
		sprintf( __( 'WP Review Rating (%s)', 'wp-review' ), $wp_review_rating_types[ $type ]['label'] ),
		'wp_review_comment_meta_box_fields',
		'comment',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_comment', 'wp_review_comment_add_meta_box' );

/**
 * Shows comment meta box fields.
 *
 * @param object $comment Comment object.
 */
function wp_review_comment_meta_box_fields( $comment ) {
	$comment_id = $comment->comment_ID;
	if ( WP_REVIEW_COMMENT_TYPE_COMMENT === get_comment_type( $comment_id ) ) {
		$rating = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, true );
	} else {
		$rating = get_comment_meta( $comment_id, WP_REVIEW_VISITOR_RATING_METAKEY, true );
	}
	$title        = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_TITLE_METAKEY, true );
	$rating_items = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
	wp_nonce_field( 'wp_review_comment_rating_update', 'wp_review_comment_rating_update', false );
	?>
	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_comment_rating"><?php esc_html_e( 'Review total', 'wp-review' ); ?></label>
		</div>
		<div class="wp-review-field-option">
			<input type="text" class="small-text" name="wp_review_comment_rating" value="<?php echo esc_attr( $rating ); ?>" id="wp_review_comment_rating">
		</div>
	</div>

	<?php
	if ( ! empty( $rating_items ) ) :
		$items = wp_review_get_review_items( $comment->comment_post_ID );
		foreach ( $items as $item_id => $item ) :
			$value = ! empty( $rating_items[ $item_id ] ) ? $rating_items[ $item_id ] : 0;
			?>
			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_comment_rating_item-<?php echo esc_attr( $item_id ); ?>"><?php echo esc_html( $item['wp_review_item_title'] ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input type="text" class="small-text" name="wp-review-comment-feature-rating[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $value ); ?>" id="wp_review_comment_rating_item-<?php echo esc_attr( $item_id ); ?>">
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php
	$comment_qualifier  = get_comment_meta( $comment_id, 'wp_review_comment_qualifier', true );
	$comment_image      = get_comment_meta( $comment_id, 'wp_review_comment_attachment_url', true );
	$comment_image_name = 'wp_review_comment_attachment_url';
	if ( ! $comment_image ) {
		$comment_image = get_comment_meta( $comment_id, 'wp_review_comment_attachment_src', true );
		if ( $comment_image ) {
			$comment_image_name = 'wp_review_comment_attachment_src';
		}
	}

	if ( $comment_qualifier ) {
		?>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_comment_qualifier"><?php echo apply_filters( 'wp_review_comment_qualifier', __( 'Does Product Matches the Description?', 'wp-review' ) ); ?></label>
			</div>
			<div class="wp-review-field-option">
				<select id="wp_review_comment_qualifier" name="wp_review_comment_qualifier">
					<option value=""><?php _e( 'Select', 'wp-review' ); ?></option>
					<option value="yes" <?php selected( $comment_qualifier, 'yes', true ); ?>><?php _e( 'Yes', 'wp-review' ); ?></option>
					<option value="no" <?php selected( $comment_qualifier, 'no', true ); ?>><?php _e( 'No', 'wp-review' ); ?></option>
				</select>
			</div>
		</div>
		<?php
	}

	if ( $comment_image ) {
		if ( is_numeric( $comment_image ) ) {
			$comment_image = wp_get_attachment_url( $comment_image );
		}
		?>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_comment_image"><?php esc_html_e( 'Comment Image', 'wp-review' ); ?></label>
			</div>
			<div class="wp-review-field-option">
				<input type="text" name="<?php echo esc_attr( $comment_image_name ); ?>" value="<?php echo esc_attr( $comment_image ); ?>" />
			</div>
		</div>
	<?php } ?>
	<style>.wp-review-field { margin-bottom: 1em; }</style>
	<?php
}

/**
 * Save our comment (from the admin area).
 *
 * @param int $comment_id Comment ID.
 */
function wp_review_comment_edit_comment( $comment_id ) {
	if (
		! isset( $_POST['wp_review_comment_rating'] ) && ! isset( $_POST['wp-review-comment-feature-rating'] )
		|| ! isset( $_POST['wp_review_comment_rating_update'] )
		|| ! wp_verify_nonce( $_POST['wp_review_comment_rating_update'], 'wp_review_comment_rating_update' )
	) {
		return;
	}

	if ( WP_REVIEW_COMMENT_TYPE_COMMENT === get_comment_type( $comment_id ) ) {
		$meta_key = WP_REVIEW_COMMENT_RATING_METAKEY;
	} else {
		$meta_key = WP_REVIEW_VISITOR_RATING_METAKEY;
	}

	$rating = filter_input( INPUT_POST, 'wp_review_comment_rating', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

	$comment = get_comment( $comment_id );
	update_comment_meta( $comment_id, $meta_key, $rating );
	wp_review_clear_cached_reviews( $comment );

	if ( ! empty( $_POST['wp_review_comment_pros'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_pros', wp_kses_post( wp_unslash( $_POST['wp_review_comment_pros'] ) ) );
	}
	if ( ! empty( $_POST['wp_review_comment_cons'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_cons', wp_kses_post( wp_unslash( $_POST['wp_review_comment_cons'] ) ) );
	}
	if ( ! empty( $_POST['wp_review_comment_qualifier'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_qualifier', wp_kses_post( wp_unslash( $_POST['wp_review_comment_qualifier'] ) ) );
	}

	if ( ! empty( $_POST['wp-review-comment-feature-rating'] ) ) {
		$rating = ! is_array( $_POST['wp-review-comment-feature-rating'] ) ? json_decode( wp_unslash( $_POST['wp-review-comment-feature-rating'] ), true ) : $_POST['wp-review-comment-feature-rating'];
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, $rating );

		$total = 0;
		foreach ( $rating as $value ) {
			$total += floatval( $value );
		}

		update_comment_meta( $comment_id, $meta_key, $total / count( $rating ) );
		wp_review_clear_cached_reviews( $comment );
	}
}
add_action( 'edit_comment', 'wp_review_comment_edit_comment' );


/**
 * Script for Comments quick edit
 */
function wp_review_comment_quick_edit_javascript() {
	?>
	<script type="text/html" id="tmpl-wpr-comment-review-quick-edit">
		<# if ( ! data.features ) { #>
			<div class="inside">
				<label for="wp_review_comment_rating"><?php esc_html_e( 'Review Total', 'wp-review' ); ?></label>
				<input type="text" name="wp_review_comment_rating" size="50" value="{{ data.rating }}" id="wp_review_comment_rating">
			</div>
		<# } else { #>

			<# for ( var i = 0; i < data.features.length; i++ ) { #>
				<# var feature = data.features[ i ]; #>
				<div class="inside">
					<label for="wp_review_comment_feature_{{ feature.id }}">{{ feature.wp_review_item_title }}</label>
					<input type="text" name="wp-review-comment-feature-rating[{{ feature.id }}]" size="50" value="{{ feature.comment_rating }}" id="wp_review_comment_feature_{{ feature.id }}" />
				</div>
			<# } #>

		<# } #>
	</script>

	<script type="text/javascript">
		function wpreview_expandedOpen( id ) {
			var tmpl, editRow, rowData, type, features, tmplData;
			tmpl = wp.template( 'wpr-comment-review-quick-edit' );
			editRow = jQuery( '#replyrow' );
			rowData = jQuery( '#inline-commentreview-' + id );
			type = jQuery( '.comment-review-type', rowData ).val();

			if ( 'wp_review_comment' !== type && 'wp_review_visitor' !== type ) {
				return;
			}

			if ( jQuery( '.comment-review-feature-rating', rowData ).length ) {
				features = jQuery( '.comment-review-feature-rating', rowData ).val();
				features = JSON.parse( features );
			}

			tmplData = {
				title: jQuery( '.comment-review-title', rowData ).val(),
				rating: jQuery( '.comment-review-rating', rowData ).val(),
				type: type,
				features: features,
				pros: jQuery( '.comment-review-pros', rowData ).val(),
				cons: jQuery( '.comment-review-cons', rowData ).val()
			};

			jQuery( '#editwpreview', editRow ).html( tmpl( tmplData ) );
		}
	</script>
	<?php
}
add_action( 'admin_footer-edit-comments.php', 'wp_review_comment_quick_edit_javascript' );

/**
 * Filters comment quick edit link.
 *
 * @param array  $actions Comments list table actions.
 * @param object $comment Comment object.
 * @return array
 */
function wp_review_comment_quick_edit_action( $actions, $comment ) {
	$actions['quickedit'] = sprintf(
		'<span class="quickedit hide-if-no-js"><a onclick="if (typeof(wpreview_expandedOpen) == \'function\') wpreview_expandedOpen(%1$s);" data-comment-id="%1$s" data-post-id="%2$s" data-action="edit" class="vim-q comment-inline" title="%3$s" href="#">%4$s</a></span>',
		$comment->comment_ID,
		$comment->comment_post_ID,
		__( 'Edit this item inline', 'wp-review' ),
		__( 'Quick Edit', 'wp-review' )
	);
	return $actions;
}
add_filter( 'comment_row_actions', 'wp_review_comment_quick_edit_action', 10, 2 );

/**
 * Gets comments rating template.
 *
 * @param float $value      Rating value.
 * @param int   $comment_id Comment ID.
 * @param array $args       Custom args.
 * @return string
 */
function wp_review_comment_rating( $value, $comment_id = null, $args = array() ) {
	global $post;

	if ( ! empty( $comment_id ) ) {
		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;

		$rating_items = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
		if ( $rating_items && is_array( $rating_items ) ) {
			return wp_review_comment_rating_items( $rating_items, $comment );
		}
	} else {
		$post_id = $post->ID;
	}

	$type = wp_review_get_post_user_review_type( $post_id );

	if ( empty( $type ) ) {
		return '';
	}

	$rating_type = wp_review_get_rating_type_data( $type );

	$colors = wp_review_get_colors( $post_id );

	$color = $colors['color'];
	// don't allow higher rating than max.
	if ( $value > $rating_type['max'] ) {
		$value = $rating_type['max'];
	}
	$template         = $rating_type['output_template'];
	$comment_rating   = true;
	$args['show_one'] = true;
	set_query_var( 'rating', compact( 'value', 'type', 'args', 'comment_rating', 'post_id', 'color', 'colors' ) );
	ob_start();
	load_template( $template, false );
	$review = '<div class="wp-review-usercomment-rating wp-review-usercomment-rating-' . $type . '">' . ob_get_contents() . '</div>';
	ob_end_clean();
	return $review;
}


/**
 * Update user ratings total if comment status is changed.
 *
 * @param string $new_status New status.
 * @param string $old_status Old status.
 * @param object $comment    Comment object.
 */
function wp_review_update_comment_ratings( $new_status, $old_status, $comment ) {
	if ( WP_REVIEW_COMMENT_TYPE_VISITOR === $comment->comment_type ) {
		mts_get_post_reviews( $comment->comment_post_ID, true );
	}
}
add_action( 'transition_comment_status', 'wp_review_update_comment_ratings', 10, 3 );


/**
 * Adds view links in comments list page.
 *
 * @since 3.0.0
 *
 * @param  array $views View links.
 * @return array
 */
function wp_review_add_comments_list_view( $views ) {
	foreach ( $views as $key => $view ) {
		$view          = str_replace( 'comment_type=' . WP_REVIEW_COMMENT_TYPE_COMMENT . '&', '', $view );
		$view          = str_replace( 'comment_type=' . WP_REVIEW_COMMENT_TYPE_VISITOR . '&', '', $view );
		$views[ $key ] = $view;
	}

	// Visitor reviews.
	$url   = add_query_arg( 'comment_type', WP_REVIEW_COMMENT_TYPE_VISITOR );
	$url   = remove_query_arg( 'comment_status', $url );
	$count = get_comments(
		array(
			'count' => true,
			'type'  => WP_REVIEW_COMMENT_TYPE_VISITOR,
		)
	);

	$views['visitor_reviews'] = sprintf(
		'<a href="%1$s" class="%2$s" aria-current="page">%3$s <span class="count">(<span class="all-count">%4$s</span>)</span></a>',
		esc_url( $url ),
		isset( $_GET['comment_type'] ) && WP_REVIEW_COMMENT_TYPE_VISITOR === $_GET['comment_type'] ? 'current' : '',
		esc_html__( 'Visitor reviews', 'wp-review' ),
		$count
	);

	return $views;
}
add_filter( 'views_edit-comments', 'wp_review_add_comments_list_view' );
