<?php

$comments_template = wp_review_option( 'comments_template', 'theme' );
if ( 'theme' != $comments_template ) {
	add_filter( 'comments_template', 'wp_review_comments_template', 99 );
	add_filter( 'comment_form_fields', 'wp_review_move_comment_field_to_bottom' );
	add_filter( 'body_class', 'wp_review_comments_template_body_class' );
	add_filter( 'wp_enqueue_scripts', 'wp_review_comments_template_style' );
}

function wp_review_comments_template( $theme_template ) {
	global $wp_query;
	$wp_query->comments_by_type = array();

	if ( file_exists( get_stylesheet_directory().'/wp-review/comments.php' ) ) {
		return get_stylesheet_directory() . '/wp-review/comments.php';
	}
	return WP_REVIEW_DIR . 'comments/comments.php';
}

function wp_review_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	$cookies_field = $fields['cookies'];
	unset( $fields['comment'] );
	unset( $fields['cookies'] );
	$fields['comment'] = $comment_field;
	$fields['cookies'] = $cookies_field;
	return $fields;
}

function wp_review_comments_template_body_class( $classes ) {

	if ( is_singular() ) {
		$classes[] = 'wp_review_comments_template';
	}

	return $classes;
}

function wp_review_comments_template_style(  ) {
	wp_enqueue_style( 'wp_review_comments', trailingslashit( WP_REVIEW_ASSETS ) . 'css/comments.css', array(), WP_REVIEW_PLUGIN_VERSION, 'all' );
}

function wp_review_override_comments_count() {
	remove_filter( 'get_comments_number', 'mts_comment_count', 0 );
	add_filter( 'get_comments_number', 'wp_review_comment_count', 0 );
}
add_action( 'after_setup_theme', 'wp_review_override_comments_count', 30 );

