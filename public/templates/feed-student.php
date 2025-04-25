<!-- Incluir CDN de Swiper -->
<link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<div>
    <h2 style="font-size:24px;text-align:center;">
        <?php if (wp_date('H') < 12) { ?>
            <?= __('Good Morning', 'storefront-child') . ', ' . $current_user->first_name; ?> 🙌
        <?php } else if (wp_date('H') < 18) { ?>
            <?= __('Good Afternoon', 'storefront-child') . ', ' . $current_user->first_name; ?> 👋
        <?php } else { ?>
            <?= __('Good Evening', 'storefront-child') . ', ' . $current_user->first_name; ?> ✌
        <?php } ?>
    </h2>
</div>

<?php if(count($feeds) > 0): ?>
    <div class="swiper-container content-dashboard">
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php foreach ($feeds as $feed): ?>
                    <?php
                    $desktop_url = wp_get_attachment_url($feed->attach_id_desktop);
                    $mobile_url = !empty($feed->attach_id_mobile) ? wp_get_attachment_url($feed->attach_id_mobile) : $desktop_url;
                    ?>
                    
                    <div class="swiper-slide" 
                        style="--desktop-image: url('<?php echo $desktop_url; ?>');
                                --mobile-image: url('<?php echo $mobile_url; ?>');">
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Botones de navegación -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <!-- Paginación -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
<?php endif; ?>

<?php if ($student && !$student->moodle_password) { ?>
	<div class="text-center info-box content-dashboard">
		<div>
			<p>To help you get started on the right foot, we've put together a few simple steps to make your journey as
				smooth and enjoyable as possible. Please take a few minutes to complete them, and let's get started on this
				exciting path together</p>
			<ul class="info-list">
				<li>
					<i class="fas fa-upload"></i>
					Once your payment is approved, the option to upload all required documents is enabled <a
						style="text-decoration: underline !important; color: #091c5c;"
						href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents' ?>">here</a>
				</li>
				<li>
					<i class="fas fa-credit-card"></i>
					If you haven't already, please process the payment for your registration fee. This will enable us to
					finalize your registration and grant you access to the <a
						style="text-decoration: underline !important; color: #091c5c;"
						href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student' ?>">virtual
						classroom</a>.
				</li>
			</ul>
			<p class="info-note">Once we receive your documents and process your payment, we will review your application
				and grant you access to the <a style="text-decoration: underline !important; color: #091c5c;"
					href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student' ?>">virtual
					classroom</a>. You will receive an email with instructions on how to access the classroom and start your
				course.</p>
		</div>
	</div>
<?php } ?>

<?php if (count($orders) > 0 && (in_array('student', $roles) || in_array('parent', $roles))) { ?>
	<a href="<?php echo home_url('my-account/orders') ?>">
		<div class="text-center info-box"
			style="margin: 20px 0px; background-color: #ff0000c2; color: white; font-weight: bold; font-size: 18px; -webkit-box-shadow: 0px 0px 10px 0px rgba(255, 0, 0, 1); -moz-box-shadow: 0px 0px 10px 0px rgba(255, 0, 0, 1); box-shadow: 0px 0px 10px 0px rgb(255 0 0 / 75%); border: 0px;">
			You have pending payments
			<?php foreach ($orders as $key => $order) { ?>
				<?php
				$dates = $order->get_meta('dates_next_orders');
				if ($dates) {
					$dates = json_decode($dates, true);
					foreach ($dates as $key => $date) { 
						$pending_payment = $order->get_meta('pending_payment');
						if ($pending_payment) { ?>
							<p><strong>Payment to: <?php echo $date['date'] ?> with an amount of
									<?php echo wc_price($pending_payment) ?></strong></p>
						<?php }
					}
				} ?>
			<?php } ?>
		</div>
	</a>
<?php } ?>

<?php if (get_user_meta($current_user->ID, 'pay_application_password', true) == '1') { ?>
	<div class="text-center info-box" style="margin: 10px 0px; background-color: #ffff0045;">
		<p>
			We're excited to have you join our community! As we prepare for your on boarding process, we want to remind you
			to schedule your meeting with our team to complete your registration.
		</p><br>

		<p>Don't Forget to Schedule <strong>Your Onboarding Meeting</strong></p> <br>

		<p>
		<div style="text-align:center;">
			<a target="_blank"
				href="https://calendly.com/americaneliteschooleu/reunion-de-onboarding-despues-de-la-compra?back=1&month=2024-10"><button
					type="button" class="submit"><?= __('Schedule Your Meeting Now', 'aes'); ?></button></a>
		</div>
		</p>
	</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.swiper', {
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});
</script>