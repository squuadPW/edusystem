<?php

require plugin_dir_path( __FILE__ ) . 'roles.php';
require plugin_dir_path( __FILE__ ) . 'admission.php';
require plugin_dir_path( __FILE__ ) . 'payments.php';
require plugin_dir_path( __FILE__ ) . 'departments.php';
require plugin_dir_path( __FILE__ ) . 'bitrix/sdk/crest.php';

function add_custom_admin_page() {
  
    add_menu_page( 
      __('Departments','restaurant-system-app'),
      __('Departments','restaurant-system-app'),
      'read', 
      'list_admin_form_department_content',
      'list_admin_form_department_content', 
      'dashicons-admin-network',
      10
    );
  
    add_submenu_page(
      'list_admin_form_department_content',
      'Add new department',
      'Add new department',
      'manage_options',
      'add_admin_form_department_content',
      'add_admin_form_department_content' 
    );

    add_menu_page( 
        __('Admission','restaurant-system-app'),
        __('Admission','restaurant-system-app'),
        'read', 
        'add_admin_form_admission_content',
        'add_admin_form_admission_content', 
        'dashicons-groups', 
        7
    );

    add_menu_page( 
        __('Payments','restaurant-system-app'),
        __('Payments','restaurant-system-app'),
        'read', 
        'add_admin_form_payments_content',
        'add_admin_form_payments_content', 
        'dashicons-money-alt', 
        7
    );
  
  }
  
add_action('admin_menu', 'add_custom_admin_page');