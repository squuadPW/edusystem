<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Custom inputs', 'edusystem'); ?></h1>
		</div>

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_custom_input_content&section_tab=add_custom_input'); ?>"
				class="button button-outline-primary"><?= __('Add Custom Input', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="get">
			<!-- <p class="search-box">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="academic_period_cut">
					<option value="">Select academic period cut</option>
					<option value="A" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
					<option value="B" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
					<option value="C" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
					<option value="D" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
					<option value="E" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="academic_period">
					<option value="" selected>Select academic period to filter</option>
					<?php foreach ($periods as $key => $period) { ?>
						<option value="<?php echo $period->code; ?>" <?= !empty($_GET['academic_period']) ? (($_GET['academic_period'] == $period->code) ? 'selected' : '') : ''; ?>>
							<?php echo $period->name; ?>
						</option>
					<?php } ?>
				</select>
			</p> -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_custom_inputs->display() ?>
		</form>
	</div>
</div>