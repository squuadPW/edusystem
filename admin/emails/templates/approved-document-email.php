<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'I am writing to inform you that your document has been approved. We have carefully reviewed your document and believe that it meets all of our requirements. ', 'aes' )); ?></p>

<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

?>