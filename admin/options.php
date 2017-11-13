<?php

// create custom plugin settings menu
add_action('admin_menu', 'wpreview_create_menu');

function wpreview_create_menu() {

	//create new top-level menu
	$hook = add_options_page('WP Review', 'WP Review', 'administrator', __FILE__, 'wpreview_settings_page');

	//call register settings function
	add_action( 'admin_init', 'wpreview_register_settings' );

	// body class
	add_action( "load-$hook", 'wpreview_admin_body_class_filter' );
}
function wpreview_admin_body_class_filter() {
	add_filter( "admin_body_class", "wpreview_admin_body_class" );
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
	    'default_link_texts' => array(),
	    'default_link_urls' => array(),
	    'default_schema_type' => 'Thing',
		'default_user_review_type' => WP_REVIEW_REVIEW_DISABLED,
		'last_tab' => 'styling',
	);
    // set defaults
    if (empty($options)) {
    	update_option( 'wp_review_options', $options = $default_options );
    }

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

	// Migrate
	global $wpdb;
	$current_blog_id = get_current_blog_id();
	$total_rows = 0;
	$rows_left = 0;
	$migrated_rows = get_option( 'wp_review_migrated_rows', 0 );
	$has_migrated = get_option( 'wp_review_has_migrated', false );
	if ( ! $has_migrated && $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->base_prefix}mts_wp_reviews'") == "{$wpdb->base_prefix}mts_wp_reviews") {
		// Table exists and not migrated (fully) yet
		$total_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM '.$wpdb->base_prefix.'mts_wp_reviews WHERE blog_id = '.$current_blog_id );
		$rows_left = $total_rows - $migrated_rows;
	}
	
