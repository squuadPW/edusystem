<?php
/*
Plugin Name: AES
Plugin URI: https://americanelite.dreamhosters.com/wp-admin/plugins.php
Description: Plugin for AES
Version:01
Author: AES
Author URI:https://americanelite.dreamhosters.com/wp-admin/plugins.php
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  form-plugin
*/

if(!class_exists('WP_List_Table')){
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require plugin_dir_path(__FILE__).'settings.php';
require plugin_dir_path(__FILE__).'public/functions.php';
require plugin_dir_path(__FILE__).'admin/functions.php';
require_once(ABSPATH. 'wp-admin/includes/upgrade.php');

function create_tables() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_departments = $wpdb->prefix. 'departments';
  $table_student_payments = $wpdb->prefix.'student_payments';
  $table_students = $wpdb->prefix.'students';
  $table_student_documents = $wpdb->prefix.'student_documents';
  $table_institutes =  $wpdb->prefix.'institutes';
  $table_alliances =  $wpdb->prefix.'alliances';
  $table_grades = $wpdb->prefix.'grades';
  $table_documents = $wpdb->prefix.'documents'; 
  $table_student_payments = $wpdb->prefix.'student_payments';
  $table_pre_users = $wpdb->prefix.'pre_users';
  $table_pre_students = $wpdb->prefix.'pre_students';
  $table_student_scholarship_application = $wpdb->prefix.'student_scholarship_application';
  $table_academic_periods = $wpdb->prefix.'academic_periods';

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_academic_periods}'") != $table_academic_periods){

    dbDelta( "CREATE TABLE " . $table_academic_periods . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        code TEXT NOT NULL,
        status_id INT(11) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_pre_users}'") != $table_pre_users){

    dbDelta( "CREATE TABLE " . $table_pre_users . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NULL,
        partner_id INT(11) NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        type TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_pre_students}'") != $table_pre_students){

    dbDelta( "CREATE TABLE " . $table_pre_students . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        gender TEXT NULL,
        country TEXT NULL,
        city TEXT NULL,
        postal_code TEXT NULL,
        grade_id INT(11) NOT NULL,
        name_institute TEXT NOT NULL,
        institute_id INT(11) NULL,
        program_id TEXT NOT NULL,
        partner_id INT(11) NOT NULL, 
        status_id INT(11) NOT NULL,
        moodle_student_id INT(11) NULL,
        moodle_password TEXT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_student_scholarship_application}'") != $table_student_scholarship_application){

    dbDelta( "CREATE TABLE " . $table_student_scholarship_application . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        partner_id INT(11) NOT NULL,
        status_id INT(11) NOT NULL,
        from_date DATE NULL,
        until_date DATE NULL,
        description TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_departments}'") != $table_departments){

    dbDelta( "CREATE TABLE " . $table_departments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_student_payments}'") != $table_student_payments){

    dbDelta( "CREATE TABLE " . $table_student_payments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        status_id INT(11) NOT NULL,
        order_id INT(11) NOT NULL,
        student_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        amount DOUBLE(10, 2) NOT NULL,
        type_payment INT(11) NOT NULL,
        cuote INT(11) NOT NULL,
        num_cuotes INT(11) NOT NULL,
        date_payment DATE NOT NULL,
        date_next_payment DATE NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }
 
  if($wpdb->get_var("SHOW TABLES LIKE '{$table_students}'") != $table_students){

    dbDelta( "CREATE TABLE " . $table_students . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        gender TEXT NULL,
        country TEXT NULL,
        city TEXT NULL,
        postal_code TEXT NULL,
        grade_id INT(11) NOT NULL,
        name_institute TEXT NOT NULL,
        institute_id INT(11) NULL,
        program_id TEXT NOT NULL,
        partner_id INT(11) NOT NULL, 
        status_id INT(11) NOT NULL,
        moodle_student_id INT(11) NULL,
        moodle_password TEXT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_student_documents}'") != $table_student_documents){

    dbDelta( "CREATE TABLE " . $table_student_documents . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        document_id TEXT NOT NULL,
        attachment_id BIGINT NOT NULL,
        status INT(11) NOT NULL,
        is_required INT(11) NOT NULL DEFAULT 0,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  
  if($wpdb->get_var("SHOW TABLES LIKE '{$table_institutes}'") != $table_institutes){

    dbDelta( "CREATE TABLE " . $table_institutes . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        country TEXT NOT NULL,
        state TEXT NOT NULL,
        city TEXT NOT NULL,
        address TEXT NOT NULL,
        level_id INT(11) NOT NULL,
        name_rector TEXT NOT NULL,
        lastname_rector TEXT NOT NULL,
        phone_rector TEXT NOT NULL,
        reference TEXT NOT NULL,
        status INT(11) NOT NULL,
        alliance_id INT(11) NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        fee float NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }


  if($wpdb->get_var("SHOW TABLES LIKE '{$table_alliances}'") != $table_alliances){

      dbDelta( "CREATE TABLE " . $table_alliances . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        code TEXT NULL,
        name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        name_legal TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        country TEXT NOT NULL,
        state TEXT NOT NULL,
        city TEXT NOT NULL,
        address TEXT NOT NULL,
        type INT(11) NULL,
        status INT(11) NOT NULL,
        fee float NOT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
      );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_grades}'") != $table_grades){
      
    dbDelta( "CREATE TABLE " . $table_grades . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );


    $wpdb->insert($table_grades,[
      'name' => '9no (antepenultimate)',
      'created_at' => date('Y-m-d H:i:s') 
    ]);

    $wpdb->insert($table_grades,[
      'name' => '10mo (penultimate)',
      'created_at' => date('Y-m-d H:i:s') 
    ]);

    $wpdb->insert($table_grades,[
      'name' => '11vo (last)',
      'created_at' => date('Y-m-d H:i:s') 
    ]);

    $wpdb->insert($table_grades,[
      'name' => 'Bachelor (graduate)',
      'created_at' => date('Y-m-d H:i:s') 
    ]);
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_documents}'") != $table_documents){
      
    dbDelta( "CREATE TABLE " . $table_documents . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NOT NULL,
      grade_id INT(11) NOT NULL,
      is_required INT(11) NOT NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );

    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");

    if(!empty($grades)){

        foreach($grades as $grade){

            $wpdb->insert($table_documents,[
              'name' => 'CERTIFIED NOTES HIGH SCHOOL',
              'grade_id' => $grade->id,
              'is_required' => 0,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'HIGH SCHOOL DIPLOMA',
              'grade_id' => $grade->id,
              'is_required' => 0,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'ID OR CI OF THE PARENTS',
              'grade_id' => $grade->id,
              'is_required' => 1,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'ID STUDENTS',
              'grade_id' => $grade->id,
              'is_required' => 1,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'PHOTO OF STUDENT CARD',
              'grade_id' => $grade->id,
              'is_required' => 1,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'PROOF OF GRADE',
              'grade_id' => $grade->id,
              'is_required' => 0,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'PROOF OF STUDY',
              'grade_id' => $grade->id,
              'is_required' => 0,
              'created_at' => date('Y-m-d H:i:s') 
            ]);

            $wpdb->insert($table_documents,[
              'name' => 'VACCUNATION CARD',
              'grade_id' => $grade->id,
              'is_required' => 1,
              'created_at' => date('Y-m-d H:i:s') 
            ]);
        } 
    }
  }
}

register_activation_hook(__FILE__, 'create_tables');

