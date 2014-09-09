<?php

// create custom plugin settings menu
add_action('admin_menu', 'wpreview_create_menu');

function wpreview_create_menu() {

	//create new top-level menu
	$hook = add_options_page('WP Review', 'WP Review', 'administrator', __FILE__, 'wpreview_settings_page');

	//call register settings function
	add_action( 'admin_init', 'wpreview_register_settings' );

	// body class
	// create_function() requires PHP 5.2+
	add_action( "load-$hook", create_function('', 'add_filter( "admin_body_class", "wpreview_admin_body_class" );') );
}
// body class
function wpreview_admin_body_class( $classes ) {
	$classes .= 'wp-review-admin-options';
	return $classes;
}
function wpreview_register_settings() {
	//register our settings
	register_setting( 'wpreview-settings-group', 'wp_review_options' );
}

function wpreview_settings_page() {
	$options = get_option('wp_review_options');

    $available_types = apply_filters('wp_review_metabox_types', array('star' => __('Star', 'wp-review'), 'point' => __('Point', 'wp-review'), 'percentage' => __('Percentage', 'wp-review')));
    $default_options = array(
		'colors' => array(
			'color' => '',
    		'fontcolor' => '',
    		'bgcolor1' => '',
    		'bgcolor2' => '',
    		'bordercolor' => ''),
		'default_features' => array(),
		'image_sizes' => array()
	);
    // set defaults
    if (empty($options)) {
    	update_option( 'wp_review_options', $options = $default_options );
    }
    if (empty($options['image_sizes'])) $options['image_sizes'] = array();

    $opt_name = 'wp_review_options_'.wp_get_theme();
	$options_updated = get_option( $opt_name );
	$suggest_theme_defaults = true;
	if (!empty($_GET['wp-review-theme-defaults']) && empty($_GET['settings-updated'])) {
		wp_review_theme_defaults($options_updated, true);
		$options = get_option('wp_review_options');
		$suggest_theme_defaults = false;
	}
	// test to see if we need to sugges setting theme defaults
	if (empty($options_updated)) $options_updated = array();
	$opts_tmp = array_merge($options, $options_updated);
	if ($opts_tmp == $options) $suggest_theme_defaults = false;
?>
<div id="poststuff" class="wrap wp-review">
	<div id="post-body">
		<div id="col-right" style="float: left;">
			<h2><?php _e('WP Review Settings', 'wp-review'); ?></h2>

			<form method="post" action="options.php">
			    <?php settings_fields( 'wpreview-settings-group' ); ?>

				<?php 
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
			    foreach ($defaultCriteria as $item) {
			        $defaultItems[] = array( 'wp_review_item_title' => $item, 'wp_review_item_star' => '');
			    }
			    $options['colors'] = apply_filters( 'wp_review_colors', $options['colors'], 0 );
			    if (!isset($options['deafults'])) $options['deafults'] = array();
				/* Retrieve an existing value from the database. */
				$items     = ! empty($options['default_features']) ? $options['default_features'] : ''; 
				$color     = ! empty($options['colors']['color']) ? $options['colors']['color'] : ''; 
				$location  = ! empty($options['review_location']) ? $options['review_location'] : ''; 
				$fontcolor = ! empty($options['colors']['fontcolor']) ? $options['colors']['fontcolor'] : ''; 
				$bgcolor1  = ! empty($options['colors']['bgcolor1']) ? $options['colors']['bgcolor1'] : ''; 
				$bgcolor2  = ! empty($options['colors']['bgcolor2']) ? $options['colors']['bgcolor2'] : ''; 
				$bordercolor  = ! empty($options['colors']['bordercolor']) ? $options['colors']['bordercolor'] : ''; 

			    if ( $items == '' ) $items = $defaultItems;
				if( $color == '' ) $color = $defaultColors['color'];
			    if( $location == '' ) $location = $defaultLocation;
				if( $fontcolor == '' ) $fontcolor = $defaultColors['fontcolor'];
				if( $bgcolor1 == '' ) $bgcolor1 = $defaultColors['bgcolor1'];
				if( $bgcolor2 == '' ) $bgcolor2 = $defaultColors['bgcolor2'];
				if( $bordercolor == '' ) $bordercolor = $defaultColors['bordercolor'];
			    
			    $fields = array(
			        'location' => true, 
			        'color' => true, 
			        'fontcolor' => true, 
			        'bgcolor1' => true, 
			        'bgcolor2' => true, 
			        'bordercolor' => true,
			        'custom_colors' => true,
			        'custom_location' => true,
			        'width' => true,
			        'align' => true
			    );
			    $displayed_fields = apply_filters('wp_review_metabox_item_fields', $fields);
				
				$location = apply_filters( 'wp_review_location', $location, 0 );
				if (has_filter('wp_review_location')) echo '<p class="wp-review-filter-msg"><div class="dashicons dashicons-info"></div>'.__('There is a filter set for the review location that may modify the options below.', 'wp-review').'</p>'; 
				
				if ($suggest_theme_defaults) { ?>
				<div class="wp-review-theme-defaults-msg updated settings-error">
					<p class="wp-review-field">
						<?php _e('The current theme provides default settings for the plugin.', 'wp-review'); ?><br />
					</p>
					<a href="<?php echo admin_url('options-general.php?page=wp-review/admin/options.php&wp-review-theme-defaults=1'); ?>" class="button button-primary"><?php _e('Set to theme defaults', 'wp-review'); ?></a>
					<a href="#" class="dashicons dashicons-no-alt close-notice"></a>
				</div>
				<?php } ?>

				<p class="wp-review-field">
					<label for="wp_review_location"><?php _e( 'Review Location', 'wp-review' ); ?></label>
					<select name="wp_review_options[review_location]" id="wp_review_location">
						<option value="bottom" <?php selected( $location, 'bottom' ); ?>><?php _e( 'After Content', 'wp-review' ) ?></option>
						<option value="top" <?php selected( $location, 'top' ); ?>><?php _e( 'Before Content', 'wp-review' ) ?></option>
			            <option value="custom" <?php selected( $location, 'custom' ); ?>><?php _e( 'Custom (use shortcode)', 'wp-review' ) ?></option>
					</select>
				</p>
			    
			    <p class="wp-review-field" id="wp_review_shortcode_hint_field">
					
					<input id="wp_review_shortcode_hint" type="text" value="[wp-review]" readonly="readonly" />
			        <span><?php _e('Copy &amp; paste this shortcode in the post content.', 'wp-review') ?></span>
				</p>

				<?php if (has_filter('wp_review_colors')) echo '<p class="wp-review-filter-msg"><div class="dashicons dashicons-info"></div>'.__('There is a filter set for the review colors that may modify the options below.', 'wp-review').'</p>'; ?>
				
				<div class="wp-review-color-options">
					<p class="wp-review-field"<?php if (empty($displayed_fields['color'])) echo ' style="display: none;"'; ?>>
						<label for="wp_review_color"><?php _e( 'Review Color', 'wp-review' ); ?></label>
						<input type="text" class="wp-review-color" name="wp_review_options[colors][color]" value="<?php echo $color; ?>" />
					</p>

					<p class="wp-review-field"<?php if (empty($displayed_fields['fontcolor'])) echo ' style="display: none;"'; ?>>
						<label for="wp_review_fontcolor"><?php _e( 'Font Color', 'wp-review' ); ?></label>
						<input type="text" class="wp-review-color" name="wp_review_options[colors][fontcolor]" id ="wp_review_fontcolor" value="<?php echo $fontcolor; ?>" />
					</p>

					<p class="wp-review-field"<?php if (empty($displayed_fields['bgcolor1'])) echo ' style="display: none;"'; ?>>
						<label for="wp_review_bgcolor1"><?php _e( 'Heading Background Color', 'wp-review' ); ?></label>
						<input type="text" class="wp-review-color" name="wp_review_options[colors][bgcolor1]" id ="wp_review_bgcolor1" value="<?php echo $bgcolor1; ?>" />
					</p>

					<p class="wp-review-field"<?php if (empty($displayed_fields['bgcolor2'])) echo ' style="display: none;"'; ?>>
						<label for="wp_review_bgcolor2"><?php _e( 'Background Color', 'wp-review' ); ?></label>
						<input type="text" class="wp-review-color" name="wp_review_options[colors][bgcolor2]" id="wp_review_bgcolor2" value="<?php echo $bgcolor2; ?>" />
					</p>

					<p class="wp-review-field"<?php if (empty($displayed_fields['bordercolor'])) echo ' style="display: none;"'; ?>>
						<label for="wp_review_bordercolor"><?php _e( 'Border Color', 'wp-review' ); ?></label>
						<input type="text" class="wp-review-color" name="wp_review_options[colors][bordercolor]" id="wp_review_bordercolor" value="<?php echo $bordercolor; ?>" />
					</p>
				</div>

				<div class="wp-review-default-features" style="display: block;">
				<!-- Start repeater field -->
				<table id="wp-review-item" class="wp-review-item" width="100%">

					<thead>
						<tr>
							<th width="90%">
				<?php if (has_filter('wp_review_default_criteria')) echo '<p class="wp-review-filter-msg"><div class="dashicons dashicons-info"></div>'.__('Default features are set by a filter function. Remove it to change.', 'wp-review').'</p>'; else _e( 'Default features', 'wp-review' ); ?>
							</th>
							<th width="10%"></th>
						</tr>
					</thead>

					<tbody>
					<?php if (has_filter('wp_review_default_criteria')) { ?>
						<?php foreach ( $defaultCriteria as $item ) { ?>
								<tr>
									<td>
										<input type="text" class="widefat" name="wp_review_options[default_features][]" value="<?php if( !empty( $item ) ) echo esc_attr( $item ); ?>" disabled="disabled" readonly="readonly" />
									</td>
									<td></td>
								</tr>
							<?php } ?>
					<?php } else { ?> 
						<?php if ( !empty($items) ) : ?>
							<?php foreach ( $items as $item ) { ?>

								<tr>
									<td>
										<input type="text" class="widefat" name="wp_review_options[default_features][]" value="<?php if( !empty( $item ) ) echo esc_attr( $item ); ?>" />
									</td>
									<td><a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a></td>
								</tr>
							<?php } ?>
						<?php endif; ?>
					<?php } ?>
						<!-- empty hidden one for jQuery -->
						<tr class="empty-row screen-reader-text">
							<td><input type="text" class="widefat" data-name="wp_review_options[default_features][]" /></td>
							<td><a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a></td>
						</tr>

					</tbody>

				</table>
				<?php if ( ! has_filter('wp_review_default_criteria') ) { ?>
				<table width="100%">
					<tr>
						<td width="100%"><a id="add-row" class="button" href="#"><?php _e( 'Add default feature', 'wp-review' ) ?></a></td>
					</tr>
				</table>
				<?php } ?>
				</div>

			    <p class="submit">
			    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>

			</form>
		</div>
		<div id="col-left" style="float: right">
			<a href="https://mythemeshop.com/plugins/wp-review-pro/" target="_blank">
				<img src="<?php echo trailingslashit( WP_REVIEW_ASSETS ); ?>/images/wp-review-pro.jpg">
			</a>
		</div>
	</div>
</div>
<?php }

// Add settings link on plugin page
function wpreview_plugin_settings_link($links) {
	$dir = explode('/', WP_REVIEW_PLUGIN_BASE);
	$dir = $dir[0];
	$settings_link = '<a href="options-general.php?page='.$dir.'/admin/options.php">'.__('Settings', 'wp-review').'</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
add_filter('plugin_action_links_'.WP_REVIEW_PLUGIN_BASE, 'wpreview_plugin_settings_link' );

?>