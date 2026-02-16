<?php
if ($_GET['page'] == 'add_admin_form_payments_content') {
	include(plugin_dir_path(__FILE__) . 'topbar-payments.php');
}
?>

<div class="tabs-content">
	<div class="wrap">
		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

		<div style="text-align:start;">
			<?php if ($_GET['page'] == 'add_admin_form_payments_content') { ?>

				<?php if (!isset($_GET['section_tab'])): ?>
					<h1 class="wp-heading-line"><?= __('Payments for Review', 'edusystem'); ?></h1>
				<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments'): ?>
					<h1 class="wp-heading-line"><?= __('All Payments', 'edusystem'); ?></h1>
				<?php endif; ?>

			<?php } else if ($_GET['page'] == 'fees_content') { ?>

					<h1 class="wp-heading-line"><?= __('Fees', 'edusystem'); ?></h1>

			<?php } else { ?>

					<h1 class="wp-heading-line"><?= __('Payment Plans', 'edusystem'); ?></h1>
			<?php } ?>
		</div>

		<?php if ($_GET['page'] == 'add_admin_form_payments_plans_content') { ?>
			<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details'); ?>"
					class="button button-outline-primary"><?= __('Add new plan', 'edusystem'); ?></a>
			</div>
		<?php } else if ($_GET['page'] == 'fees_content') { ?>
				<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
					<a href="<?= admin_url('admin.php?page=fees_content&section_tab=fee_details'); ?>"
						class="button button-outline-primary"><?= __('Add fee', 'edusystem'); ?></a>
				</div>
		<?php } else if ($_GET['page'] == 'fees_content') { ?>
					<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
						<a href="<?= admin_url('admin.php?page=fees_content&section_tab=fee_details'); ?>"
							class="button button-outline-primary"><?= __('Add fee', 'edusystem'); ?></a>
					</div>
		<?php } ?>

		<form action="" id="post-filter" method="post">
			<?php if (isset($_GET['page']) && $_GET['page'] == 'add_admin_form_payments_content') { ?>
				<?php
				$payment_gateways = function_exists('WC') && WC()->payment_gateways ? WC()->payment_gateways->payment_gateways() : [];
				$selected_payment_method = $_REQUEST['payment_method'] ?? '';
				$selected_payment_type = $_REQUEST['payment_type'] ?? '';
				?>
				<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:10px;">
					<input type="date" name="date_from" value="<?= !empty($_REQUEST['date_from']) ? esc_attr($_REQUEST['date_from']) : ''; ?>"
						placeholder="<?= __('From date', 'edusystem'); ?>">
					<input type="date" name="date_to" value="<?= !empty($_REQUEST['date_to']) ? esc_attr($_REQUEST['date_to']) : ''; ?>"
						placeholder="<?= __('To date', 'edusystem'); ?>">
					<select name="payment_method">
						<option value=""><?= __('Select payment method', 'edusystem'); ?></option>
						<?php foreach ($payment_gateways as $gateway) { ?>
							<option value="<?= esc_attr($gateway->id); ?>" <?= ($selected_payment_method == $gateway->id) ? 'selected' : ''; ?>>
								<?= esc_html($gateway->get_title()); ?>
							</option>
						<?php } ?>
					</select>
					<input type="number" name="order_id" min="1" placeholder="<?= __('Order ID', 'edusystem'); ?>"
						value="<?= !empty($_REQUEST['order_id']) ? esc_attr($_REQUEST['order_id']) : ''; ?>">
					<select name="payment_type">
						<option value=""><?= __('Select payment type', 'edusystem'); ?></option>
						<option value="normal" <?= ($selected_payment_type == 'normal') ? 'selected' : ''; ?>>
							<?= __('Normal', 'edusystem'); ?>
						</option>
						<option value="split" <?= ($selected_payment_type == 'split') ? 'selected' : ''; ?>>
							<?= __('Split', 'edusystem'); ?>
						</option>
					</select>
					<?php
					// 1. Define the placeholder text variable and set a default value
					$placeholder_text = __('Search...', 'edusystem');

					// 2. Use conditional logic to set the correct placeholder based on the page
					if (isset($_GET['page'])) {
						if ($_GET['page'] == 'add_admin_form_payments_content') {
							$placeholder_text = __('Search for student', 'edusystem');
						} elseif ($_GET['page'] == 'add_admin_form_payments_plans_content') {
							// You can change this to 'Search for plan', 'Search for subscription', etc.
							$placeholder_text = __('Search for name, identificator or description', 'edusystem');
						}
					}

					// Check if the search box should be displayed at all (optional, based on your original code's logic)
					if (isset($_GET['page']) && ($_GET['page'] == 'add_admin_form_payments_content' || $_GET['page'] == 'add_admin_form_payments_plans_content')) {
						?>
						<p class="search-box" style="margin:0;">
							<label class="screen-reader-text"
								for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
							<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= $placeholder_text; ?>"
								value="<?= (!empty($_REQUEST['s'])) ? esc_attr($_REQUEST['s']) : ''; ?>">
							<input type="submit" id="search-submit" class="button" value="Search">
						</p>
					<?php } ?>
				</div>
			<?php } ?>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_payments->display() ?>
		</form>
	</div>
</div>