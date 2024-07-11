<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom Expedited Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Rejected_Institution_Email extends WC_Email {

    public function __construct() {

        $this->id = 'wc_rejected_institution';
        $this->title = __('Rejected Institution','aes');
        $this->description = __('Institution rejection notification emails are produced when an institution is rejected for approval.','aes');

        $this->heading = __('Rejected Institution','aes');
        $this->subject = __('Rejected Institution','aes');
        $this->customer_email = true;
        $this->manual = true;
        $this->template_html  = 'templates/rejected-institution-email.php';
        $this->template_plain = 'templates/rejected-institution-email.php';
        $this->template_base  = plugin_dir_path( __FILE__ );

        parent::__construct();
    }

    public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'institution'			=> $this->object,
			'email_heading'			=> $this->heading,
			'sent_to_admin'			=> false,
			'plain_text'			=> false,
			'email'					=> $this
		), $this->template_base, $this->template_base);
	}

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1
     * @param int $order_id
     */
    public function trigger($institute_id){

        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institutes';

        $this->object = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
        $this->recipient = $this->object->email;
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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