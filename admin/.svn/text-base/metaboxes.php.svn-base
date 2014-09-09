<?php
/**
 * File for registering meta box.
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Adds a box to the Posts edit screens. */
add_action( 'add_meta_boxes', 'wp_review_add_meta_boxes' );

/* Saves the meta box custom data. */
add_action( 'save_post', 'wp_review_save_postdata', 10, 2 );

/**
 * Adds a box to the Post edit screens.
 *
 * @since 1.0
 */
function wp_review_add_meta_boxes() {
    $post_types = get_post_types( array('public' => true), 'names' );
    $excluded_post_types = apply_filters('wp_review_excluded_post_types', array('attachment'));
    
    foreach ($post_types as $post_type) {
        if (!in_array($post_type, $excluded_post_types)) {
        	add_meta_box(
        		'wp-review-metabox-review',
        		__( 'Review', 'wp-review' ),
        		'wp_review_render_meta_box_review_options',
        		$post_type,
        		'normal',
        		'high'
        	);
        
        	add_meta_box(
        		'wp-review-metabox-item',
        		__( 'Review Item', 'wp-review' ),
        		'wp_review_render_meta_box_item',
        		$post_type,
        		'normal',
        		'high'
        	);
        	
        	add_meta_box(
        		'wp-review-metabox-heading',
        		__( 'Review Heading', 'wp-review' ),
        		'wp_review_render_meta_box_heading',
        		$post_type,
        		'normal',
        		'high'
        	);
        	
        	add_meta_box(
        		'wp-review-metabox-desc',
        		__( 'Review Description', 'wp-review' ),
        		'wp_review_render_meta_box_desc',
        		$post_type,
        		'normal',
        		'high'
        	);
        	
        	add_meta_box(
        		'wp-review-metabox-userReview',
        		__( 'User Reviews', 'wp-review' ),
        		'wp_review_render_meta_box_userReview',
        		$post_type,
        		'normal',
        		'high'
        	);
        }
    }
}

/**
 * Render the meta box.
 *
 * @since 1.0
 */
function wp_review_render_meta_box_review_options( $post ) {
	global $post;

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-review-options-nonce' );

	/* Retrieve an existing value from the database. */
	$type = get_post_meta( $post->ID, 'wp_review_type', true );
    
    $available_types = apply_filters('wp_review_metabox_types', array('star' => __('Star', 'wp-review'), 'point' => __('Point', 'wp-review'), 'percentage' => __('Percentage', 'wp-review')));
?>
	
	<p class="wp-review-field">
		<label for="wp_review_type"><?php _e( 'Review Type', 'wp-review' ); ?></label>
		<select name="wp_review_type" id="wp_review_type">
			<option value=""><?php _e( 'No Review', 'wp-review' ) ?></option>
            <?php foreach ($available_types as $available_type => $label) { ?>
                <option value="<?php echo $available_type; ?>" <?php selected( $type, $available_type ); ?>><?php echo $label; ?></option>
            <?php } ?>
		</select>
        <span id="wp_review_id_hint">Review ID: <strong><?php echo $post->ID; ?></strong></span>
	</p>

	<?php
}

/**
 * Render the meta box.
 *
 * @since 1.0
 */
