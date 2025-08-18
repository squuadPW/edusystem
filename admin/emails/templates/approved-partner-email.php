<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?= __('Congratulations. I am pleased to inform you that your alliance registration has been approved. This means that you have successfully completed the registration process and your alliance is now officially recognized.', 'edusystem' ); ?></p>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );