<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?= __('Unfortunately, I must inform you that your application for registration has been rejected. I understand that this may be discouraging news, and I want to assure you that I am here to help you in any way I can.', 'edusystem' ); ?></p>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );