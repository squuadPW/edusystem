<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?= __( 'I\'m very sorry to hear that! The registration for your institution has been rejected. I understand this is disappointing news, and I want to assure you I\'m here to help in any way I can. Unfortunately, I don\'t have enough information to tell you exactly why your application was rejected. Reasons for rejection can vary depending on the institution and the jurisdiction you\'re located in.', 'edusystem' ); ?></p>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );