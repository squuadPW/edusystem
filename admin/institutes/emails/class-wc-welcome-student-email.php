<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class WC_Welcome_Student_Email extends WC_Email {

    public function __construct() {

        $this->id = 'wc_welcome-student';
        $this->title = __('Welcome','edusystem');
        $this->description = __('Welcome students.','edusystem');

        $this->heading = __('Welcome','edusystem');
        $this->subject = __('Welcome','edusystem');
        $this->customer_email = true;
        $this->manual = true;
        $this->template_html  = 'templates/welcome-student-email.php';
        $this->template_plain = 'templates/welcome-student-email.php';
        $this->template_base  = plugin_dir_path( __FILE__ );

        parent::__construct();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1
     * @param int $order_id
     */
    public function trigger($student_id, $reset_url, $copy_parent = 0){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';

        $this->student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        if ($copy_parent == 1) {
            $parent = get_user_by('id', $this->student->partner_id);
            if($parent->user_email != $this->student->email) {
                $this->recipient = $parent->user_email;
                $this->reset_url = $reset_url;
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            }

            // para no duplicar su envio, si es mayor de edad
        } else {
            $this->recipient = $this->student->email;
            $this->reset_url = $reset_url;
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
    }

    public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'student'				=> $this->student,
			'reset_url'				=> $this->reset_url,
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