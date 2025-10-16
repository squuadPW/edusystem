<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Payment links', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="#" id="open-upload-modal" class="button button-outline-primary" onclick="return false;">
				<?= __('Upload .csv', 'edusystem'); ?>
			</a>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=add_dynamic_link'); ?>" class="button button-outline-primary"><?= __('Add Payment Link', 'edusystem'); ?></a>
		</div>
	</div>

	<?php
    	include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

	<form action="" id="post-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
			<input value="<?= $_GET['s'] ?>" type="search" id="search-box-id-search-input" name="s"
				placeholder="<?= __('Search for title', 'edusystem'); ?>"
				value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
			<input type="submit" id="search-submit" class="button" value="Search">
		</p>
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $list_dynamic_links->display() ?>
	</form>
</div>
</div>


<div id='upload-modal' class='modal' style='display:none'>
    <form id="upload-form" method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content&action=upload_document'); ?>"
        enctype="multipart/form-data">
        <div class='modal-content' style="width: 70%;">
            <div class="modal-header">
                <h3 style="font-size:20px;"><?= __('Upload CSV') ?></h3>
                <span id="upload-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
            </div>
			<div class="modal-body" style="padding:10px;">
				<div style="margin-bottom:15px;">
					<strong><?= __('Expected CSV format:', 'edusystem'); ?></strong><br>
					<small><?= __('The CSV must include the following columns (header required):', 'edusystem'); ?></small>
					<pre style="background:#f8f8f8;border:1px solid #ddd;padding:8px;overflow-x:auto;font-size:12px;">
type_document,id_document,name,last_name,email,program_identificator,payment_plan_identificator,transfer_cr,send_email
DNI,12345678,Juan,Pérez,juan.perez@email.com,PROG001,PLAN001,1,1
DNI,87654321,Ana,García,ana.garcia@email.com,PROG002,PLAN002,0,0
					</pre>
					<ul style="font-size:12px;">
						<li><b>type_document</b>: <?= __('Document type (e.g., DNI, Passport)', 'edusystem'); ?></li>
						<li><b>id_document</b>: <?= __('Document number', 'edusystem'); ?></li>
						<li><b>name</b>: <?= __('First name', 'edusystem'); ?></li>
						<li><b>last_name</b>: <?= __('First surname', 'edusystem'); ?></li>
						<li><b>email</b>: <?= __('Email address', 'edusystem'); ?></li>
						<li><b>program_identificator</b>: <?= __('Program code/identifier', 'edusystem'); ?></li>
						<li><b>payment_plan_identificator</b>: <?= __('Payment plan code/identifier', 'edusystem'); ?></li>
						<li><b>transfer_cr</b>: <?= __('Transfer credits (1 = Yes, 0 = No)', 'edusystem'); ?></li>
						<li><b>send_email</b>: <?= __('Send email (1 = Yes, 0 = No)', 'edusystem'); ?></li>
					</ul>
				</div>
				<div>
					<label for="document_upload_file">Document</label><br>
					<input type="file" name="document_upload_file" id="document_upload_file" required>
				</div>
			</div>
            <div class="modal-footer">
                <button id="upload-button" type="submit"
                    class="button button-outline-primary modal-close"><?= __('Upload', 'edusystem'); ?></button>
                <button id="upload-exit-button" type="button"
                    class="button button-danger modal-close"><?= __('Exit', 'edusystem'); ?></button>
            </div>
        </div>
    </form>
</div>