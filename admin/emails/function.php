<?php 

function add_expedited_order_woocommerce_email( $email_classes ){

    require plugin_dir_path( __FILE__ ) . 'class-wc-new-applicant-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-welcome-student-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-update-document-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-approved-document-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-rejected-document-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-registered-institution-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-approved-institution-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-rejected-institution-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-registered-partner-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-approved-partner-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-rejected-partner-email.php';
    require plugin_dir_path( __FILE__ ) . 'class-wc-request-documents-email.php';

    $email_classes['WC_New_Applicant_Email'] = new WC_New_Applicant_Email();
    $email_classes['WC_Welcome_Student_Email'] = new WC_Welcome_Student_Email();
    $email_classes['WC_Request_Documents_Email'] = new WC_Request_Documents_Email();
    $email_classes['WC_Update_Document_Email'] = new WC_Update_Document_Email();
    $email_classes['WC_Approved_Document_Email'] = new WC_Approved_Document_Email();
    $email_classes['WC_Rejected_Document_Email'] = new WC_Rejected_Document_Email();
    $email_classes['WC_Registered_Institution_Email'] = new WC_Registered_Institution_Email();
    $email_classes['WC_Approved_Institution_Email'] = new WC_Approved_Institution_Email();
    $email_classes['WC_Rejected_Institution_Email'] = new WC_Rejected_Institution_Email();
    $email_classes['WC_Registered_Partner_Email'] = new WC_Registered_Partner_Email();
    $email_classes['WC_Approved_Partner_Email'] = new WC_Approved_Partner_Email();
    $email_classes['WC_Rejected_Partner_Email'] = new WC_Rejected_Partner_Email();
    return $email_classes;
}

add_filter( 'woocommerce_email_classes', 'add_expedited_order_woocommerce_email' );

function change_email_heading_order($email_heading, $order ){
    $email_heading = __('New Payment has been received','aes'); 
    return $email_heading;
} 

add_filter('woocommerce_email_heading_new_order','change_email_heading_order',10,2);
add_filter('woocommerce_email_heading_customer_processing_order','change_email_heading_order',1,2);

function change_email_heading_order_completed($email_heading, $order ){
    $email_heading = __('Your Payment has been completed','aes'); 
    return $email_heading;
}


add_filter('woocommerce_email_heading_customer_completed_order','change_email_heading_order_completed',10,2);


function change_admin_email_subject_new_order($subject, $order ){
    $subject = __('New Payment','aes');
    return $subject;
}


add_filter('woocommerce_email_subject_new_order', 'change_admin_email_subject_new_order',1, 2);
