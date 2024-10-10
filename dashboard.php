<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

$user = get_userdata(get_current_user_id());
$roles= $user->roles;

global $wpdb;
$current_user = wp_get_current_user();
$table_students = $wpdb->prefix.'students';
$student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}' OR partner_id={$current_user->ID}");

$orders = wc_get_orders(array(
	'status' => 'pending',
	'customer_id' => $current_user->ID,
));
?>

<?php if($student && !$student->moodle_password) {?>
	<div class="text-center info-box">
		<h2 style="font-size:24px;text-align:center;">
			<?php if (wp_date('H') < 12){ ?>
				<?= __('Good Morning','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php }else if(wp_date('H') < 18){ ?>
				<?= __('Good Afternoon','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php }else{ ?>
				<?= __('Good Evening','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php } ?>
		</h2>
		<div>
			<p>To help you get started on the right foot, we've put together a few simple steps to make your journey as smooth and enjoyable as possible. Please take a few minutes to complete them, and let's get started on this exciting path together</p>
			<ul class="info-list">
				<li>
				<i class="fas fa-upload"></i>
				Once your payment is approved, the option to upload all required documents is enabled <a style="text-decoration: underline !important; color: #091c5c;" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/student-documents' ?>">here</a>
				</li>
				<li>
				<i class="fas fa-credit-card"></i>
				If you haven't already, please process the payment for your registration fee. This will enable us to finalize your registration and grant you access to the <a style="text-decoration: underline !important; color: #091c5c;" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/student' ?>">virtual classroom</a>.
				</li>
			</ul>
			<p class="info-note">Once we receive your documents and process your payment, we will review your application and grant you access to the <a style="text-decoration: underline !important; color: #091c5c;" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/student' ?>">virtual classroom</a>. You will receive an email with instructions on how to access the classroom and start your course.</p>
		</div>
	</div>
	<?php } else { ?>
	<div>
		<h2 style="font-size:18px;text-align:start;">
			<?php if (wp_date('H') < 12){ ?>
				<?= __('Good Morning','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php }else if(wp_date('H') < 18){ ?>
				<?= __('Good Afternoon','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php }else{ ?>
				<?= __('Good Evening','storefront-child').', '.$user->first_name.' '.$user->last_name; ?>
			<?php } ?>
		</h2>
	</div>
<?php } ?>

<?php if (count($orders) > 0) { ?>
		<a href="<?php echo home_url('my-account/orders') ?>">
			<div class="text-center info-box" style="margin: 10px 0px; background-color: #ffff0045;">
				You have pending payments
				<?php foreach ($orders as $key => $order) { ?>
					<?php 
					$dates = $order->get_meta('dates_next_orders');
					if ($dates) {
						$dates = json_decode($dates, true);
						foreach ($dates as $key => $date) { ?>
							<p><strong>Payment to: <?php echo $date['date'] ?> with an amount of <?php echo wc_price($order->get_meta('pending_payment')) ?></strong></p>
					<?php }
						} ?>
				<?php } ?>
			</div>	
		</a>
<?php } ?>

<?php if (get_user_meta($current_user->ID, 'pay_application_password', true) == '1') { ?>
		<div class="text-center info-box" style="margin: 10px 0px; background-color: #ffff0045;">
			<p>
			We're excited to have you join our community! As we prepare for your on boarding process, we want to remind you to schedule your meeting with our team to complete your registration.
			</p><br>

			<p>Don't Forget to Schedule <strong>Your Onboarding Meeting</strong></p> <br>

			<p>
				If you haven't already, please take a moment to schedule your onboarding meeting with us. This meeting is crucial to getting you started with our program, and we want to ensure you have all the necessary information to succeed.
			</p><br>

			<p>
				<div style="text-align:center;">
				<a target="_blank" href="https://calendly.com/americaneliteschooleu/reunion-de-onboarding-despues-de-la-compra?back=1&month=2024-10"><button type="button" class="submit"><?= __('Schedule Your Meeting Now', 'aes'); ?></button></a>
				</div>
			</p>
		</div>	
<?php } ?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
