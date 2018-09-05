<div class="wp-review-role-manager-wrap">

	<?php
	$roles = get_editable_roles();
	foreach($roles as $slug => $role) {
		$role_caps = $role['capabilities'];
	?>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><strong><?php echo $role['name']; ?></strong></label>
			</div>

			<div class="wp-review-field-option">
				<div class="wpr-flex">
					<?php
					foreach(wp_review_get_capabilities() as $cap  => $capability) {
						$checked = isset($role_caps[$cap]) ? 'checked="checked"' : '';
					?>
						<div class="pr-10 wpr-col-1-3">
							<label for="wp_review_<?php echo $slug.'_'.$cap; ?>">
								<input name="wp_review_capabilities[<?php echo $slug ?>][<?php echo $cap; ?>]" id="wp_review_<?php echo $slug.'_'.$cap; ?>" type="checkbox" value="<?php echo $cap; ?>" <?php echo $checked; ?> >
								<?php echo $capability; ?>
							</label>
						</div>
				<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>