<?php
/*
Plugin Name: Squuad for educational system
Plugin URI: https://online.american-elite.us/wp-admin/plugins.php
Description: The WordPress plugin for educational system is a customized tool that offers a range of functionalities for the proper functioning of the institute website
Version: 1.3.57
Author: Squuad
Author URI: https://online.american-elite.us/wp-admin/plugins.php
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  form-plugin
*/

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require plugin_dir_path(__FILE__) . 'settings.php';
require plugin_dir_path(__FILE__) . 'public/functions.php';
require plugin_dir_path(__FILE__) . 'admin/functions.php';
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

function create_tables()
{
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_departments = $wpdb->prefix . 'departments';
  $table_student_payments = $wpdb->prefix . 'student_payments';
  $table_students = $wpdb->prefix . 'students';
  $table_student_documents = $wpdb->prefix . 'student_documents';
  $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
  $table_student_califications = $wpdb->prefix . 'student_califications';
  $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
  $table_academic_projection_base = $wpdb->prefix . 'academic_projection_base';
  $table_school_subject_matrix_regular = $wpdb->prefix . 'school_subject_matrix_regular';
  $table_school_subject_matrix_elective = $wpdb->prefix . 'school_subject_matrix_elective';
  $table_institutes = $wpdb->prefix . 'institutes';
  $table_alliances = $wpdb->prefix . 'alliances';
  $table_grades = $wpdb->prefix . 'grades';
  $table_documents = $wpdb->prefix . 'documents';
  $table_documents_for_teachers = $wpdb->prefix . 'documents_for_teachers';
  $table_pre_users = $wpdb->prefix . 'pre_users';
  $table_pre_students = $wpdb->prefix . 'pre_students';
  $table_student_scholarship_application = $wpdb->prefix . 'student_scholarship_application';
  $table_academic_periods = $wpdb->prefix . 'academic_periods';
  $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
  $table_users_signatures = $wpdb->prefix . 'users_signatures';
  $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
  $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
  $table_user_notices = $wpdb->prefix . 'users_notices';
  $table_tickets_created = $wpdb->prefix . 'tickets_created';
  $table_school_subjects = $wpdb->prefix . 'school_subjects';
  $table_teachers = $wpdb->prefix . 'teachers';
  $table_teacher_documents = $wpdb->prefix . 'teacher_documents';

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_academic_projection}'") != $table_student_academic_projection) {
    dbDelta(
      "CREATE TABLE " . $table_student_academic_projection . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        projection JSON NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_califications}'") != $table_student_califications) {
    dbDelta(
      "CREATE TABLE " . $table_student_califications . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        code_subject TEXT NULL,
        code_period INT(11) NOT NULL,
        cut_period TEXT NOT NULL,
        calification TEXT NOT NULL,
        max_calification TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_teachers}'") != $table_teachers) {
    dbDelta(
      "CREATE TABLE " . $table_teachers . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NULL,
        gender TEXT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NULL,
        status INT(1) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_school_subjects}'") != $table_school_subjects) {
    dbDelta(
      "CREATE TABLE " . $table_school_subjects . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        is_active BOOLEAN NOT NULL DEFAULT 1,
        code_subject TEXT NOT NULL,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        min_pass DOUBLE(10, 2) NOT NULL,
        hc INT(11) NOT NULL,
        max_students INT(11) NOT NULL,
        maxtrix_position INT(11) NOT NULL,
        moodle_course_id INT(11) NULL,
        teacher_id INT(11) NULL,
        is_elective BOOLEAN NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_tickets_created}'") != $table_tickets_created) {
    dbDelta(
      "CREATE TABLE " . $table_tickets_created . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        ticket_id INT(11) NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_period_inscriptions}'") != $table_student_period_inscriptions) {
    // status
    // 0 to begin (por iniciar)
    // 1 activo (activo, cursando actualmente)
    // 2 unsubscribed (se retiro)
    // 3 completed (completado)
    // 4 failed (reprobado)
    dbDelta(
      "CREATE TABLE " . $table_student_period_inscriptions . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        status_id INT(11) NOT NULL,
        type TEXT NULL,
        student_id INT(11) NOT NULL,
        subject_id INT(11) NULL,
        code_subject TEXT NULL,
        calification DOUBLE(10, 2) NULL,
        code_period INT(11) NOT NULL,
        cut_period TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_user_notices}'") != $table_user_notices) {
    dbDelta(
      "CREATE TABLE " . $table_user_notices . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        message LONGTEXT NOT NULL,
        `read` BOOLEAN NOT NULL DEFAULT 0,
        type_notice TEXT NOT NULL,
        importance INT(11) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_alliances_payments}'") != $table_alliances_payments) {
    dbDelta(
      "CREATE TABLE " . $table_alliances_payments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        alliance_id INT(11) NOT NULL,
        total_orders INT(11) NOT NULL,
        amount DOUBLE(10, 2) NOT NULL,
        status_id  INT(11) NOT NULL DEFAULT 0,
        month  DATE NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_institutes_payments}'") != $table_institutes_payments) {

    dbDelta(
      "CREATE TABLE " . $table_institutes_payments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        institute_id INT(11) NOT NULL,
        total_orders INT(11) NOT NULL,
        amount DOUBLE(10, 2) NOT NULL,
        status_id  INT(11) NOT NULL DEFAULT 0,
        month  DATE NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_users_signatures}'") != $table_users_signatures) {

    dbDelta(
      "CREATE TABLE " . $table_users_signatures . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id TEXT NOT NULL,
        signature LONGTEXT NOT NULL,
        document_id LONGTEXT NOT NULL,
        grade_selected TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_academic_periods}'") != $table_academic_periods) {

    dbDelta(
      "CREATE TABLE " . $table_academic_periods . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        code TEXT NOT NULL,
        code_next TEXT NOT NULL,
        year INT(11) NULL,
        start_date DATE NULL,
        end_date DATE NULL,
        start_date_inscription DATE NULL,
        end_date_inscription DATE NULL,
        start_date_pre_inscription DATE NULL,
        end_date_pre_inscription DATE NULL,
        status_id INT(11) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_academic_periods_cut}'") != $table_academic_periods_cut) {
    dbDelta(
      "CREATE TABLE " . $table_academic_periods_cut . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        code TEXT NOT NULL,
        cut TEXT NOT NULL,
        start_date DATE NULL,
        end_date DATE NULL,
        max_date DATE NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_pre_users}'") != $table_pre_users) {

    dbDelta(
      "CREATE TABLE " . $table_pre_users . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NULL,
        gender TEXT NULL,
        ethnicity TEXT NULL,
        partner_id INT(11) NULL,
        email TEXT NOT NULL,
        password TEXT NULL,
        is_parent BOOLEAN DEFAULT FALSE,
        phone TEXT NOT NULL,
        type TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_pre_students}'") != $table_pre_students) {

    dbDelta(
      "CREATE TABLE " . $table_pre_students . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        ethnicity TEXT NULL,
        academic_period TEXT NULL,
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

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_scholarship_application}'") != $table_student_scholarship_application) {

    dbDelta(
      "CREATE TABLE " . $table_student_scholarship_application . " (
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

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_departments}'") != $table_departments) {

    dbDelta(
      "CREATE TABLE " . $table_departments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_payments}'") != $table_student_payments) {

    dbDelta(
      "CREATE TABLE " . $table_student_payments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        status_id INT(11) NOT NULL,
        student_id INT(11) NOT NULL,
        order_id INT(11) NULL,
        product_id INT(11) NOT NULL,
        variation_id INT(11) NULL,
        amount DOUBLE(10, 2) NOT NULL,
        type_payment INT(11) NOT NULL,
        cuote INT(11) NOT NULL,
        num_cuotes INT(11) NOT NULL,
        date_payment DATE NULL,
        date_next_payment DATE NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_students}'") != $table_students) {

    // condition_student (0 retirado, 1 activo, 2 pausado)
    // status_id (0 pendiente, 1 aprobado, 2 documentos subidos con acceso al moodle)
    dbDelta(
      "CREATE TABLE " . $table_students . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        ethnicity TEXT NULL,
        academic_period TEXT NULL,
        initial_cut TEXT NULL,
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
        condition_student BOOLEAN NOT NULL DEFAULT 1,
        elective BOOLEAN NOT NULL DEFAULT 0,
        skip_cut BOOLEAN NOT NULL DEFAULT 0,
        moodle_student_id INT(11) NULL,
        moodle_password TEXT NULL,
        set_password BOOLEAN NOT NULL DEFAULT 0,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_student_documents}'") != $table_student_documents) {

    dbDelta(
      "CREATE TABLE " . $table_student_documents . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        document_id TEXT NOT NULL,
        attachment_id BIGINT NOT NULL,
        approved_by INT(11) NULL,
        status INT(11) NOT NULL,
        is_required INT(11) NOT NULL DEFAULT 0,
        is_visible BOOLEAN NOT NULL DEFAULT 1,
        upload_at DATETIME NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_teacher_documents}'") != $table_teacher_documents) {
    dbDelta(
      "CREATE TABLE " . $table_teacher_documents . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        teacher_id INT(11) NOT NULL,
        document_id TEXT NOT NULL,
        attachment_id BIGINT NOT NULL,
        approved_by INT(11) NULL,
        status INT(11) NOT NULL,
        description VARCHAR(255) NULL,
        is_required INT(11) NOT NULL DEFAULT 0,
        is_visible BOOLEAN NOT NULL DEFAULT 1,
        upload_at DATETIME NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }


  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_institutes}'") != $table_institutes) {

    dbDelta(
      "CREATE TABLE " . $table_institutes . " (
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
        name_contact TEXT NOT NULL,
        lastname_rector TEXT NOT NULL,
        lastname_contact TEXT NOT NULL,
        phone_rector TEXT NOT NULL,
        phone_contact TEXT NOT NULL,
        reference TEXT NOT NULL,
        status INT(11) NOT NULL,
        alliance_id INT(11) NULL,
        fee float NOT NULL,
        business_name TEXT NOT NULL,
        description TEXT NOT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }


  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_alliances}'") != $table_alliances) {

    dbDelta(
      "CREATE TABLE " . $table_alliances . " (
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
        description TEXT NOT NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_grades}'") != $table_grades) {

    dbDelta(
      "CREATE TABLE " . $table_grades . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NULL,
      description TEXT NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );


    $wpdb->insert($table_grades, [
      'name' => 'Lower',
      'description' => '(Antepenultimate)',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_grades, [
      'name' => 'Middle',
      'description' => '(Penultimate)',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_grades, [
      'name' => 'Upper',
      'description' => '(Last)',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_grades, [
      'name' => 'Graduate',
      'created_at' => date('Y-m-d H:i:s')
    ]);
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_documents}'") != $table_documents) {

    dbDelta(
      "CREATE TABLE " . $table_documents . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NOT NULL,
      type_file TEXT NOT NULL,
      grade_id INT(11) NOT NULL,
      is_required INT(11) NOT NULL,
      is_visible BOOLEAN NOT NULL DEFAULT 1,
      id_requisito TEXT NOT NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );

    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");

    if (!empty($grades)) {

      foreach ($grades as $grade) {

        $wpdb->insert($table_documents, [
          'name' => 'CERTIFIED NOTES HIGH SCHOOL',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'id_requisito' => 'NC',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'HIGH SCHOOL DIPLOMA',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'id_requisito' => 'TB',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'ID OR CI OF THE PARENTS',
          'type_file' => '.pdf, .png, .jpeg',
          'grade_id' => $grade->id,
          'is_required' => 1,
          'id_requisito' => 'IR',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'ID STUDENTS',
          'type_file' => '.pdf, .png, .jpeg',
          'grade_id' => $grade->id,
          'is_required' => 1,
          'id_requisito' => 'ID',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'STUDENT\'S PHOTO',
          'type_file' => '.pdf, .png, .jpeg',
          'grade_id' => $grade->id,
          'is_required' => 1,
          'id_requisito' => 'FP',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'PROOF OF GRADE',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'id_requisito' => 'PG',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'PROOF OF STUDY',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'id_requisito' => 'PS',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'VACCINATION CARD',
          'type_file' => '.pdf, .png, .jpeg',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'id_requisito' => 'TV',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'ENROLLMENT',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 1,
          'is_visible' => 0,
          'id_requisito' => 'ENROLLMENT',
          'created_at' => date('Y-m-d H:i:s')
        ]);

        $wpdb->insert($table_documents, [
          'name' => 'MISSING DOCUMENT',
          'type_file' => '.pdf',
          'grade_id' => $grade->id,
          'is_required' => 0,
          'is_visible' => 0,
          'id_requisito' => 'CC',
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }
    }
  }


  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_documents_for_teachers}'") != $table_documents_for_teachers) {
    dbDelta(
      "CREATE TABLE " . $table_documents_for_teachers . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NOT NULL,
      type_file TEXT NOT NULL,
      is_required INT(11) NOT NULL,
      is_visible BOOLEAN NOT NULL DEFAULT 1,
      id_requisito TEXT NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );

    $wpdb->insert($table_documents_for_teachers, [
      'name' => 'PHOTO',
      'type_file' => '.jpeg, .png, .jpg',
      'is_required' => 1,
      'is_visible' => 1,
      'id_requisito' => '',
      'created_at' => date('Y-m-d H:i:s')
    ]);


    $wpdb->insert($table_documents_for_teachers, [
      'name' => 'FORM 402',
      'type_file' => '.pdf',
      'is_required' => 1,
      'is_visible' => 1,
      'id_requisito' => '',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_documents_for_teachers, [
      'name' => 'DIGITAL COPY OF UNDERGRADUATE DEGREE',
      'type_file' => '.pdf',
      'is_required' => 1,
      'is_visible' => 1,
      'id_requisito' => '',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_documents_for_teachers, [
      'name' => 'DIGITAL COPY OF THE GRADUATE DEGREE',
      'type_file' => '.pdf',
      'is_required' => 1,
      'is_visible' => 1,
      'id_requisito' => '',
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_documents_for_teachers, [
      'name' => 'CURRICULAR SUMMARY',
      'type_file' => '.pdf',
      'is_required' => 1,
      'is_visible' => 1,
      'id_requisito' => '',
      'created_at' => date('Y-m-d H:i:s')
    ]);
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_academic_projection_base}'") != $table_academic_projection_base) {
    dbDelta(
      "CREATE TABLE " . $table_academic_projection_base . " (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name TEXT NULL,
      projection JSON NULL,
      updated_at DATETIME NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id))$charset_collate;"
    );

    $lower = [
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. History",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "U.S. Government",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "English III",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "Economic & financial literacy",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "English IV",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "Pre-calculus",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
    ];
    $middle = [
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. History",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "regular",
        "name" => "Economic & financial literacy",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "U.S. Government",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "English III",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "regular",
        "name" => "English IV",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "Pre-calculus",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
    ];
    $upper = [
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. History",
        "code" => ""
      ],
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. Government",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "regular",
        "name" => "English III",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "Economic & financial literacy",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "regular",
        "name" => "English IV",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "regular",
        "name" => "Pre-calculus",
        "code" => ""
      ],
    ];
    $graduated = [
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. History",
        "code" => ""
      ],
      [
        "cut" => "A",
        "type" => "regular",
        "name" => "U.S. Government",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "regular",
        "name" => "English III",
        "code" => ""
      ],
      [
        "cut" => "B",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "regular",
        "name" => "Economic & financial literacy",
        "code" => ""
      ],
      [
        "cut" => "C",
        "type" => "elective",
        "name" => "",
        "code" => ""
      ],
      [
        "cut" => "D",
        "type" => "regular",
        "name" => "English IV",
        "code" => ""
      ],
      [
        "cut" => "E",
        "type" => "regular",
        "name" => "Pre-calculus",
        "code" => ""
      ],
    ];

    $wpdb->insert($table_academic_projection_base, [
      'name' => 'Lower',
      'projection' => json_encode($lower),
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_academic_projection_base, [
      'name' => 'Middle',
      'projection' => json_encode($middle),
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_academic_projection_base, [
      'name' => 'Upper',
      'projection' => json_encode($upper),
      'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_academic_projection_base, [
      'name' => 'Graduated',
      'projection' => json_encode($graduated),
      'created_at' => date('Y-m-d H:i:s')
    ]);
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_school_subject_matrix_regular}'") != $table_school_subject_matrix_regular) {
    dbDelta(
      "CREATE TABLE " . $table_school_subject_matrix_regular . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        subject TEXT NOT NULL,
        subject_id INT(11) NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_school_subject_matrix_elective}'") != $table_school_subject_matrix_elective) {
    dbDelta(
      "CREATE TABLE " . $table_school_subject_matrix_elective . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        subject TEXT NOT NULL,
        subject_id INT(11) NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );
  }

}

register_activation_hook(__FILE__, 'create_tables');

