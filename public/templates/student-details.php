<h2 style="font-size:24px;text-align:center;"><?= __('Student Details','aes') ?></h2>

<div style="margin-top:20px;">
    <a href="<?= wc_get_account_endpoint_url('student') ?>" class="button button-primary" style="width:auto"><?= __('Back','aes'); ?></a>
</div>

<form action="" method="post" style="margin-top:20px;" >
	<h3 style="font-size:20px;"><?= __('General Information','aes'); ?></h3>
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="document_type"><?php esc_html_e( 'Document Type', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<select style="height:43.75px;padding:5px !important; background-color: #e0e7ff !important" name="document_type" disabled>
				<option value=""></option>
				<option value="identification_document" <?= ($student->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document','aes'); ?></option>
				<option value="passport" <?= ($student->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport','aes'); ?></option>
				<option value="ssn" <?= ($student->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SNN','aes'); ?></option>
			</select>
		<?php else: ?>
			<select style="height:43.75px;padding:5px !important; background-color: #e0e7ff !important" name="document_type" disabled>
				<option value=""></option>
				<option value="identification_document" <?= ($student->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document','aes'); ?></option>
				<option value="passport" <?= ($student->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport','aes'); ?></option>
				<option value="ssn" <?= ($student->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SNN','aes'); ?></option>
			</select>
		<?php endif; ?>
	</p>		
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-last">
		<label for="id_document"><?php esc_html_e( 'ID document', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="id_document" id="id_document" autocomplete="given-name" value="<?= $student->id_document; ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="id_document" id="id_document" autocomplete="given-name" value="<?= $student->id_document; ?>" disabled/>
		<?php endif; ?>
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'aes' ); ?></label>
        <input style=" background-color: #e0e7ff !important" type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="student_id" id="student_id" value="<?php echo esc_attr( $student->id ); ?>" />
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $student->name ); ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $student->name ); ?>" disabled/>
		<?php endif; ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-last">
		<label for="account_first_name"><?php esc_html_e( 'Middle Name', 'aes' ); ?>&nbsp;<span>(<?= __('Optional','aes'); ?>)</span></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_middle_name" id="account_middle_name" autocomplete="given-name" value="<?php echo esc_attr( $student->middle_name ); ?>" disabled/>
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_middle_name" id="account_middle_name" autocomplete="given-name" value="<?php echo esc_attr( $student->middle_name ); ?>" disabled/>
		<?php endif; ?>
	</p>
	<div class="clear"></div>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-first">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $student->last_name ); ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $student->last_name ); ?>" disabled/>
		<?php endif; ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Middle Last name', 'aes' ); ?>&nbsp;<span>(<?= __('Optional','aes'); ?>)</span></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_middle_last_name" id="account_middle_last_name" autocomplete="family-name" value="<?php echo esc_attr( $student->middle_last_name ); ?>" disabled/>
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_middle_last_name" id="account_middle_last_name" autocomplete="family-name" value="<?php echo esc_attr( $student->middle_last_name ); ?>" disabled/>
		<?php endif; ?>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-first">
		<label for="birth_date"><?php esc_html_e( 'Birth Date', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="birth_date" id="birth_date" value="<?= $student->birth_date; ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="birth_date" id="birth_date" value="<?= $student->birth_date; ?>" disabled/>
		<?php endif; ?>
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Gender', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<select style=" background-color: #e0e7ff !important" style="height:43.75px;padding:5px !important;" name="gender" disabled>
				<option value=""></option>
				<option value="female" <?= ($student->gender == 'female') ? 'selected' : ''; ?>><?= __('Female','aes'); ?></option>
				<option value="male" <?= ($student->gender == 'male') ? 'selected' : ''; ?>><?= __('Male','aes'); ?></option>
			</select>
		<?php else: ?>
			<select style=" background-color: #e0e7ff !important" style="height:43.75px;padding:5px !important;" name="gender" disabled>
				<option value=""></option>
				<option value="female" <?= ($student->gender == 'female') ? 'selected' : ''; ?>><?= __('Female','aes'); ?></option>
				<option value="male" <?= ($student->gender == 'male') ? 'selected' : ''; ?>><?= __('Male','aes'); ?></option>
			</select>
		<?php endif; ?>
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $student->email ); ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $student->email ); ?>" disabled/>
		<?php endif; ?>
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
		<label for="number_phone"><?php esc_html_e( 'Phone', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="number_phone" id="number_phone_account"  value="<?= $student->phone; ?>" disabled />
			<input style=" background-color: #e0e7ff !important" type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="number_phone_hidden" id="number_phone_hidden"  value="<?= $student->phone; ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="number_phone" id="number_phone_account"  value="<?= $student->phone; ?>" disabled/>
		<?php endif; ?>
	</p>

	<div class="clear"></div>
	<?php $countries = get_countries(); ?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="account_email"><?php esc_html_e('Country', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<select style="height:43.75px;padding:5px !important; background-color: #e0e7ff !important" " name="country" disabled>
				<?php foreach($countries as $key => $country): ?>
					<?php if($student->country == $key): ?>
						<option value='<?= $key ?>' selected><?= $country ?></option>
					<?php else: ?>
						<option value='<?= $key ?>'><?= $country ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		<?php else: ?>
			<select style="height:43.75px;padding:5px !important; background-color: #e0e7ff !important" " name="country" disabled>
				<?php foreach($countries as $key => $country): ?>
					<?php if($student->country == $key): ?>
						<option value='<?= $key ?>' selected><?= $country ?></option>
					<?php else: ?>
						<option value='<?= $key ?>'><?= $country ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
		<label for="account_email"><?php esc_html_e('City', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>			
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="city"  value="<?= $student->city; ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="city"  value="<?= $student->city; ?>" disabled/>
		<?php endif; ?>
	</p>

	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<label for="billing_postcode"><?php esc_html_e('Postal Code', 'aes' ); ?></label>
		<?php if($student->status_id < 2): ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="postal_code"  value="<?= $student->postal_code; ?>" disabled />
		<?php else: ?>
			<input style=" background-color: #e0e7ff !important" type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="postal_code"  value="<?= $student->postal_code; ?>" disabled />
		<?php endif; ?>
	</p>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<?php if($student->status_id < 2): ?>
			<!-- <button style="margin-top:20px;" type="submit" class="button-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'aes' ); ?></button>
			<input type="hidden" name="action" value="save_student_details" /> -->
		<?php else: ?>
			<!-- <button style="margin-top:20px;" type="submit" class="button-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" disabled><?php esc_html_e( 'Save changes', 'aes' ); ?></button> -->
		<?php endif; ?>
	</p>
	<!--
	<?php if($student->status_id == 2 && !empty($student->moodle_student_id)): ?>
		<p>
			<h2 style="margin-top:1rem;"><?= __('Virtual Classroom Information','aes'); ?></h2>
		</p>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
			<label for="billing_postcode"><?php esc_html_e('Username', 'aes' ); ?></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="moodle_password"  value="<?= $student->email; ?>" disabled/>
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
			<label for="billing_postcode"><?php esc_html_e('Password', 'aes' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="password"  value="<?= $student->moodle_password; ?>" disabled />
		</p>
		<p>
			<button style="width:100%;margin-top:20px;" type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'aes' ); ?></button>
			<input type="hidden" name="action" value="save_password_moodle" />
		</p>
	<?php endif; ?>
	-->
</form>