function wp_review_render_meta_box_item( $post ) {
	$options = get_option('wp_review_options');
    $defaultColors = apply_filters('wp_review_default_colors', array(
    	'color' => '#1e73be',
    	'fontcolor' => '#555555',
    	'bgcolor1' => '#e7e7e7',
    	'bgcolor2' => '#ffffff',
    	'bordercolor' => '#e7e7e7'
    ));
    $defaultLocation = apply_filters('wp_review_default_location', 'bottom');
    
    $defaultCriteria = apply_filters('wp_review_default_criteria', array());
    $defaultItems = array();
    if (empty($defaultCriteria) && ! empty($options['default_features'])) $defaultCriteria = $options['default_features'];
    foreach ($defaultCriteria as $item) {
        $defaultItems[] = array( 'wp_review_item_title' => $item, 'wp_review_item_star' => '');
    }
    
	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-item-nonce' ); 

	/* Retrieve an existing value from the database. */
	$custom_colors   = get_post_meta( $post->ID, 'wp_review_custom_colors', true );
	$custom_location = get_post_meta( $post->ID, 'wp_review_custom_location', true );

	$items     = get_post_meta( $post->ID, 'wp_review_item', true ); 
	$color     = get_post_meta( $post->ID, 'wp_review_color', true );
	$location  = get_post_meta( $post->ID, 'wp_review_location', true );
	$fontcolor = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
	$bgcolor1  = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
	$bgcolor2  = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
	$bordercolor  = get_post_meta( $post->ID, 'wp_review_bordercolor', true );
    if ( $items == '' ) $items = $defaultItems;
	if( $color == '' ) $color = ( ! empty($options['colors']['color'] ) ? $options['colors']['color'] : $defaultColors['color']);
    if( $location == '' ) $location = ( ! empty($options['location'] ) ? $options['location'] : $defaultLocation);
	if( $fontcolor == '' ) $fontcolor = ( ! empty($options['colors']['fontcolor'] ) ? $options['colors']['fontcolor'] : $defaultColors['fontcolor']);
	if( $bgcolor1 == '' ) $bgcolor1 = ( ! empty($options['colors']['bgcolor1'] ) ? $options['colors']['bgcolor1'] : $defaultColors['bgcolor1']);
	if( $bgcolor2 == '' ) $bgcolor2 = ( ! empty($options['colors']['bgcolor2'] ) ? $options['colors']['bgcolor2'] : $defaultColors['bgcolor2']);
	if( $bordercolor == '' ) $bordercolor = ( ! empty($options['colors']['bordercolor'] ) ? $options['colors']['bordercolor'] : $defaultColors['bordercolor']);

    $fields = array(
        'location' => true, 
        'color' => true, 
        'fontcolor' => true, 
        'bgcolor1' => true, 
        'bgcolor2' => true, 
        'bordercolor' => true,
        'custom_colors' => true,
        'custom_location' => true,
    );
    $displayed_fields = apply_filters('wp_review_metabox_item_fields', $fields);
?>

	<!-- Start repeater field -->
	<table id="wp-review-item" class="wp-review-item" width="100%">

		<thead>
			<tr>
				<th width="80%"><?php _e( 'Feature Name', 'wp-review' ); ?></th>
				<th width="10%" class="dynamic-text"><?php _e( 'Star (1-5)', 'wp-review' ); ?></th>
				<th width="10%"></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( !empty($items) ) : ?>
		 
				<?php foreach ( $items as $item ) { ?>

					<tr>
						<td>
							<input type="text" class="widefat" name="wp_review_item_title[]" value="<?php if( !empty( $item['wp_review_item_title'] ) ) echo esc_attr( $item['wp_review_item_title'] ); ?>" />
						</td>
						<td>
							<input type="text" min="1" step="1" autocomplete="off" class="widefat review-star" name="wp_review_item_star[]" value="<?php if ( !empty ($item['wp_review_item_star'] ) ) echo $item['wp_review_item_star']; ?>" />
						</td>
						<td><a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a></td>
					</tr>

				<?php } ?>

			<?php else : ?>
				
				<tr>
					<td><input type="text" class="widefat" name="wp_review_item_title[]" /></td>
					<td><input type="text" min="1" step="1" autocomplete="off" class="widefat review-star" name="wp_review_item_star[]" /></td>
					<td><a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a></td>
				</tr>

			<?php endif; ?>
		 
			<!-- empty hidden one for jQuery -->
			<tr class="empty-row screen-reader-text">
				<td><input type="text" class="widefat" name="wp_review_item_title[]" /></td>
				<td><input type="text" min="1" step="1" autocomplete="off" class="widefat" name="wp_review_item_star[]" /></td>
				<td><a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a></td>
			</tr>

		</tbody>

	</table>
	
	<table width="100%">
		<tr>
			<td width="80%"><a id="add-row" class="button" href="#"><?php _e( 'Add another', 'wp-review' ) ?></a></td>
			<td width="10%">
				<input type="text" class="widefat wp-review-total" name="wp_review_total" value="<?php echo get_post_meta( $post->ID, 'wp_review_total', true ); ?>" />
			</td>
			<td width="10%"><?php _e( 'Total', 'wp-review' ); ?></td>
		</tr>
	</table>

	<p class="wp-review-field">
		<label for="wp_review_custom_location"><?php _e( 'Custom Location', 'wp-review' ); ?></label>
		<input type="hidden" name="wp_review_custom_location" id="wp_review_custom_location_unchecked" value="" />
		<input name="wp_review_custom_location" id="wp_review_custom_location" type="checkbox" value="1" <?php echo (! empty($custom_location) ? 'checked ' : ''); ?> />
	</p>
    <div class="wp-review-location-options"<?php if (empty($custom_location)) echo ' style="display: none;"'; ?>>
		<p class="wp-review-field">
			<label for="wp_review_location"><?php _e( 'Review Location', 'wp-review' ); ?></label>
			<select name="wp_review_location" id="wp_review_location">
				<option value="bottom" <?php selected( $location, 'bottom' ); ?>><?php _e( 'After Content', 'wp-review' ) ?></option>
				<option value="top" <?php selected( $location, 'top' ); ?>><?php _e( 'Before Content', 'wp-review' ) ?></option>
	            <option value="custom" <?php selected( $location, 'custom' ); ?>><?php _e( 'Custom (use shortcode)', 'wp-review' ) ?></option>
			</select>
		</p>
		<p class="wp-review-field" id="wp_review_shortcode_hint_field">
			<label for="wp_review_shortcode_hint"></label>
			<input id="wp_review_shortcode_hint" type="text" value="[wp-review]" readonly="readonly" />
	        <span><?php _e('Copy &amp; paste this shortcode in the content.', 'wp-review') ?></span>
		</p>
	</div>
	<p class="wp-review-field">
		<label for="wp_review_custom_colors"><?php _e( 'Custom Colors', 'wp-review' ); ?></label>
		<input type="hidden" name="wp_review_custom_colors" id="wp_review_custom_colors_unchecked" value="" />
		<input name="wp_review_custom_colors" id="wp_review_custom_colors" type="checkbox" value="1" <?php echo (! empty($custom_colors) ? 'checked ' : ''); ?>/>
	</p>
    <div class="wp-review-color-options"<?php if (empty($custom_colors)) echo ' style="display: none;"'; ?>>

		<p class="wp-review-field"<?php if (empty($displayed_fields['color'])) echo ' style="display: none;"'; ?>>
			<label for="wp_review_color"><?php _e( 'Review Color', 'wp-review' ); ?></label>
			<input type="text" class="wp-review-color" name="wp_review_color" value="<?php echo $color; ?>" />
		</p>

		<p class="wp-review-field"<?php if (empty($displayed_fields['fontcolor'])) echo ' style="display: none;"'; ?>>
			<label for="wp_review_fontcolor"><?php _e( 'Font Color', 'wp-review' ); ?></label>
			<input type="text" class="wp-review-color" name="wp_review_fontcolor" id ="wp_review_fontcolor" value="<?php echo $fontcolor; ?>" />
		</p>

		<p class="wp-review-field"<?php if (empty($displayed_fields['bgcolor1'])) echo ' style="display: none;"'; ?>>
			<label for="wp_review_bgcolor1"><?php _e( 'Heading Background Color', 'wp-review' ); ?></label>
			<input type="text" class="wp-review-color" name="wp_review_bgcolor1" id ="wp_review_bgcolor1" value="<?php echo $bgcolor1; ?>" />
		</p>

		<p class="wp-review-field"<?php if (empty($displayed_fields['bgcolor2'])) echo ' style="display: none;"'; ?>>
			<label for="wp_review_bgcolor2"><?php _e( 'Background Color', 'wp-review' ); ?></label>
			<input type="text" class="wp-review-color" name="wp_review_bgcolor2" id="wp_review_bgcolor2" value="<?php echo $bgcolor2; ?>" />
		</p>

		<p class="wp-review-field"<?php if (empty($displayed_fields['bordercolor'])) echo ' style="display: none;"'; ?>>
			<label for="wp_review_bordercolor"><?php _e( 'Border Color', 'wp-review' ); ?></label>
			<input type="text" class="wp-review-color" name="wp_review_bordercolor" id="wp_review_bordercolor" value="<?php echo $bordercolor; ?>" />
		</p>
	</div>

	<?php
}

/**
 * Render the meta box.
 *
 * @since 1.0
 */
function wp_review_render_meta_box_heading( $post ) {
	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-heading-nonce' ); 
	
	/* Retrieve an existing value from the database. */
	$heading = get_post_meta( $post->ID, 'wp_review_heading', true );	
	?>
	<p class="wp-review-field">
		<label><?php _e( 'Review Heading', 'wp-review' ); ?></label>
		<input type="text" name="wp_review_heading" id="wp_review_heading" value="<?php _e( $heading ); ?>" />
	</p>
	<?php
}
 
function wp_review_render_meta_box_desc( $post ) {

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-desc-nonce' ); 

	/* Retrieve existing values from the database. */
	$hide_desc = get_post_meta( $post->ID, 'wp_review_hide_desc', true );
	$desc = get_post_meta( $post->ID, 'wp_review_desc', true );
	$desc_title = get_post_meta( $post->ID, 'wp_review_desc_title', true );
	if (!$desc_title) $desc_title = __('Summary', 'wp-review');
	?>
	<p id="wp-review-desc-title" class="wp-review-field">
			<input type="text" name="wp_review_desc_title" id="wp_review_desc_title" value="<?php esc_attr_e( $desc_title ); ?>" />
	</p>
	<?php

	/* Display wp editor field. */
	wp_editor( 
		$desc,
		'wp_review_desc',
		array(
			'tinymce'       => false,
			'quicktags'     => true,
			'media_buttons' => false,
			'textarea_rows' => 10 
		) 
	);
	?>
	<p class="wp-review-field">
		<label style="width: 100%;">
			<input type="hidden" name="wp_review_hide_desc" id="wp_review_hide_desc_unchecked" value="" />
			<input type="checkbox" name="wp_review_hide_desc" id="wp_review_hide_desc" value="1" <?php checked( $hide_desc ); ?> />
			<?php _e( 'Hide Description &amp; Total Rating', 'wp-review' ); ?>
		</label>
	</p>
	<?php
}

