<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom Expedited Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Rejected_Document_Email extends WC_Email {

    public function __construct() {

        $this->id = 'wc_rejected_document';
        $this->title = __('Rejected Document','aes');
        $this->description = __('Document rejection notification emails are produced when a student\'s document is rejected from admissions.','aes');
        $this->customer_email = true;
        $this->manual = true;
        $this->heading = __('Rejected Document','aes');
        $this->subject = __('Rejected Document','aes');

        $this->template_html  = 'templates/rejected-document-email.php';
        $this->template_plain = 'templates/rejected-document-email.php';
        $this->template_base  = plugin_dir_path( __FILE__ );
        parent::__construct();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1
     * @param int $order_id
     */
    public function trigger($student_id,$document_id, $parent = false){

        global $wpdb;
        $table_student_documents = $wpdb->prefix.'student_documents';
        $table_students = $wpdb->prefix.'students';
        $this->object = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE id={$document_id}");
        $this->student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        if ($parent) {
            $user_parent = get_user_by('id', $this->student->partner_id);
            $this->recipient = $user_parent->user_email;
        } else {
            $this->recipient = $this->student->email;
        }
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'document'			    => $this->object,
            'student'               => $this->student,
			'email_heading'			=> $this->heading,
			'sent_to_admin'			=> false,
			'plain_text'			=> false,
			'email'					=> $this
		), $this->template_base, $this->template_base);
	}

    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => 'Choose which format of email to send.',
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => 'Plain text',
                    'html'      => 'HTML', 'woocommerce',
                    'multipart' => 'Multipart', 'woocommerce',
                )
            )
        );
    }
}