<?php 
	include(plugin_dir_path(__FILE__).'topbar-payments.php');
?>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Payments for Review','edusystem'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments'): ?>
				<h1 class="wp-heading-line"><?= __('All Payments','edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<input type="checkbox" name="only_pending" <?php echo $_POST['only_pending'] ? 'checked' : '' ?>>
				<label for="all"><?= __('Only pending payments','edusystem'); ?></label>
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<!-- <p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for payment ID','edusystem'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p> -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_payments->display() ?>
		</form>  
	</div>
</div>