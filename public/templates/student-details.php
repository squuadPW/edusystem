<h2 style="font-size:24px;text-align:center;"><?= __('Student Details','storefront-child') ?></h2>

<div style="margin-top:20px;">
    <a href="<?= wc_get_account_endpoint_url('student') ?>" class="button button-primary"><span class="dashicons dashicons-arrow-left-alt2" style="vertical-align:middle;"></span><?= __('Back','aes'); ?></a>
</div>

<form action="" method="post" style="margin-top:20px;" >

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="document_type"><?php esc_html_e( 'Document Type', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<select style="height:43.75px;padding:5px !important;" name="document_type" required>
			<option value=""></option>
			<option value="identification_document" <?= ($student->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document','aes'); ?></option>
			<option value="passport" <?= ($student->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport','aes'); ?></option>
			<option value="ssn" <?= ($student->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SNN','aes'); ?></option>
		</select>

	</p>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-last">
		<label for="id_document"><?php esc_html_e( 'ID document', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="id_document" id="id_document" autocomplete="given-name" value="<?= $student->id_document; ?>" required />
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="student_id" id="student_id" value="<?php echo esc_attr( $student->id ); ?>" />
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $student->name ); ?>" required />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $student->last_name ); ?>" required />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-first">
		<label for="birth_date"><?php esc_html_e( 'Birth Date', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="birth_date" id="birth_date" value="<?= $student->birth_date; ?>" required />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Gender', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<select style="height:43.75px;padding:5px !important;" name="gender" required>
			<option value=""></option>
			<option value="female" <?= ($student->gender == 'female') ? 'selected' : ''; ?>><?= __('Female','aes'); ?></option>
			<option value="male" <?= ($student->gender == 'male') ? 'selected' : ''; ?>><?= __('Male','aes'); ?></option>
		</select>
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $student->email ); ?>" required />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
		<label for="number_phone"><?php esc_html_e( 'Phone', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="number_phone" id="number_phone"  value="<?= $student->phone; ?>" required />
	</p>

	<div class="clear"></div>
	<?php $countries = get_countries(); ?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="account_email"><?php esc_html_e('Country', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<select style="height:43.75px;padding:5px !important;" name="country" required>
			<?php foreach($countries as $key => $country): ?>
				<?php if($student->country == $key): ?>
					<option value='<?= $key ?>' selected><?= $country ?></option>
				<?php else: ?>
					<option value='<?= $key ?>'><?= $country ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
		<label for="account_email"><?php esc_html_e('City', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="city"  value="<?= $student->city; ?>" required />
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="billing_postcode"><?php esc_html_e('Postal Code', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="postal_code"  value="<?= $student->postal_code; ?>" required />
	</p>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button style="width:100%;margin-top:20px;" type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_student_details" />
	</p>
</form>