function wp_review_comment_count( $count ) {
	if ( ! is_admin() ) {
		$comments = get_comments( 'status=approve&post_id=' . get_the_ID() );
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

if ( ! function_exists( 'wp_review_comments' ) ) {
	function wp_review_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;

		//$mts_options = get_option( MTS_THEME_NAME ); ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

			<?php

			switch( $comment->comment_type ) :
				case 'pingback':
				case 'trackback': ?>
					<div id="comment-<?php comment_ID(); ?>">
						<div class="comment-author vcard">
							Pingback: <?php comment_author_link(); ?>
							<?php //if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php //} ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'wp-review' ), '  ', '' ); ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'wp-review' ); ?></em>
							<br />
						<?php endif; ?>
					</div>
					<?php
					break;

				default: ?>
					<div id="comment-<?php comment_ID(); ?>" itemscope itemtype="http://schema.org/UserComments">
						<div class="comment-author vcard">
							<?php echo get_avatar( $comment->comment_author_email, 50 ); ?>
							<?php printf( '<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person"><span itemprop="name">%s</span></span>', get_comment_author_link() ); ?>
							<?php //if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php //} ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'wp-review' ), '  ', '' ); ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'wp-review' ); ?></em>
							<br />
						<?php endif; ?>
						<div class="commentmetadata">
							<div class="commenttext" itemprop="commentText">
								<?php comment_text() ?>
							</div>
							<div class="reply">
								<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
							</div>
						</div>
					</div>
					<?php
					break;
			endswitch; ?>
		<!-- WP adds </li> -->
	<?php
	}
}

$comment_form_integration = wp_review_option( 'comment_form_integration', 'replace' );
if ( 'replace' != $comment_form_integration ) {
	$comment_form_integration = 'extend';
}


// Filter comment fields.
if ( 'replace' == $comment_form_integration ) {
	add_filter( 'comment_form_fields', 'wp_review_comment_fields_replace', 99 );
} else {
	add_action( 'comment_form_logged_in_after', 'wp_review_comment_fields_extend' );
	add_action( 'comment_form_after_fields', 'wp_review_comment_fields_extend' );
}

/**
 * Replace comment Name / Email / Website fields with our own for consistent styling.
 * Also pushes the "comment" field (textarea) to the end of the comment form fields.
 *
 * @since  1.0.0
 * @access public
 * @param  array   $fields
 * @return array
 */
function wp_review_comment_fields_replace( $fields ) {
	global $post;

	$hide_user_reviews = wp_review_network_option('hide_user_reviews_');
	if ( wp_review_has_reviewed( $post->ID, get_current_user_id(), null, WP_REVIEW_COMMENT_TYPE_COMMENT ) || $hide_user_reviews) {
		return $fields;
	}

	// Only touch review fields
	if ( ! is_singular() || ! wp_review_get_post_user_review_type() || ! in_array( wp_review_get_user_rating_setup( $post->ID ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) ) ) {
		return $fields;
	}
	//echo '<!-- '.print_r($fields,1).' -->'; return $fields;

	if ( ! wp_review_user_can_rate_features( $post->ID, 'comment' ) ) {
		$review_rating = wp_review_comment_rating_input();
	} else {
		$review_rating = wp_review_visitor_feature_rating( $post->ID, array( 'type' => 'comment' ) );
	}

	$review_add_fields = array(
		'review_title' => '<div class="wp-review-comment-form-title"><input id="author" type="text" name="wp_review_comment_title" class="wp-review-comment-title-field-input" size="30" id="wp-review-comment-title-field" placeholder="' . esc_attr__( 'Review Title', 'wp-review' ) . '" value="" /></div>',
		'review_rating' => '<div class="wp-review-comment-form-rating wp-review-comment-'.$post->ID.'">' . $review_rating . '</div>',
	);
	$fields = $review_add_fields + $fields; // prepend our new fields.

	if ( isset( $fields['author'] ) ) {
		$fields['author'] = '<p class="wp-review-comment-form-author"><label for="author" class="review-comment-field-msg">'.__('Name', 'wp-review').'</label><input id="author" name="author" type="text" value="" size="30" /></p>';
	}
	if ( isset( $fields['email'] ) ) {
		$fields['email'] = '<p class="wp-review-comment-form-email"><label for="email" class="review-comment-field-msg">'.__('Email', 'wp-review').'</label><input id="email" name="email" type="text"  value="" size="30" /></p>';
	}
	if ( isset( $fields['url'] ) ) {
		$fields['url'] = '<p class="wp-review-comment-form-url"><label for="url" class="review-comment-field-msg">'.__('Website', 'wp-review').'</label><input id="url" name="url" type="text" value="" size="30" /></p>';
	}


	if ( isset( $fields['comment'] ) ) {

		// Grab the comment field.
		$comment_field = $fields['comment'];
		$cookies_field = isset($fields['cookies']) ? $fields['cookies'] : '';

		// Remove the comment field from its current position.
		unset( $fields['comment'] );
		unset( $fields['cookies'] );

		// Put the comment field at the end.
		// Also add title & rating field when user is logged in
		$new_comment_field = '';
		if ( is_user_logged_in() ) {
			foreach ($review_add_fields as $field_name => $field_html) {
				$new_comment_field .= $field_html;
			}
		}
		ob_start();
		?>
		<p class="wp-review-comment-form-comment">
			<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="<?php esc_attr_e( 'Review Text*', 'wp-review' ); ?>"></textarea>
		</p>
		<?php

		if ( function_exists( 'is_product' ) && is_product() ) {
			$comment_image = get_post_meta( $post->ID, 'wp_review_comment_image', true );
			$comment_matches = get_post_meta( $post->ID, 'wp_review_comment_product_desc', true );
			?>
			<?php if ( 'yes' === $comment_matches ) { ?>
				<p class="wp-review-comment-form-qualifier">
					<label><?php echo apply_filters( 'wp_review_comment_qualifier', __( 'Does Product Matches the Description?', 'wp-review' ) ); ?></label>
					<select id="wp_review_comment_qualifier" name="wp_review_comment_qualifier">
						<option value=""><?php echo apply_filters( 'wp_review_comment_qualifier', __( 'Does Product Matches the Description?', 'wp-review' ) ); ?></option>
						<option value="yes"><?php _e( 'Yes', 'wp-review' ); ?></option>
						<option value="no"><?php _e( 'No', 'wp-review' ); ?></option>
					</select>
				</p>
			<?php }
			if ( 'yes' === $comment_image ) { ?>
				<div class="wp-review-comment-form-photo">
					<label><?php _e('Atach a photo', 'wp-review'); ?></label>
					<p class="wp-review-comment-attachment-url wp-review-comment-img-field">
						<a href="#" class="wp-review-toggle-src"><?php _e('Rather attach photo from your computer?', 'wp-review'); ?></a>
						<input id="wp_review_comment_attachment_url" name="wp_review_comment_attachment_url" placeholder="<?php esc_attr_e( 'http://', 'wp-review' ); ?>" />
					</p>
					<p class="wp-review-comment-attachment-source wp-review-comment-img-field hide">
						<a href="#" class="wp-review-toggle-source"><?php _e('Rather attach photo from your another website?', 'wp-review'); ?></a>
						<input type="file" class="input-file" name="" id="wp_review_comment_attachment_src" accept="image/*">
						<input type="hidden" name="wp_review_comment_attachment_src" value="" />
					</p>
				</div>
			<?php }
			}
		$new_comment_field .= ob_get_clean();
		$fields['comment'] = $new_comment_field;
		$fields['cookies'] = $cookies_field;
	}

	return $fields;
}


function wp_review_comment_fields_extend() {
	global $post;

	if ( wp_review_has_reviewed( $post->ID, get_current_user_id(), null, WP_REVIEW_COMMENT_TYPE_COMMENT ) ) {
		return;
	}

	$review_through_comment = in_array( wp_review_get_user_rating_setup( $post->ID ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );

	if ( $review_through_comment ) {
		$type = wp_review_get_post_user_review_type( $post->ID );
		?>
		<div class="wp-review-comment-title-field">
			<label for="wp-review-comment-title-field" class="wp-review-comment-title-field-msg"><?php esc_html_e( 'Review Title', 'wp-review' ); ?></label>
			<span class="wp-review-comment-title-field-input-wrapper">
				<input type="text" name="wp_review_comment_title" class="wp-review-comment-title-field-input" id="wp-review-comment-title-field">
			</span>
		</div>

		<div class="wp-review-comment-field wp-review-comment-rating-<?php echo esc_attr( $type ); ?>-wrapper">
			<div class="wp-review-comment-field-inner" >
				<?php if ( ! wp_review_user_can_rate_features( $post->ID, 'comment' ) ) : ?>
					<?php echo wp_review_comment_rating_input(); ?>
				<?php else : ?>
					<?php echo wp_review_visitor_feature_rating( $post->ID, array( 'type' => 'comment' ) ); ?>
				<?php endif; ?>
			</div>
		</div>

		<?php
		if(function_exists('is_product') && is_product()) {
			$comment_image = apply_filters('wpr_comment_image_field', get_post_meta( $post->ID, 'wp_review_comment_image', true ));
			$comment_matches = apply_filters('wpr_comment_product_desc', get_post_meta( $post->ID, 'wp_review_comment_product_desc', true ));
		?>
			<?php if('yes' === $comment_matches) { ?>
				<p class="wp-review-comment-form-qualifier">
					<label><?php echo apply_filters('wp_review_comment_qualifier', __('Does Product Matches the Description?', 'wp-review')); ?></label>
					<select id="wp_review_comment_qualifier" name="wp_review_comment_qualifier">
						<option value=""><?php _e('Does it match the description?', 'wp-review') ?></option>
						<option value="yes"><?php _e('Yes', 'wp-review') ?></option>
						<option value="no"><?php _e('No', 'wp-review') ?></option>
					</select>
				</p>
			<?php }
			if('yes' === $comment_image) {
			?>
				<div class="wp-review-comment-form-photo">
					<label><?php _e('Atach a photo', 'wp-review'); ?></label>
					<p class="wp-review-comment-attachment-url wp-review-comment-img-field">
						<a href="#" class="wp-review-toggle-src"><?php _e('Rather attach photo from your computer?', 'wp-review'); ?></a>
						<input id="wp_review_comment_attachment_url" name="wp_review_comment_attachment_url" placeholder="<?php esc_attr_e( 'http://', 'wp-review' ); ?>" />
					</p>
					<p class="wp-review-comment-attachment-source wp-review-comment-img-field hide">
						<a href="#" class="wp-review-toggle-source"><?php _e('Rather attach photo from your another website?', 'wp-review'); ?></a>
						<input type="file" class="input-file" name="" id="wp_review_comment_attachment_src" accept="image/*">
						<input type="hidden" name="wp_review_comment_attachment_src" value="" />
					</p>
				</div>
<?php }
		}
	}
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
	add_meta_box( 'wp-review-comment-rating', sprintf(__( 'WP Review Rating (%s)', 'wp-review' ), $wp_review_rating_types[$type]['label']), 'wp_review_comment_meta_box_fields', 'comment', 'normal', 'high' );
}
add_action( 'add_meta_boxes_comment', 'wp_review_comment_add_meta_box' );

function wp_review_comment_meta_box_fields( $comment ) {
	$comment_id = $comment->comment_ID;
	if ( WP_REVIEW_COMMENT_TYPE_COMMENT === get_comment_type( $comment_id ) ) {
		$rating = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, true );
	} else {
		$rating = get_comment_meta( $comment_id, WP_REVIEW_VISITOR_RATING_METAKEY, true );
	}
	$title = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_TITLE_METAKEY, true );
	$rating_items = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
	wp_nonce_field( 'wp_review_comment_rating_update', 'wp_review_comment_rating_update', false );
	?>
	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_comment_title"><?php esc_html_e( 'Review Title', 'wp-review' ); ?></label>
		</div>
		<div class="wp-review-field-option">
			<input type="text" name="wp_review_comment_title" value="<?php echo esc_attr( $title ); ?>" id="wp_review_comment_title">
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_comment_rating"><?php esc_html_e( 'Review total', 'wp-review' ); ?></label>
		</div>
		<div class="wp-review-field-option">
			<input type="text" class="small-text" name="wp_review_comment_rating" value="<?php echo esc_attr( $rating ); ?>" id="wp_review_comment_rating">
		</div>
	</div>

	<?php if ( ! empty( $rating_items ) ) :
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
	$comment_qualifier = get_comment_meta( $comment_id, 'wp_review_comment_qualifier', true );
	$comment_image = get_comment_meta( $comment_id, 'wp_review_comment_attachment_url', true );
	$comment_image_name = 'wp_review_comment_attachment_url';
	if(!$comment_image) {
		$comment_image = get_comment_meta( $comment_id, 'wp_review_comment_attachment_src', true );
		if($comment_image) {
			$comment_image_name = 'wp_review_comment_attachment_src';
		}
	}

	if($comment_qualifier) {
		?>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_comment_qualifier"><?php echo apply_filters('wp_review_comment_qualifier', __('Does Product Matches the Description?', 'wp-review')); ?></label>
			</div>
			<div class="wp-review-field-option">
				<select id="wp_review_comment_qualifier" name="wp_review_comment_qualifier">
					<option value=""><?php _e('Select', 'wp-review'); ?></option>
					<option value="yes" <?php selected($comment_qualifier, 'yes', true); ?>><?php _e('Yes', 'wp-review'); ?></option>
					<option value="no" <?php selected($comment_qualifier, 'no', true); ?>><?php _e('No', 'wp-review'); ?></option>
				</select>
			</div>
		</div>
	<?php }
	if($comment_image) {
		if(is_numeric($comment_image)) {
			$comment_image = wp_get_attachment_url($comment_image);
		}
		?>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_comment_image"><?php esc_html_e( 'Comment Image', 'wp-review' ); ?></label>
			</div>
			<div class="wp-review-field-option">
				<input type="text" name="<?php echo esc_attr($comment_image_name) ?>" value="<?php echo esc_attr($comment_image); ?>" />
			</div>
		</div>
	<?php } ?>
	<style>.wp-review-field { margin-bottom: 1em; }</style>
	<?php
}

