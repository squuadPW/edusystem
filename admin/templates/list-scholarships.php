<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Scholarships for Review', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=all_scholarships'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('All Scholarships', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=pre_scholarships'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pre_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('Pre-Scholarships', 'edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<?php if (!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Scholarships for Review', 'edusystem'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships'): ?>
				<h1 class="wp-heading-line"><?= __('All Scholarships', 'edusystem'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pre_scholarships'): ?>
				<h1 class="wp-heading-line"><?= __('Pre-Scholarships', 'edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<button class="button button-outline-primary"
				id="pre-scholarship"><?= __('Add pre-scholarship', 'edusystem'); ?></button>
			<button class="button button-outline-primary"
				id="assign-scholarship" style="margin-left: 10px"><?= __('Assign scholarship', 'edusystem'); ?></button>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s"
					placeholder="<?= __('Search for scholarship ID', 'edusystem'); ?>"
					value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_scholarships->display() ?>
		</form>
	</div>
</div>

<div id='pre-scholarship-modal' class='modal' style='display:none'>
	<form id="pre-scholarship-form" method="post"
		action="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&action=pre_scholarship'); ?>">
		<div class='modal-content' style="width: 70%;">
			<div class="modal-header">
				<h3 style="font-size:20px;"><?= __('Pre-scholarship info') ?></h3>
				<span id="pre-scholarship-exit-icon" class="modal-close"><span
						class="dashicons dashicons-no-alt"></span></span>
			</div>
			<div class="modal-body" style="padding:10px;">
				<div class="form-group">
					<label for="document_type">Document type</label><br>
					<select name="document_type" style="width: 250px" required>
						<option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
						<option value="passport"><?= __('Passport', 'edusystem'); ?></option>
						<option value="identification_document"><?= __('Identification Document', 'edusystem'); ?></option>
						<option value="ssn"><?= __('SSN', 'edusystem'); ?></option>
					</select>
				</div>
				<div class="form-group">
					<label for="document_id">ID Document</label><br>
					<input type="text" name="document_id" style="width: 250px" required>
				</div>
				<div class="form-group">
					<label for="name">Name</label><br>
					<input type="text" name="name" style="width: 250px" required>
				</div>
				<div class="form-group">
					<label for="scholarship_type">Scholarship type</label><br>
					<select name="scholarship_type" style="width: 250px" required>
						<?php foreach ($scholarships_availables as $key => $available) { ?>
							<option value="<?= $available->id ?>" selected><?= $available->name ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button id="pre-scholarship-button" type="submit"
					class="button button-outline-primary modal-close"><?= __('Save', 'edusystem'); ?></button>
				<button id="pre-scholarship-exit-button" type="button"
					class="button button-danger modal-close"><?= __('Exit', 'edusystem'); ?></button>
			</div>
		</div>
	</form>
</div>

<div id='assign-scholarship-modal' class='modal' style='display:none'>
	<form id="assign-scholarship-form" method="post"
		action="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&action=assign_scholarship'); ?>">
		<div class='modal-content' style="width: 70%;">
			<div class="modal-header">
				<h3 style="font-size:20px;"><?= __('Assign scholarship') ?></h3>
				<span id="assign-scholarship-exit-icon" class="modal-close"><span
						class="dashicons dashicons-no-alt"></span></span>
			</div>
			<div class="modal-body" style="padding:10px;">
				<input type="hidden" name="student_id" id="student_id">
				<div class="form-group">
					<label for="hc"><b><?= __('Search student', 'edusystem'); ?></b></label><br>
					<select class="js-example-basic" name="student_id"></select>
				</div>
				<div class="form-group">
					<label for="scholarship_type">Scholarship type</label><br>
					<select name="scholarship_type" style="width: 250px" required>
						<?php foreach ($scholarships_availables as $key => $available) { ?>
							<option value="<?= $available->id ?>" selected><?= $available->name ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button id="assign-scholarship-button" type="submit"
					class="button button-outline-primary modal-close"><?= __('Save', 'edusystem'); ?></button>
				<button id="assign-scholarship-exit-button" type="button"
					class="button button-danger modal-close"><?= __('Exit', 'edusystem'); ?></button>
			</div>
		</div>
	</form>
</div>