?>
<div id="col-right" class="wrap wp-review" style="float: left; padding-right: 3%; box-sizing: border-box;">
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
		$default_schema = empty( $options['default_schema_type'] ) ? $default_options['default_schema_type'] : $options['default_schema_type'];
		$default_user_review_type = empty( $options['default_user_review_type'] ) ? WP_REVIEW_REVIEW_DISABLED : $options['default_user_review_type'];
	    $options['colors'] = apply_filters( 'wp_review_colors', $options['colors'], 0 );
	    if (!isset($options['deafults'])) $options['deafults'] = array();
		/* Retrieve an existing value from the database. */
		$items = ! empty($options['default_features']) ? $options['default_features'] : '';
		$link_texts = ! empty( $options['default_link_text'] ) ? $options['default_link_text'] : array();
		$link_urls = ! empty( $options['default_link_url'] ) ? $options['default_link_url'] : array();
		$color     = ! empty($options['colors']['color']) ? $options['colors']['color'] : '';
		$location  = ! empty($options['review_location']) ? $options['review_location'] : ''; 
		$fontcolor = ! empty($options['colors']['fontcolor']) ? $options['colors']['fontcolor'] : ''; 
		$bgcolor1  = ! empty($options['colors']['bgcolor1']) ? $options['colors']['bgcolor1'] : ''; 
		$bgcolor2  = ! empty($options['colors']['bgcolor2']) ? $options['colors']['bgcolor2'] : ''; 
		$bordercolor  = ! empty($options['colors']['bordercolor']) ? $options['colors']['bordercolor'] : ''; 
		$registered_only = ! empty( $options['registered_only'] ) ? $options['registered_only'] : '';
		$add_backlink = ! empty( $options['add_backlink'] ) ? true : false;
		$last_tab = ! empty( $options['last_tab'] ) ? $options['last_tab'] : 'styling' ;
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
	    );
	    $displayed_fields = apply_filters('wp_review_metabox_item_fields', $fields);
		
		?>
		
		<div class="nav-tab-wrapper">
			<a href="#styling" class="nav-tab nav-tab-active" data-tab="styling"><?php _e('Styling', 'wp-review'); ?></a>
			<a href="#defaults" class="nav-tab" data-tab="defaults"><?php _e('Defaults', 'wp-review'); ?></a>
			<a href="#help" class="nav-tab" data-tab="help"><?php _e('Help', 'wp-review'); ?></a>
			<a href="#pro" class="nav-tab" data-tab="pro"><?php _e('Pro', 'wp-review'); ?></a>
			<?php if ( $rows_left ) : ?>
				<a href="#migrate" class="nav-tab" data-tab="migrate"><?php _e('Migrate Ratings', 'wp-review'); ?></a>
			<?php endif; ?>
		</div>
		<div id="wp-review-settings-tab-contents">
		
		<div class="settings-tab-styling">

		<h3><?php _e( 'Styling', 'wp-review' ); ?></h3>

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
		
		<p class="wp-review-field">
			<?php 
			$backlink_text = wp_review_get_backlink();
			?>
			<input name="wp_review_options[add_backlink]" type="hidden" value="0" />
			<input name="wp_review_options[add_backlink]" id="wp_review_add_backlink" type="checkbox" value="1" <?php checked( $add_backlink, '1' ); ?> />
			<label for="wp_review_add_backlink" style="width: 300px;"><?php printf(__( 'Add Backlink (%s)', 'wp-review' ), $backlink_text); ?></label>
		</p>
		
		<p class="submit">
	    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>

	    <input name="wp_review_options[last_tab]" id="wp_review_last_tab" type="hidden" value="<?php echo esc_attr($last_tab); ?>" />

		</div>
		<div class="settings-tab-defaults">
		<h3><?php _e( 'Defaults', 'wp-review' ); ?></h3>

		<?php $has_criteria_filter = has_filter( 'wp_review_default_criteria' ); ?>
		<?php $schemas = apply_filters( 'wp_review_schema_types', array() ); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Review Location', 'wp-review' ); ?></th>
				<td>
					<select name="wp_review_options[review_location]" id="wp_review_location">
						<option value="bottom" <?php selected( $location, 'bottom' ); ?>><?php _e( 'After Content', 'wp-review' ) ?></option>
						<option value="top" <?php selected( $location, 'top' ); ?>><?php _e( 'Before Content', 'wp-review' ) ?></option>
			            <option value="custom" <?php selected( $location, 'custom' ); ?>><?php _e( 'Custom (use shortcode)', 'wp-review' ) ?></option>
					</select>
					<p class="wp-review-field" id="wp_review_shortcode_hint_field">
						<input id="wp_review_shortcode_hint" type="text" value="[wp-review]" readonly="readonly" />
				        <span><?php _e('Copy &amp; paste this shortcode in the post content.', 'wp-review') ?></span>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Features', 'wp-review' ); ?></th>
				<td>
					<table id="wp-review-item">
						<?php if ( $has_criteria_filter ) : ?>
							<?php foreach ( $defaultCriteria as $item ) : ?>
								<?php if ( ! empty( $item ) ) : ?>
									<tr>
										<td style="padding:0">
											<input type="text" name="wp_review_options[default_features][]" value="<?php if( !empty( $item ) ) echo esc_attr( $item ); ?>" <?php echo $has_criteria_filter ? 'disabled="disabled" readonly="readonly"' : ''; ?> />
											<?php if ( ! $has_criteria_filter ) : ?>
												<a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php else : ?>
							<?php foreach ( $items as $item ) : ?>
								<?php if ( ! empty( $item ) ) : ?>
									<tr>
										<td style="padding:0">
											<input type="text" name="wp_review_options[default_features][]" value="<?php if( !empty( $item ) ) echo esc_attr( $item ); ?>" <?php echo $has_criteria_filter ? 'disabled="disabled" readonly="readonly"' : ''; ?> />
											<?php if ( ! $has_criteria_filter ) : ?>
												<a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<tr class="empty-row screen-reader-text">
							<td style="padding:0">
								<input class="focus-on-add" type="text" name="wp_review_options[default_features][]" />
								<a class="button remove-row" href="#"><?php _e( 'Delete', 'wp-review' ); ?></a>
							</td>
						</tr>
					</table>
					<?php if ( $has_criteria_filter ) : ?>
						<p class="description"><?php _e('Default features are set by a filter function. Remove it to change.', 'wp-review'); ?></p>
					<?php else : ?>
						<a class="add-row button" data-target="#wp-review-item" href="#"><?php _e( 'Add default feature', 'wp-review' ) ?></a>
					<?php endif; ?>
				</td>
			</tr>
		</table>

		<p class="submit">
	    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>

		</div>

		<div class="settings-tab-help">
			<p>
				<object type="application/x-shockwave-flash" style="width:450px; height:366px;" data="//www.youtube.com/v/NzMe-QY_WZY?version=3">
			        <param name="movie" value="//www.youtube.com/v/NzMe-QY_WZY?version=3" />
			        <param name="allowFullScreen" value="true" />
			        <param name="allowscriptaccess" value="always" />
		        </object>
	        </p>
			<p>
				<?php _e('All support for this plugin is provided through our forums. If you have not registered yet, you can do so here for ​<strong>FREE​</strong>: ', 'wp-review'); ?> 
				<a target="_blank" href="https://mythemeshop.com/#signup">https://mythemeshop.com/#signup</a>
			</p>
			<p>
				<?php _e('Check our free WordPress video tutorials here: ', 'wp-review'); ?> 
				<a target="_blank" href="https://mythemeshop.com/wordpress-101/">https://mythemeshop.com/wordpress-101/</a>
				<?php _e('(no registration required)', 'wp-review'); ?>
			</p>
	        <p>
				<?php _e('Thank you for using our plugin.', 'wp-review'); ?> 
			</p>

			<p class="submit">
		    	<a href="https://mythemeshop.com/#login" target="_blank" class="button-primary"><?php _e('Get Support for Free') ?></a>
		    </p>
		</div>

		<div class="settings-tab-pro">
			<p>
				<?php _e('Create Reviews Easily &amp; Rank Higher In Search Engines', 'wp-review'); ?> - <a target="_blank" href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Notification+Link&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg"><strong><?php _e('WP Review Pro Plugin', 'wp-review'); ?></strong></a>
			</p>

			<p class="submit">
		    	<a href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Link+CPC&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg" target="_blank" class="button-primary"><?php _e('Check the Pro Version') ?></a>
		    </p>
		</div>

		<?php if ( $rows_left ) : ?>
			<div class="settings-tab-migrate">
				<div id="settings-allow-migrate">
					<p><?php _e('Here you can import your existing user ratings from WP Review &lt; 4.0', 'wp-review'); ?></p>
					<p class="migrate-items"><?php printf( __( '%s ratings left to import.', 'wp-review'), '<span id="migrate-items-num">'.$rows_left.'</span>' ); ?></p>
					<a href="#" class="button button-secondary" id="start-migrate" data-start="<?php echo $migrated_rows; ?>"><?php _e('Start import', 'wp-review'); ?></a>
					<textarea id="wp-review-migrate-log"></textarea>
				</div>
				<p class="already-migrated-msg"><?php _e('Ratings have already been migrated.', 'wp-review'); ?></p>
			</div>
		<?php endif; ?>

		</div>
		

	    

	</form>
</div>
<?php if (!apply_filters( 'wp_review_remove_branding', false )) : ?>
	<div id="col-left" style="float: right; margin-top: 47px;">
		<a href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Link+CPC&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg" target="_blank">
			<img src="<?php echo trailingslashit( WP_REVIEW_ASSETS ); ?>/images/wp-review-pro.jpg">
		</a>
	</div>
	<?php endif; ?>
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