<?php
/*
Plugin Name: AES
Plugin URI: https://americanelite.dreamhosters.com/wp-admin/plugins.php
Description: Plugin for test
Version:01
Author:Jose Mora
Author URI:https://americanelite.dreamhosters.com/wp-admin/plugins.php
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  form-plugin
*/

if(!class_exists('WP_List_Table')){
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

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

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_departments}'") != $table_departments){

    dbDelta( "CREATE TABLE " . $table_departments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
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
        last_name TEXT NOT NULL,
        birth_date DATE NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        gender TEXT NULL,
        country TEXT NULL,
        city TEXT NULL,
        post_code TEXT NULL,
        grade_id INT(11) NOT NULL,
        name_institute TEXT NOT NULL,
        program_id TEXT NOT NULL,
        partner_id INT(11) NOT NULL, 
        status_id INT(11) NOT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if($wpdb->get_var("SHOW TABLES LIKE '{$table_student_documents}'") != $table_student_documents){

    dbDelta( "CREATE TABLE " . $table_student_documents . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        document_id INT(11) NULL,
        status INT(11) NOT NULL,
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
        PRIMARY KEY (id))$charset_collate;"
    );
  }


  if($wpdb->get_var("SHOW TABLES LIKE '{$table_alliances}'") != $table_alliances){

      dbDelta( "CREATE TABLE " . $table_alliances . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
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
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
      );
  }
}

register_activation_hook(__FILE__, 'create_tables');

