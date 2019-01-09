<?php
/**
 * Role Manager options
 *
 * @package WP_Review
 */

?>

<div class="wp-review-role-manager-wrap">

	<?php
	$roles = get_editable_roles();
	foreach ( $roles as $slug => $role ) {
		$role_caps = $role['capabilities'];
		?>
		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><strong><?php echo esc_html( $role['name'] ); ?></strong></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<div class="wpr-flex">
					<?php
					foreach ( wp_review_get_capabilities() as $cap => $capability ) {
						$checked = isset( $role_caps[ $cap ] ) ? 'checked="checked"' : '';
						?>
						<div class="pr-10 wpr-col-1-3">
							<span class="wp-review-disabled inline-block">
								<label for="wp_review_<?php echo esc_attr( $slug . '_' . $cap ); ?>">
									<input name="wp_review_capabilities[<?php echo esc_attr( $slug ); ?>][<?php echo esc_attr( $cap ); ?>]" id="wp_review_<?php echo esc_attr( $slug . '_' . $cap ); ?>" type="checkbox" value="<?php echo esc_attr( $cap ); ?>" <?php echo $checked; ?> disabled>
									<?php echo $capability; ?>
								</label>
							</span>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
