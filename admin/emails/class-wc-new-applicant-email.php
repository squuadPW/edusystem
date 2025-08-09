<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class WC_New_Applicant_Email extends WC_Email {

    public function __construct() {

        $this->id = 'wc_new_student';
        $this->title = __('New Applicant Registered','edusystem');
        $this->description = __('Student notification emails are sent when a student registers after making a payment.','edusystem');

        $this->heading = __('New Applicant Registered','edusystem');
        $this->subject = __('New Applicant Registered','edusystem');

        $this->template_html  = 'templates/new-applicant-email.php';
        $this->template_plain = 'templates/new-applicant-email.php';
        $this->template_base  = plugin_dir_path( __FILE__ );

        parent::__construct();

        $this->recipient = $this->get_option( 'recipient' );

        if(!$this->recipient){
            $this->recipient = get_option( 'admin_email' );
        }
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1
     * @param int $order_id
     */
    public function trigger($student_id){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';

        $this->object = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'student'				=> $this->object,
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
            'recipient'  => array(
                'title'       => 'Recipient(s)',
                'type'        => 'text',
                'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
                'placeholder' => '',
                'default'     => ''
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