function wp_review_render_meta_box_userReview( $post ) {
	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-userReview-nonce' ); 
	
	/* Retrieve an existing value from the database. */
	$userReviews = get_post_meta( $post->ID, 'wp_review_userReview', true );	
	$enabled = false;	
	if( is_array( $userReviews ) && $userReviews[0] == 1 ) $enabled = true;

	?>
	
	<p class="wp-review-field">
		<label for="wp-review-userReview-disable"> <?php _e( 'Disabled', 'wp-review' ); ?>
			<input type="radio" name="wp_review_userReview[]" id="wp-review-userReview-disable" class="wp-review-userReview-options" value="0" <?php echo !$enabled ? 'checked' : ''; ?> />
		</label>
		
		<label for="wp-review-userReview-enable"> <?php _e( 'Enabled', 'wp-review' ); ?>
			<input type="radio" name="wp_review_userReview[]" id="wp-review-userReview-enable" class="wp-review-userReview-options" value="1" <?php echo $enabled ? 'checked' : ''; ?> />
		</label>
	</p>
	
	<?php
}

/**
 * Saves the meta box.
 *
 * @since 1.0
 */
function wp_review_save_postdata( $post_id, $post ) {

	if ( !isset( $_POST['wp-review-review-options-nonce'] ) || !wp_verify_nonce( $_POST['wp-review-review-options-nonce'], basename( __FILE__ ) ) )
		return;

	if ( !isset( $_POST['wp-review-item-nonce'] ) || !wp_verify_nonce( $_POST['wp-review-item-nonce'], basename( __FILE__ ) ) )
		return;

	if ( !isset( $_POST['wp-review-heading-nonce'] ) || !wp_verify_nonce( $_POST['wp-review-heading-nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( !isset( $_POST['wp-review-desc-nonce'] ) || !wp_verify_nonce( $_POST['wp-review-desc-nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( !isset( $_POST['wp-review-userReview-nonce'] ) || !wp_verify_nonce( $_POST['wp-review-userReview-nonce'], basename( __FILE__ ) ) )
		return;

	/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	/* Check the user's permissions. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	$meta = array(
		'wp_review_custom_location' => $_POST['wp_review_custom_location'],
		'wp_review_custom_colors' => $_POST['wp_review_custom_colors'],
		'wp_review_custom_width' => $_POST['wp_review_custom_width'],
		'wp_review_location' => $_POST['wp_review_location'],
		'wp_review_type'     => $_POST['wp_review_type'],
		'wp_review_heading'     => $_POST['wp_review_heading'],
		'wp_review_desc_title'     => $_POST['wp_review_desc_title'],
		'wp_review_desc'     => $_POST['wp_review_desc'],
		'wp_review_hide_desc'     => $_POST['wp_review_hide_desc'],
		'wp_review_userReview'     => $_POST['wp_review_userReview'],
		'wp_review_total'    => $_POST['wp_review_total'],
		'wp_review_color'    => $_POST['wp_review_color'],
		'wp_review_fontcolor'    => $_POST['wp_review_fontcolor'],
		'wp_review_bgcolor1'    => $_POST['wp_review_bgcolor1'],
		'wp_review_bgcolor2'    => $_POST['wp_review_bgcolor2'],
		'wp_review_bordercolor' => $_POST['wp_review_bordercolor'],
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' === $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && ($new_meta_value || $new_meta_value === '0') && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && ($new_meta_value || $new_meta_value === '0') && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
	}

	/* Repeatable update and delete meta fields method. */
	$title = $_POST['wp_review_item_title'];
	$star  = $_POST['wp_review_item_star'];

	$old   = get_post_meta( $post_id, 'wp_review_item', true );
	$new   = array();

	$count = count( $title );
	
	for ( $i = 0; $i < $count; $i++ ) {
		if ( $title[$i] != '' )
			$new[$i]['wp_review_item_title'] = sanitize_text_field( $title[$i] );
		if ( $star[$i] != '' )
			$new[$i]['wp_review_item_star'] = sanitize_text_field( $star[$i] );
	}

	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'wp_review_item', $new );
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'wp_review_item', $old );

	/**
	 * Delete all data when switched to 'No Review' type.
	 */
	$type = $meta['wp_review_type'];//get_post_meta( $post_id, 'wp_review_type', true );
	if ( $type == '' ) {
		delete_post_meta( $post_id, 'wp_review_desc', $_POST['wp_review_desc'] );
		delete_post_meta( $post_id, 'wp_review_heading', $_POST['wp_review_heading'] );
		delete_post_meta( $post_id, 'wp_review_userReview', $_POST['wp_review_userReview'] );
		delete_post_meta( $post_id, 'wp_review_item', $old );
	}

}

// Fix for post previews
// with this code, the review meta data will actually get saved on Preview
add_filter('_wp_post_revision_fields', 'add_field_debug_preview');
function add_field_debug_preview($fields){
   $fields["debug_preview"] = "debug_preview";
   return $fields;
}
add_action( 'edit_form_after_title', 'add_input_debug_preview' );
function add_input_debug_preview() {
   echo '<input type="hidden" name="debug_preview" value="debug_preview">';
}
?>