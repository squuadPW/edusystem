<?php global $wpdb ?>

<div id='modalStatusRequest' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-status-request"></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_requests_content&action=change_status_request') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<p id="message-modal-status-request"></p>
				<div class="display:flex">
					<div>
						<label for="description">You can add a description about the request <strong><span id="text-modal-status-request"></span></strong> if you wish</label>
						<textarea style="width: 100%" name="description" value=""></textarea>
					</div>
					<input type="hidden" id="request_id" name="request_id" value="<?= $request->id ?>">
					<input type="hidden" id="status_id" name="status_id" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'aes'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'aes'); ?></button>
			</div>
		</form>
	</div>
</div>