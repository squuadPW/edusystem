<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Departments','restaurant-system-app'); ?>
			<button type="submit" class="button-link add-new-toggle" style="border: 1px solid #2271b1; padding: 6px; font-size: 13px; border-radius: 3px; text-decoration: none;">
				<?php
					printf( get_post_type_object( 'page' )->labels->add_new_item );
				?>
			</button>
		</h1>
		</div>
		<form id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_departments->display() ?>
		</form>  
    </div>
</div>