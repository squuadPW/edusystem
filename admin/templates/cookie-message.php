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