/**
 * Save our comment (from the admin area)
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

	// if ( ! empty( $rating ) ) {
		$comment = get_comment( $comment_id );
		update_comment_meta( $comment_id, $meta_key, $rating );
		wp_review_clear_cached_reviews( $comment );
	// }

	if ( ! empty( $_POST['wp_review_comment_title'] ) ) {
		$title = sanitize_text_field( wp_unslash( $_POST['wp_review_comment_title'] ) );
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_TITLE_METAKEY, $title );
	}

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
 * Save our title & rating (from the front end).
 *
 * @param int  $comment_id       Comment ID.
 * @param bool $comment_approved Comment approved or not.
 */
function wp_review_comment_insert_comment( $comment_id, $comment_approved ) {

	$rating = '';
	$comment = get_comment( $comment_id );
	$type = wp_review_get_post_user_review_type( $comment->comment_post_ID );
	$rating_type = wp_review_get_rating_type_data( $type );
	if ( ! $rating_type ) {
		return;
	}

	if ( isset( $_POST['wp-review-user-rating-val'] ) ) {

		$rating = filter_input( INPUT_POST, 'wp-review-user-rating-val', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		if ( $rating > $rating_type['max'] ) {
			$rating = $rating_type['max'];
		}
		if ( $rating < 0 ) {
			$rating = 0;
		}
	}

	if ( '' !== $rating ) {
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, $rating );
	}

	if ( ! empty( $_POST['wp_review_comment_title'] ) ) {
		$title = sanitize_text_field( $_POST['wp_review_comment_title'] );
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_TITLE_METAKEY, $title );
	}

	if ( ! empty( $_POST['wp-review-comment-feature-rating'] ) ) {
		$rating = json_decode( wp_unslash( $_POST['wp-review-comment-feature-rating'] ), true );
		$total = 0;
		foreach ( $rating as $key => $value ) {
			if ( $value < 0 ) {
				$value = 0;
			}
			$total += $value;
			$rating[ $key ] = $value;
		}
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, $rating );
		update_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, $total / count( $rating ) );
	}

	if ( ! empty( $_POST['wp_review_comment_pros'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_pros', wp_kses_post( wp_unslash( $_POST['wp_review_comment_pros'] ) ) );
	}
	if ( ! empty( $_POST['wp_review_comment_cons'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_cons', wp_kses_post( wp_unslash( $_POST['wp_review_comment_cons'] ) ) );
	}
	if ( ! empty( $_POST['wp_review_comment_qualifier'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_qualifier', wp_kses_post( wp_unslash( $_POST['wp_review_comment_qualifier'] ) ) );
	}

	if ( ! empty( $_POST['wp_review_comment_attachment_url'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_attachment_url', wp_kses_post( wp_unslash( $_POST['wp_review_comment_attachment_url'] ) ) );
	}

	if ( ! empty( $_POST['wp_review_comment_attachment_src'] ) ) {
		update_comment_meta( $comment_id, 'wp_review_comment_attachment_src', wp_kses_post( wp_unslash( $_POST['wp_review_comment_attachment_src'] ) ) );
	}

	if ( '' !== $rating && 1 === $comment_approved ) {
		wp_review_clear_cached_reviews( $comment );
	}
}
add_action( 'comment_post', 'wp_review_comment_insert_comment', 10, 2 );

/**
 * Add our rating and title to the comment text.
 *
 * @param string     $text    Comment text.
 * @param WP_Comment $comment Comment object.
 * @return string
 */
function wp_review_comment_add_title_to_text( $text, $comment = null ) {
	if ( null === $comment || ! in_array( $comment->comment_type, array( WP_REVIEW_COMMENT_TYPE_COMMENT, WP_REVIEW_COMMENT_TYPE_VISITOR ) ) ) {
		return $text;
	}
	if ( is_admin() ) {
		$comment_id = $comment->comment_ID;
		$title = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_TITLE_METAKEY, true );
		$title_html = '';
		if ( $title ) {
			$title_html = '<h4 class="wp-review-comment-title">' . $title . '</h4>';
		}

		$rating_html = '';
		$type = get_comment_type( $comment_id );
		$rating = '';
		if ( WP_REVIEW_COMMENT_TYPE_COMMENT === $type ) {
			$rating = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, true );
		} else if ( WP_REVIEW_COMMENT_TYPE_VISITOR === $type ) {
			$rating = get_comment_meta( $comment_id, WP_REVIEW_VISITOR_RATING_METAKEY, true );
			$text = ''; // Don't show text for Visitor Ratings.
		}
		if ( '' !== $rating ) {
			$rating_html = wp_review_comment_rating( $rating, $comment_id );
		}

		$rating_data = wp_review_get_comment_feature_rating_data( $comment );
		$rating_data = array_values( $rating_data );
		$text .= '<div id="inline-commentreview-' . $comment_id . '" class="hidden">';
		$text .= '<input type="hidden" class="comment-review-title" value="' . esc_attr( $title ) . '">';
		$text .= '<input type="hidden" class="comment-review-rating" value="' . esc_attr( $rating ) . '">';
		$text .= '<input type="hidden" class="comment-review-type" value="' . esc_attr( get_comment_type( $comment_id ) ) . '">';
		if ( $rating_data ) {
			$text .= '<input type="hidden" class="comment-review-feature-rating" value="' . esc_attr( wp_json_encode( $rating_data ) ) . '">';
		}
		$text .= '</div>';

		return $title_html . $rating_html . $text;
	}

	$title = '';
	$review = '';
	$feedback = '';
	$title_meta = get_comment_meta( $comment->comment_ID, WP_REVIEW_COMMENT_TITLE_METAKEY, true );
	if ( $title_meta ) {
		$title = '<h4 class="wp-review-comment-title">' . $title_meta . '</h4>';
	}

	$comment_id = $comment->comment_ID;
	$rating = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_RATING_METAKEY, true );
	$rating_items = get_comment_meta( $comment_id, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );

	if ( '' !== $rating ) {
		echo wp_review_verified_comment($comment->comment_author_email);

		if ( ! $rating_items || ! is_array( $rating_items ) ) {
			$review .= wp_review_comment_rating( $rating );
		} else {
			$review .= wp_review_comment_rating( $rating, $comment_id );
		}

		// Comment Qualifier.
		$comment_qualifier = get_comment_meta( $comment_id, 'wp_review_comment_qualifier', true );
		if ( $comment_qualifier ) {
			$review .= '<div class="wp-review-comment-qualifier"><strong>' . apply_filters( 'wp_review_comment_qualifier', __( 'Does Product Matches the Description?', 'wp-review' ) ) . '</strong> ' . ucwords( $comment_qualifier ) . '</div>';
		}

		// Comment Image.
		$comment_image = get_comment_meta( $comment_id, 'wp_review_comment_attachment_url', true );
		if ( ! $comment_image ) {
			$comment_image_id = get_comment_meta( $comment_id, 'wp_review_comment_attachment_src', true );
			if ( $comment_image_id ) {
				$comment_image = wp_get_attachment_url( $comment_image_id );
			}
		}
		if ( $comment_image ) {
			$review .= '<div class="wp-review-usercomment-image"><img src="' . esc_url( $comment_image ) . '" /></div>';
		}
	}

	return $title . $review . '<div class="comment-text-inner">' . $text . '</div>' . $feedback;
}
add_filter( 'comment_text', 'wp_review_comment_add_title_to_text', 99, 2 );

/**
 * Add rating and title to the comment quick edit
 */
function wp_review_comment_reply_filter( $output, $args ) {
	$table_row = true;
	global $wp_list_table;
	if ( ! $wp_list_table ) {
		if ( 'single' === $args['mode'] ) {
			$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
		} else {
			$wp_list_table = _get_list_table('WP_Comments_List_Table');
		}
	}
	ob_start();
	?>
	<form method="get">
		<?php if ( $table_row ) : ?>
			<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" class="inline-edit-row" style="display:none;"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
		<?php else : ?>
			<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">
		<?php endif; ?>

			<fieldset class="comment-reply">
				<legend>
					<span class="hidden" id="editlegend"><?php esc_html_e( 'Edit Comment', 'wp-review' ); ?></span>
					<span class="hidden" id="replyhead"><?php esc_html_e( 'Reply to Comment', 'wp-review' ); ?></span>
					<span class="hidden" id="addhead"><?php esc_html_e( 'Add new Comment', 'wp-review' ); ?></span>
				</legend>

				<div id="editwpreview"></div>

				<div id="replycontainer">
					<label for="replycontent" class="screen-reader-text"><?php esc_html_e( 'Comment', 'wp-review' ); ?></label>
					<?php
					$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
					wp_editor( '', 'replycontent', array(
						'media_buttons' => false,
						'tinymce' => false,
						'quicktags' => $quicktags_settings,
					) );
					?>
				</div>

				<div id="edithead" style="display:none;">
					<div class="inside">
						<label for="author-name"><?php esc_html_e( 'Name', 'wp-review' ); ?></label>
						<input type="text" name="newcomment_author" size="50" value="" id="author-name" />
					</div>

					<div class="inside">
						<label for="author-email"><?php esc_html_e( 'Email', 'wp-review' ); ?></label>
						<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
					</div>

					<div class="inside">
						<label for="author-url"><?php esc_html_e( 'URL', 'wp-review' ); ?></label>
						<input type="text" id="author-url" name="newcomment_author_url" class="code" size="103" value="" />
					</div>
				</div>

				<p id="replysubmit" class="submit">
					<a href="#comments-form" class="save button-primary alignright">
						<span id="addbtn" style="display:none;"><?php esc_html_e( 'Add Comment', 'wp-review' ); ?></span>
						<span id="savebtn" style="display:none;"><?php esc_html_e( 'Update Comment', 'wp-review' ); ?></span>
						<span id="replybtn" style="display:none;"><?php esc_html_e( 'Submit Reply', 'wp-review' ); ?></span>
					</a>
					<a href="#comments-form" class="cancel button-secondary alignleft"><?php esc_html_e( 'Cancel', 'wp-review' ); ?></a>
					<span class="waiting spinner"></span>
					<span class="error" style="display:none;"></span>
				</p>

				<input type="hidden" name="action" id="action" value="" />
				<input type="hidden" name="comment_ID" id="comment_ID" value="" />
				<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
				<input type="hidden" name="status" id="status" value="" />
				<input type="hidden" name="position" id="position" value="<?php echo esc_attr( $args['position'] ); ?>" />
				<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $args['checkbox'] ? 1 : 0; ?>" />
				<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr( $args['mode'] ); ?>" />
				<?php
				wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false );
				if ( current_user_can( 'unfiltered_html' ) ) {
					wp_nonce_field( 'unfiltered-html-comment', '_wp_unfiltered_html_comment', false );
				}

				wp_nonce_field( 'wp_review_comment_rating_update', 'wp_review_comment_rating_update', false );

				?>
			</fieldset>

		<?php if ( $table_row ) : ?>
			</td></tr></tbody></table>
		<?php else : ?>
			</div></div>
		<?php endif; ?>
	</form>
	<?php

	return ob_get_clean();
}
add_filter( 'wp_comment_reply', 'wp_review_comment_reply_filter', 10, 2 );

/**
 * Script for Comments quick edit
 */
function wp_review_comment_quick_edit_javascript() {
	?>
	<script type="text/html" id="tmpl-wpr-comment-review-quick-edit">
		<# if ( 'wp_review_comment' === data.type ) { #>
			<div class="inside">
				<label for="wp_review_comment_title"><?php esc_html_e( 'Review Title', 'wp-review' ); ?></label>
				<input type="text" name="wp_review_comment_title" size="50" value="{{ data.title }}" id="wp_review_comment_title">
			</div>
		<# } #>

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

		<# if ( data.showProsCons ) { #>
			<div style="clear: both;"></div>
			<div class="inside">
				<label for="wp_review_comment_pros"><?php esc_html_e( 'Pros', 'wp-review' ); ?></label>
				<textarea id="wp_review_comment_pros" class="widefat" oninput="this.nextElementSibling.setAttribute( 'value', this.value )">{{ data.pros }}</textarea>
				<input type="hidden" name="wp_review_comment_pros" value="{{ data.pros }}">
			</div>
			<div style="clear: both;"></div>
			<div class="inside">
				<label for="wp_review_comment_cons"><?php esc_html_e( 'Cons', 'wp-review' ); ?></label>
				<textarea id="wp_review_comment_cons" class="widefat" oninput="this.nextElementSibling.setAttribute( 'value', this.value )">{{ data.cons }}</textarea>
				<input type="hidden" name="wp_review_comment_cons" value="{{ data.cons }}">
			</div>
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
				showProsCons: parseInt( jQuery( '.comment-review-show-pros-cons', rowData ).val() ),
				pros: jQuery( '.comment-review-pros', rowData ).val(),
				cons: jQuery( '.comment-review-cons', rowData ).val()
			};

			jQuery( '#editwpreview', editRow ).html( tmpl( tmplData ) );
		}
	</script>
	<?php
}
add_action( 'admin_footer-edit-comments.php', 'wp_review_comment_quick_edit_javascript' );

function wp_review_comment_quick_edit_action($actions, $comment ) {
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

function wp_review_preprocess_comment( $commentdata ) {
	$options = get_option( 'wp_review_options' );
	$review_through_comment = in_array( wp_review_get_user_rating_setup( $commentdata['comment_post_ID'] ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );

	$rating = filter_input( INPUT_POST, 'wp-review-user-rating-val', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

	if ( ! empty( $rating ) ) {
		$commentdata['comment_type'] = WP_REVIEW_COMMENT_TYPE_COMMENT;
	} elseif ( ( ! empty( $options['require_rating'] ) && $review_through_comment && ! is_admin() ) ) {
		wp_die( esc_html__( 'A rating is required! Hit the back button to add your rating.', 'wp-review' ) );
	}

	return $commentdata;
}
add_action( 'preprocess_comment', 'wp_review_preprocess_comment' );

/**
 * Replace "Comment" with "Review" in submit field
 *
 */
function wp_review_change_submit_comment( $field ) {
	global $post;
	$review_through_comment = in_array( wp_review_get_user_rating_setup( $post->ID ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );
	if ( $review_through_comment ) {
		$field = str_replace( __( 'Comment', 'wp-review' ), __( 'Review', 'wp-review' ), $field );
	}
	return $field;
}
add_filter( 'comment_form_submit_field', 'wp_review_change_submit_comment', 10 );


/**
 * Show 'all' comment types instead of only 'comment' type in MTS themes.
 */
function wp_review_list_comments_args( $args ) {
	if ( is_admin() ) {
		return $args;
	}

	global $post;
	$review_through_comment = in_array( wp_review_get_user_rating_setup( $post->ID ), array( WP_REVIEW_REVIEW_COMMENT_ONLY, WP_REVIEW_REVIEW_ALLOW_BOTH ) );
	if ( ! $review_through_comment ) {
		return $args;
	}

	if ( 'comment' === $args['type'] && apply_filters( 'wp_review_to_comment_type_list', true ) ) {
		$args['type'] = 'all';
	}
	return $args;
}
add_filter( 'wp_list_comments_args', 'wp_review_list_comments_args' );


function wp_review_comment_rating_input( $args = array() ) {
	global $post, $wp_review_rating_types;
	$type = wp_review_get_post_user_review_type( $post->ID );
	$rating_type_template = $wp_review_rating_types[ $type ]['input_template'];
	$post_id = $post->ID;
	$value = 0;
	$comment_rating = true;

	$colors = wp_review_get_colors( $post_id );
	$color = $colors['color'];

	set_query_var( 'rating', compact( 'value', 'post_id', 'comment_rating', 'args', 'color', 'colors' ) );
	ob_start();
	load_template( $rating_type_template, false );
	$review = '<div class="wp-review-comment-rating wp-review-comment-rating-' . $type . '">' . ob_get_contents() . '</div>';
	ob_end_clean();

	return $review;
}


/**
 * Gets comment rating items output.
 *
 * @since 3.0.0
 *
 * @param  array      $rating_items   Rating items.
 * @param  WP_Comment $comment Comment object.
 * @return string
 */
function wp_review_comment_rating_items( $rating_items, $comment ) {
	$post_id = $comment->comment_post_ID;
	$type = wp_review_get_post_user_review_type( $post_id );
	if ( empty( $type ) ) {
		return '';
	}
	$rating_type = wp_review_get_rating_type_data( $type );
	$items = wp_review_get_review_items( $post_id );

	$colors = wp_review_get_colors( $post_id );
	$color = $colors['color'];

	$template = $rating_type['output_template'];
	$args['show_one'] = true;
	// $comment_rating = true;

	$output = '';

	foreach ( $items as $item_id => $item ) {
		if ( empty( $rating_items[ $item_id ] ) ) {
			$value = 0;
		} else {
			$value = $rating_items[ $item_id ];
		}

		if ( ! empty( $items[ $item_id ]['wp_review_item_color'] ) ) {
			$color = $items[ $item_id ]['wp_review_item_color'];
			$colors['color'] = $color;
		}

		if ( ! empty( $items[ $item_id ]['wp_review_item_inactive_color'] ) ) {
			$inactive_color = $items[ $item_id ]['wp_review_item_inactive_color'];
			$colors['inactive_color'] = $inactive_color;
		}

		// don't allow higher rating than max.
		if ( $value > $rating_type['max'] ) {
			$value = $rating_type['max'];
		}

		$value_text = '';
		if ( 'star' != $type ) {
			$value_text = ' - <span>' . sprintf( $rating_type['value_text'], $value ) . '</span>';
		}

		$circle_width = '';
		$circle_height = '';
		if ( is_admin() ) {
			$circle_width = 48;
			$circle_height = 48;
			$circle_display_input = false;
		}

		set_query_var( 'rating', compact( 'value', 'type', 'args', 'comment_rating', 'post_id', 'color', 'colors', 'circle_width', 'circle_height', 'circle_display_input' ) );
		$output .= '<li>';
		ob_start();
		load_template( $template, false );
		$output .= ob_get_clean();
		if ( 'thumbs' !== $type ) {
			$output .= '<span>' . wp_kses_post( $item['wp_review_item_title'] ) . $value_text . '</span>';
		} else {
			$output .= '<span>' . wp_kses_post( $item['wp_review_item_title'] ) . '</span>';
		}
		$output .= '</li>';
	}
	return '<div class="wpr-user-features-rating 115 wp-review-usercomment-rating-' . $type . '"><ul class="features-rating-list review-list">' . $output . '</ul></div>';
}



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

	$circle_width = '';
	$circle_height = '';

	// Override if is_admin().
	if ( is_admin() ) {
		$circle_width = 48;
		$circle_height = 48;
	}
	$color = $colors['color'];
	// don't allow higher rating than max.
	if ( $value > $rating_type['max'] ) {
		$value = $rating_type['max'];
	}
	$template = $rating_type['output_template'];
	$comment_rating = true;
	$args['show_one'] = true;
	set_query_var( 'rating', compact( 'value', 'type', 'args', 'comment_rating', 'post_id', 'color', 'colors', 'circle_width', 'circle_height' ) );
	ob_start();
	load_template( $template, false );
	$review = '<div class="wp-review-usercomment-rating wp-review-usercomment-rating-' . $type . '">' . ob_get_contents() . '</div>';
	ob_end_clean();
	return $review;
}


/**
 * Gets comment feature rating text only.
 *
 * @param WP_Comment|int $comment Comment object or comment ID.
 * @return string
 * @since 3.0.0
 */
function wp_review_get_comment_feature_rating_text_only( $comment ) {
	$comment = get_comment( $comment );
	$rating_items = get_comment_meta( $comment->comment_ID, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
	if ( ! $rating_items || ! is_array( $rating_items ) ) {
		return '';
	}

	$items = wp_review_get_review_items( $comment->comment_post_ID );
	if ( ! $items ) {
		return '';
	}

	$items = wp_list_pluck( $items, 'wp_review_item_title' );
	$output = '<ul>';
	foreach ( $rating_items as $key => $value ) {
		if ( ! isset( $items[ $key ] ) ) {
			continue;
		}
		$output .= sprintf( '<li>%1$s - %2$s</li>', esc_html( $items[ $key ] ), floatval( $rating_items[ $key ] ) );
	}
	$output .= '</ul>';
	return $output;
}


// Keep "comment" class in 'wp_review_comment' comment type
function wp_review_comment_type_classes( $classes, $class, $comment_ID, $comment, $post_id ) {
	if ( WP_REVIEW_COMMENT_TYPE_COMMENT === $comment->comment_type ) {
		$classes[] = 'comment';
	}/* elseif ( wp_review_has_reviewed( $comment->comment_post_ID, WP_REVIEW_COMMENT_TYPE_COMMENT ) ) {
		$classes[] = 'wp_review_comment';
	}*/
	return $classes;
}
add_filter( 'comment_class', 'wp_review_comment_type_classes', 10, 6 );

// Enable avatar for 'wp_review_comment' comment type
function wp_review_comment_type_avatar( $types ) {
	$types[] = 'wp_review_comment';
	return $types;
}
add_filter( 'get_avatar_comment_types', 'wp_review_comment_type_avatar' );

// Update user ratings total if comment status is changed
function wp_review_update_comment_ratings( $new_status, $old_status, $comment ) {
	if ( WP_REVIEW_COMMENT_TYPE_COMMENT === $comment->comment_type ) {
		mts_get_post_comments_reviews( $comment->comment_post_ID, true );
	}
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
		$view = str_replace( 'comment_type=' . WP_REVIEW_COMMENT_TYPE_COMMENT . '&', '', $view );
		$view = str_replace( 'comment_type=' . WP_REVIEW_COMMENT_TYPE_VISITOR . '&', '', $view );
		$views[ $key ] = $view;
	}

	// Comment reviews.
	$url = add_query_arg( 'comment_type', WP_REVIEW_COMMENT_TYPE_COMMENT );
	$url = remove_query_arg( 'comment_status', $url );
	$count = get_comments( array(
		'count' => true,
		'type'  => WP_REVIEW_COMMENT_TYPE_COMMENT,
	) );
	$views['comment_reviews'] = sprintf(
		'<a href="%1$s" class="%2$s" aria-current="page">%3$s <span class="count">(<span class="all-count">%4$s</span>)</span></a>',
		esc_url( $url ),
		isset( $_GET['comment_type'] ) && WP_REVIEW_COMMENT_TYPE_COMMENT === $_GET['comment_type'] ? 'current' : '',
		esc_html__( 'Comment reviews', 'wp-review' ),
		$count
	);

	// Visitor reviews.
	$url = add_query_arg( 'comment_type', WP_REVIEW_COMMENT_TYPE_VISITOR );
	$url = remove_query_arg( 'comment_status', $url );
	$count = get_comments( array(
		'count' => true,
		'type'  => WP_REVIEW_COMMENT_TYPE_VISITOR,
	) );
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


/**
 * Gets comment feature rating data.
 *
 * @param WP_Comment|int $comment Comment object or comment ID.
 * @return array
 * @since 3.0.0
 */
function wp_review_get_comment_feature_rating_data( $comment ) {
	$comment = get_comment( $comment );
	$rating_items = get_comment_meta( $comment->comment_ID, WP_REVIEW_COMMENT_FEATURES_RATING_METAKEY, true );
	if ( ! $rating_items || ! is_array( $rating_items ) ) {
		return array();
	}

	$items = wp_review_get_review_items( $comment->comment_post_ID );
	if ( ! $items ) {
		return array();
	}

	foreach ( $items as $key => &$item ) {
		$item['id'] = $key;
		$item['comment_rating'] = isset( $rating_items[ $key ] ) ? floatval( $rating_items[ $key ] ) : false;
	}
	return $items;
}

function wp_review_verified_comment($email) {
	if ( class_exists( 'WooCommerce' ) ) {
		global $product;
		$show_verified_product = apply_filters('wpr_show_verified_label', true);
		if(!empty($product) && wc_customer_bought_product( $email, '', $product->get_id() ) && $show_verified_product) {
			return '<span class="is_verified">'.apply_filters('wp_review_verified_label', __('Verified Purchase', 'wp-review')).'</span>';
		}
	}
	return '';
}
