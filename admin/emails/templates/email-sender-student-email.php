<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    
<?php
// Email template
$email_template = '
<html>
  <body>';
    // if ($email_heading != 'Welcome') {
    //   $email_template .= '<p>Dear ' . $student->name . ' ' . $student->last_name . ',</p>';
    // }
    
    $email_template .= '<div>'.$description.'</div>
  </body>
</html>';

// Output the email template
echo $email_template;

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

