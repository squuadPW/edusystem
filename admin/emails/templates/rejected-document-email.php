<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p>We inform you that the document <?= $document->document_id ?> has been rejected, we invite you to enter the platform to the notification area for more details.</p>

    <p><?= $description ?></p>

<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

?>