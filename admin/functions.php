<?php

require plugin_dir_path( __FILE__ ) . 'roles.php';
require plugin_dir_path( __FILE__ ) . 'admission.php';
require plugin_dir_path( __FILE__ ) . 'payments.php';
require plugin_dir_path( __FILE__ ) . 'institute.php';
require plugin_dir_path( __FILE__ ) . 'bitrix/sdk/crest.php';

include(plugin_dir_path(__FILE__).'templates/register-departments.php');
include(plugin_dir_path(__FILE__).'templates/list-departments.php');

function aes_scripts_admin(){
    wp_enqueue_style('flatpickr',plugins_url('aes').'/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('style-admin',plugins_url('aes').'/admin/assets/css/style.css');
    wp_enqueue_script('xlsx-js',plugins_url('aes').'/admin/assets/js/xlsx.full.min.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('admin-flatpickr',plugins_url('aes').'/public/assets/js/flatpickr.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('student-documents',plugins_url('aes').'/admin/assets/js/document.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('student-payment',plugins_url('aes').'/admin/assets/js/payment.js',array('jquery'),'1.0.0',true);

    wp_localize_script('student-documents','update_status_documents',[
        'url' => admin_url( 'admin-ajax.php' ),
        'action' => 'update_status_documents' 
    ]);

    wp_localize_script('student-documents','get_student_details',[
        'url' => admin_url( 'admin-ajax.php' ),
        'action' => 'update_status_documents' 
    ]);

    wp_localize_script('student-documents','get_student_details',[
        'url' => admin_url( 'admin-ajax.php' ),
        'action' => 'get_student_details' 
    ]);
}

add_action( 'admin_enqueue_scripts', 'aes_scripts_admin',3 );

function add_custom_admin_page() {
  
    add_menu_page(
        __('All departments','aes'),
        __('Departments','aes'),
        'manage_options',
        'list-departments',
        'list_departments_admin_page_callback', 
        'dashicons-admin-network',
        10 
    );
  
    add_submenu_page(
      'list-departments',
      __('Add new department','aes'),
      __('Add new department','aes'),
      'manage_options',
      'register-department',
      'register_departments_admin_page_callback' 
    );

    add_menu_page( 
        __('Admission','aes'),
        __('Admission','aes'),
        'read', 
        'add_admin_form_admission_content',
        'add_admin_form_admission_content', 
        'dashicons-groups', 
        7
    );

    add_menu_page( 
        __('Payments','aes'),
        __('Payments','aes'),
        'read', 
        'add_admin_form_payments_content',
        'add_admin_form_payments_content', 
        'dashicons-money-alt', 
        7
    );

    add_menu_page( 
        __('Institutes','aes'),
        __('Institutes','aes'),
        'read', 
        'add_admin_institutes_content',
        'add_admin_institutes_content', 
        ' dashicons-bank', 
        7
    );
  
}
  
add_action('admin_menu', 'add_custom_admin_page');

function list_departments_admin_page_callback() {
    echo do_shortcode( '[list_departments]' );
  }
  
  function register_departments_admin_page_callback() {
    echo do_shortcode( '[form_departaments]' );
  }
  