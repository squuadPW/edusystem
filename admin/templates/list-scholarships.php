<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Scholarships for Review', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=all_scholarships'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('All Scholarships', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=pre_scholarships'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pre_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('Pre-Scholarships', 'form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<?php if (!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Scholarships for Review', 'aes'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships'): ?>
				<h1 class="wp-heading-line"><?= __('All Scholarships', 'aes'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pre_scholarships'): ?>
				<h1 class="wp-heading-line"><?= __('Pre-Scholarships', 'aes'); ?></h1>
			<?php endif; ?>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<button class="button button-outline-primary"
				id="pre-scholarship"><?= __('Add pre-scholarship', 'aes'); ?></button>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'aes') . ':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s"
					placeholder="<?= __('Search for scholarship ID', 'aes'); ?>"
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
						<option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
						<option value="passport"><?= __('Passport', 'aes'); ?></option>
						<option value="identification_document"><?= __('Identification Document', 'aes'); ?></option>
						<option value="ssn"><?= __('SSN', 'aes'); ?></option>
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
					class="button button-outline-primary modal-close"><?= __('Save', 'aes'); ?></button>
				<button id="pre-scholarship-exit-button" type="button"
					class="button button-danger modal-close"><?= __('Exit', 'aes'); ?></button>
			</div>
		</div>
	</form>
</div>