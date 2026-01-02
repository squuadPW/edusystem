<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Staff members','edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_staff_content&section_tab=add_staff'); ?>" class="button button-outline-primary"><?= __('Add Staff','edusystem'); ?></a>
			</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for Staff','edusystem'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>

			<div>
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				
				<select name="role" id="role" >
					<option value=""><?= __('Filter by Role','edusystem'); ?></option>
					<?php 
						$roles = wp_roles()->get_names();

						global $wpdb;
                        $departments = $wpdb->get_col( "SELECT name FROM {$wpdb->prefix}departments" );
						
						foreach( $roles as $role_key => $role_value ) {
							if( in_array( $role_key, $departments ) ) {
								$selected = ( !empty($_POST['role']) && $_POST['role'] == $role_key ) ? 'selected' : '';
								echo "<option value='{$role_key}' {$selected} >{$role_value}</option>";
							}
						}
					?>
				</select>
				<input type="submit" id="filter-submit" class="button" value="Change">

			</div>

			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_staff->display() ?>
		</form>  
	</div>
</div>