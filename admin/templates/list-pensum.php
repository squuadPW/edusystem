<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_pensum_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Program pensum','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_pensum_content&section_tab=pensum_institute'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pensum_institute') ? 'nav-tab-active' : ''; ?>"><?= __('Institute\'s pensum','edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">

		<?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
			<div class="notice notice-success is-dismissible">
				<p><?= $_COOKIE['message']; ?></p>
			</div>
			<?php setcookie('message', '', time(), '/'); ?>
		<?php } ?>
		<?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?= $_COOKIE['message-error']; ?></p>
			</div>
			<?php setcookie('message-error', '', time(), '/'); ?>
		<?php } ?>

		<div style="text-align:start;">
			<?php if (!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Program pensum', 'edusystem'); ?></h1>
            <?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pensum_institute'): ?>
				<h1 class="wp-heading-line"><?= __('Institute\'s pensum','edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_pensum_content&section_tab=pensum_details&institute='.$institute); ?>"
				class="button button-outline-primary"><?= __('Add pensum', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_pensum->display() ?>
		</form>
	</div>
</div>