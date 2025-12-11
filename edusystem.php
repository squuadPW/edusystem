<?php
/*
Plugin Name: EduSystem
Description: Transform your WordPress into a complete, professional and scalable educational ecosystem.
Version: 3.9.5
Author: EduSof
Author URI: https://edusof.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  edusystem
*/


// Definición de constantes del plugin
define('EDUSYSTEM__FILE__', __FILE__); // ruta __FILE__
define('EDUSYSTEM_PATH', plugin_dir_path(__FILE__) ); // Ruta del directorio del plugin
define('EDUSYSTEM_URL', plugin_dir_url(__FILE__)); // URL del plugin

// funciones de comisiones
include_once( plugin_dir_path(__FILE__).'payment_method_fees/payment_method_fees.php' );

// funciones de edusystem_log
include_once( plugin_dir_path(EDUSYSTEM__FILE__).'edusystem_log/edusystem_log.php' );

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require plugin_dir_path(__FILE__) . 'settings.php';
require plugin_dir_path(__FILE__) . 'public/functions.php';
require plugin_dir_path(__FILE__) . 'admin/functions.php';
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

add_action( 'plugins_loaded', 'update_language' );
function update_language() {
    load_plugin_textdomain( 'edusystem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_departments = $wpdb->prefix . 'departments';
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_student_payments_log = $wpdb->prefix . 'student_payments_log';
    $table_students = $wpdb->prefix . 'students';
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_student_califications = $wpdb->prefix . 'student_califications';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
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
    $table_academic_offers = $wpdb->prefix . 'academic_offers';
    $table_teachers = $wpdb->prefix . 'teachers';
    $table_teacher_documents = $wpdb->prefix . 'teacher_documents';
    $table_pre_scholarship = $wpdb->prefix . 'pre_scholarship';
    $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
    $table_count_pending_student = $wpdb->prefix . 'count_pending_student';
    $table_requests = $wpdb->prefix . 'requests';
    $table_type_requests = $wpdb->prefix . 'type_requests';
    $table_expected_matrix = $wpdb->prefix . 'expected_matrix';
    $table_expected_matrix_school = $wpdb->prefix . 'expected_matrix_school';
    $table_pensum = $wpdb->prefix . 'pensum';
    $table_feed = $wpdb->prefix . 'feed';
    $table_dynamic_links = $wpdb->prefix . 'dynamic_links';
    $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
    $table_templates_email = $wpdb->prefix . 'templates_email';
    $table_programs = $wpdb->prefix . 'programs';
    $table_quota_rules = $wpdb->prefix . 'quota_rules';
    $table_scholarship_assigned_student = $wpdb->prefix . 'scholarship_assigned_student';
    $table_expenses = $wpdb->prefix . 'expenses';
    $table_alliances_by_institute = $wpdb->prefix . 'alliances_by_institutes';
    $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';
    $table_managers_by_alliance = $wpdb->prefix . 'managers_by_alliances';
    $table_custom_inputs = $wpdb->prefix . 'custom_inputs';
    $table_grades_by_country = $wpdb->prefix . 'grades_by_country';
    $table_programs_by_student = $wpdb->prefix . 'programs_by_student';
    $table_careers_by_program = $wpdb->prefix . 'careers_by_program';
    $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';
    $table_plans_by_program = $wpdb->prefix . 'plans_by_program';
    $payment_methods_by_plan = $wpdb->prefix . 'payment_methods_by_plan';
    $table_student_program = $wpdb->prefix . 'student_program';
    $table_admission_fees = $wpdb->prefix . 'admission_fees';
    $table_student_balance = $wpdb->prefix . 'student_balance';
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';


    // Para todas las tablas: Mueve la llamada a dbDelta() FUERA del if de existencia de tabla.
    // Esto asegura que dbDelta() siempre compare la estructura actual con la deseada
    // y añada columnas si faltan, o cree la tabla si no existe.

    dbDelta( "CREATE TABLE $table_student_expected_matrix (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL,
        `term_index` INT(11) NULL,
        `term_position` INT(11) NULL,
        `subject_id` INT(11) NULL,
        `academic_period` TEXT NULL,
        `academic_period_cut` TEXT NULL,
        `status` TEXT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_sem_student (student_id),
        UNIQUE KEY ux_sem_student_subject_term (student_id, subject_id, term_index)
    ) $charset_collate;" );
    
    dbDelta( "CREATE TABLE $table_student_balance (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL,
        `balance` DECIMAL(15, 2) NOT NULL,
        `currency` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;" );

    dbDelta(
        "CREATE TABLE " . $table_plans_by_program . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `program_identificator` TEXT NOT NULL,
        `payment_plan_identificator` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $payment_methods_by_plan . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `payment_plan_identificator` TEXT NOT NULL,
        `payment_method_identificator` TEXT NOT NULL,
        `account_identificator` TEXT NULL,
        `fee_payment_complete` BOOLEAN NOT NULL DEFAULT TRUE,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE $table_admission_fees (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `is_active` tinyint(1) DEFAULT 1,
            `name` TEXT NOT NULL,
            `price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `currency` TEXT NOT NULL,
            `product_id` INT(11) NULL DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `programs` TEXT NOT NULL,
            `type_fee` TEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $table_student_program . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `is_active` tinyint(1) DEFAULT 1,
        `identificator` TEXT NOT NULL,
        `name` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `type` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $table_careers_by_program . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `is_active` tinyint(1) DEFAULT 1,
        `program_identificator` TEXT NOT NULL,
        `identificator` TEXT NOT NULL,
        `name` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $table_mentions_by_career . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `is_active` tinyint(1) DEFAULT 1,
        `career_identificator` TEXT NOT NULL,
        `identificator` TEXT NOT NULL,
        `name` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_programs (PLANES DE PAGOS/PAYMENT PLANS)
    dbDelta(
        "CREATE TABLE $table_programs (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `is_active` tinyint(1) DEFAULT 1,
            `program_identificator` TEXT NOT NULL,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `total_price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `currency` TEXT NOT NULL,
            `product_id` INT(11) NULL DEFAULT NULL,
            `subprogram` JSON NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $table_programs_by_student . " (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `student_id` INT(11) NOT NULL,
            `program_identificator` TEXT NOT NULL,
            `career_identificator` TEXT NOT NULL,
            `mention_identificator` TEXT NULL,
            `plan_identificator` TEXT NOT NULL,
            `status` VARCHAR(20) NOT NULL DEFAULT 'in_progress',
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    dbDelta(
        "CREATE TABLE " . $table_grades_by_country . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `country` TEXT NOT NULL,
        `grades` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // Ejemplo para table_custom_inputs:
    dbDelta(
        "CREATE TABLE " . $table_custom_inputs . " (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `label` TEXT NOT NULL,
            `page` TEXT NOT NULL,
            `input_mode` TEXT NOT NULL,
            `input_name` TEXT NOT NULL,
            `input_id` TEXT NOT NULL,
            `input_type` TEXT NOT NULL,
            `input_required` BOOLEAN NOT NULL DEFAULT TRUE,
            `input_is_metadata` BOOLEAN NOT NULL DEFAULT FALSE,
            `input_options` TEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // Ejemplo para table_managers_by_alliance:
    dbDelta(
        "CREATE TABLE " . $table_managers_by_alliance . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            alliance_id INT(11) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // Repite este patrón para TODAS las definiciones de tablas.
    // Es decir, cada bloque `if ($wpdb->get_var("SHOW TABLES LIKE ...") != ...) { dbDelta(...) }`
    // debe ser cambiado a solo `dbDelta(...)`.

    // table_managers_by_institute
    dbDelta(
        "CREATE TABLE " . $table_managers_by_institute . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            institute_id INT(11) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_alliances_by_institute
    dbDelta(
        "CREATE TABLE " . $table_alliances_by_institute . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            alliance_id INT(11) NOT NULL,
            alliance_fee  DOUBLE(10, 2) NULL,
            institute_id INT(11) NOT NULL,
            institute_fee  DOUBLE(10, 2) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_expenses
    dbDelta(
        "CREATE TABLE " . $table_expenses . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        motive TEXT NOT NULL,
        apply_to DATE NOT NULL,
        amount DOUBLE(10, 2) NOT NULL,
        currency TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_scholarship_assigned_student
    dbDelta(
        "CREATE TABLE " . $table_scholarship_assigned_student . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            student_id INT(11) NOT NULL,
            scholarship_id INT(11) NOT NULL,
            status_id INT(11) NOT NULL,
            termination_date TIMESTAMP NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_quota_rules
    dbDelta(
        "CREATE TABLE $table_quota_rules (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `is_active` tinyint(1) DEFAULT 1,
            `name` TEXT NOT NULL,
            `program_id` TEXT NOT NULL,
            `initial_payment` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `initial_payment_sale` DECIMAL(15, 2) NULL DEFAULT null,
            `final_payment` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `final_payment_sale` DECIMAL(15, 2) NULL DEFAULT null,
            `quote_price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `quote_price_sale` DECIMAL(15, 2) NULL DEFAULT null,
            `quotas_quantity` INT(11) NOT NULL DEFAULT 1,
            `frequency_value` INT NOT NULL,
            `type_frequency` TEXT NOT NULL,
            `start_charging` TEXT DEFAULT '',
            `position` INT NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_templates_email
    dbDelta(
        "CREATE TABLE " . $table_templates_email . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_feed
    dbDelta(
        "CREATE TABLE " . $table_feed . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            title TEXT NOT NULL,
            attach_id_desktop INT(11) NULL,
            attach_id_mobile INT(11) NULL,
            link TEXT NOT NULL,
            `max_date` DATE NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_dynamic_links
    dbDelta(
        "CREATE TABLE " . $table_dynamic_links . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        link TEXT NOT NULL,
        type_document TEXT NULL,
        id_document TEXT NULL,
        name TEXT NULL,
        last_name TEXT NULL,
        email TEXT NULL,
        program_identificator TEXT NOT NULL,
        payment_plan_identificator TEXT NULL,
        transfer_cr BOOLEAN NOT NULL DEFAULT 0,
        fee_payment_completed BOOLEAN NOT NULL DEFAULT 0,
        manager_id INT(11) NULL,
        created_by INT(11) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_dynamic_links_email_log
    dbDelta(
        "CREATE TABLE " . $table_dynamic_links_email_log . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        dynamic_link_id INT(11) NOT NULL,
        email TEXT NOT NULL,
        created_by INT(11) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_pensum
    dbDelta(
        "CREATE TABLE " . $table_pensum . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        matrix JSON NULL,
        `type` TEXT NOT NULL,
        `status` INT(11) NOT NULL,
        program_id TEXT NULL,
        institute_id INT(11) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_type_requests
    dbDelta(
        "CREATE TABLE " . $table_type_requests . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type TEXT NOT NULL,
        price FLOAT NOT NULL,
        currency VARCHAR(10) NOT NULL,
        document_certificate_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_requests
    dbDelta(
        "CREATE TABLE " . $table_requests . " (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `partner_id` INT(11) NOT NULL,
        `student_id` INT(11) NULL,
        `description` TEXT NULL,
        `by` TEXT NULL,
        `type_id` INT(11) NULL,
        `status_id` INT(11) NULL,
        `response` TEXT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_count_pending_student - MANTENER el if para la inserción inicial de datos
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_count_pending_student}'") != $table_count_pending_student) {
        dbDelta(
        "CREATE TABLE " . $table_count_pending_student . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            count INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id))$charset_collate;"
        );
        $wpdb->insert($table_count_pending_student, [
        'count' => 0
        ]);
    } else {
        // Si la tabla ya existe, aún puedes llamar a dbDelta para actualizar su estructura
        dbDelta(
        "CREATE TABLE " . $table_count_pending_student . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            count INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id))$charset_collate;"
        );
    }

    // table_student_academic_projection
    dbDelta(
        "CREATE TABLE " . $table_student_academic_projection . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            student_id INT(11) NOT NULL,
            projection JSON NULL,
            matrix JSON NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id))$charset_collate;"
    );

    // table_student_califications
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
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_teachers
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
            nacionality TEXT NULL,
            profile_picture INT(11) NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            address TEXT NULL,
            status INT(1) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_school_subjects
    dbDelta(
        "CREATE TABLE " . $table_school_subjects . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `is_active` tinyint(1) DEFAULT 1,
        `is_open` tinyint(1) NOT NULL DEFAULT 0,
        `code_subject` text NOT NULL,
        `name` text NOT NULL,
        `description` text NOT NULL,
        `min_pass` double NOT NULL,
        `max_students` int(11) NOT NULL DEFAULT 25,
        `matrix_position` int(11) DEFAULT 0,
        `hc` int(11) NOT NULL,
        `moodle_course_id` int(11) DEFAULT NULL,
        `teacher_id` int(11) DEFAULT NULL,
        `type` text DEFAULT NULL,
        `is_elective` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_academic_offers
    dbDelta(
        "CREATE TABLE " . $table_academic_offers . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        grades_downloaded tinyint(1) DEFAULT 0,
        section INT(11) NULL,
        subject_id INT(11) NOT NULL,
        type TEXT NOT NULL,
        code_period TEXT NOT NULL,
        cut_period TEXT NOT NULL,
        teacher_id INT(11) NULL,
        max_students INT(11) NOT NULL,
        moodle_course_id INT(11) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_tickets_created
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

    // table_student_period_inscriptions
    dbDelta(
        "CREATE TABLE " . $table_student_period_inscriptions . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        status_id INT(11) NOT NULL,
        type TEXT NULL,
        section INT(11) NULL DEFAULT 1,
        student_id INT(11) NOT NULL,
        subject_id INT(11) NULL,
        code_subject TEXT NULL,
        calification DOUBLE(10, 2) NULL,
        code_period INT(11) NOT NULL,
        cut_period TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_user_notices
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

    // table_alliances_payments
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

    // table_institutes_payments
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

    // table_users_signatures
    dbDelta(
        "CREATE TABLE " . $table_users_signatures . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id TEXT NOT NULL,
        signature LONGTEXT NOT NULL,
        document_id LONGTEXT NOT NULL,
        grade_selected TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_academic_periods
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
        `current` int(11) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_academic_periods_cut
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

    // table_pre_users
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

    // table_pre_students
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

    // table_student_scholarship_application
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

    // table_departments
    dbDelta(
        "CREATE TABLE " . $table_departments . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_student_payments
    dbDelta(
        "CREATE TABLE " . $table_student_payments . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            status_id INT(11) NOT NULL,
            student_id INT(11) NOT NULL,
            order_id INT(11) NULL,
            product_id INT(11) NOT NULL,
            variation_id INT(11) NULL,
            manager_id INT(11) NULL,
            institute_id INT(11) NULL,
            institute_fee DOUBLE(10, 2) NULL,
            alliances JSON NULL,
            currency VARCHAR(10) NOT NULL,
            amount DOUBLE(10, 2) NOT NULL DEFAULT 0,
            original_amount_product DOUBLE(10, 2) NULL DEFAULT 0,
            total_amount DOUBLE(10, 2) NULL DEFAULT 0,
            original_amount DOUBLE(10, 2) NULL DEFAULT 0,
            discount_amount DOUBLE(10, 2) NULL DEFAULT 0,
            type_payment INT(11) NOT NULL,
            cuote INT(11) NULL,
            num_cuotes INT(11) NULL,
            date_payment DATE NULL,
            date_next_payment DATE NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_student_payments_log
    dbDelta(
        "CREATE TABLE " . $table_student_payments_log . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        old_amount DOUBLE(10, 2) NOT NULL,
        new_amount DOUBLE(10, 2) NOT NULL,
        difference DOUBLE(10, 2) NOT NULL,
        currency TEXT NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_students
    dbDelta(
        "CREATE TABLE " . $table_students . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        type_document TEXT NULL,
        id_document TEXT NULL,
        ethnicity TEXT NULL,
        academic_period TEXT NULL,
        initial_cut TEXT NULL,
        terms_available INT(11) NULL,
        profile_picture INT(11) NULL,
        name TEXT NOT NULL,
        middle_name TEXT NULL,
        last_name TEXT NOT NULL,
        middle_last_name TEXT NULL,
        birth_date DATE NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        gender TEXT NULL,
        nacionality TEXT NULL,
        country TEXT NULL,
        city TEXT NULL,
        expected_graduation_date TEXT NULL,
        postal_code TEXT NULL,
        grade_id INT(11) NOT NULL,
        name_institute TEXT NOT NULL,
        institute_id INT(11) NULL,
        program_id TEXT NULL,
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
        max_access_date DATE NULL,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_student_documents
    dbDelta(
        "CREATE TABLE " . $table_student_documents . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        document_id TEXT NOT NULL,
        attachment_id BIGINT NOT NULL,
        approved_by INT(11) NULL,
        status INT(11) NOT NULL,
        description TEXT NULL,
        is_required INT(11) NOT NULL DEFAULT 0,
        is_visible BOOLEAN NOT NULL DEFAULT 1,
        max_date_upload DATE NULL,
        upload_at DATETIME NULL,
        updated_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_teacher_documents
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

    // table_institutes
    dbDelta(
        "CREATE TABLE " . $table_institutes . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        country VARCHAR(100) NOT NULL,
        state VARCHAR(100) NOT NULL,
        city VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        level_id INT(11) NOT NULL,
        type_calendar TINYINT(1) NOT NULL DEFAULT 1,
        name_rector VARCHAR(255) NOT NULL,
        name_contact VARCHAR(255) NOT NULL,
        lastname_rector VARCHAR(255) NOT NULL,
        lastname_contact VARCHAR(255) NOT NULL,
        phone_rector VARCHAR(20) NOT NULL,
        phone_contact VARCHAR(20) NOT NULL,
        reference TINYINT(1) NOT NULL,
        status TINYINT(1) NOT NULL DEFAULT 1,
        alliance_id INT(11) NULL,
        manager_user_id INT(11) NULL,
        fee DECIMAL(5,2) NOT NULL DEFAULT 10.00,
        business_name VARCHAR(255) NOT NULL,
        lower_text VARCHAR(255) NOT NULL DEFAULT 'Lower',
        middle_text VARCHAR(255) NOT NULL DEFAULT 'Middle',
        upper_text VARCHAR(255) NOT NULL DEFAULT 'Upper',
        graduated_text VARCHAR(255) NOT NULL DEFAULT 'Graduated',
        description TEXT NOT NULL,
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        INDEX idx_status (status),
        INDEX idx_alliance (alliance_id),
        INDEX idx_level (level_id)
        )$charset_collate;"
    );

    // table_alliances
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
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_grades - MANTENER el if para la inserción inicial de datos
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
    } else {
        // Si la tabla ya existe, aún puedes llamar a dbDelta para actualizar su estructura
        dbDelta(
        "CREATE TABLE " . $table_grades . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name TEXT NULL,
            description TEXT NULL,
            updated_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id))$charset_collate;"
        );
    }

    // table_documents - MANTENER el if para la inserción inicial de datos
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_documents}'") != $table_documents) {
        dbDelta(
        "CREATE TABLE " . $table_documents . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name TEXT NOT NULL,
            type_file TEXT NOT NULL,
            academic_scope JSON NULL,
            grade_id INT(11) NOT NULL,
            program_identificator TEXT NULL,
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
            'name' => 'GOVERNMENT-ISSUED PHOTO ID. (IDENTITY DOCUMENT OR PASSPORT OR DRIVER\'S LICENSE OR CEDULA OR DNI OR DIN)',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 1,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'PHOTO-ID OR PASSPORT',
            'type_file' => '.jpeg, .png, .jpg',
            'grade_id' => $grade->id,
            'is_required' => 1,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'LAST DEGREE DIPLOMA OBTAINED. (NATIONAL OR FOREIGN)',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'REPORT OF GRADES OR OFFICIAL TRANSCRIPTS OF COURSES PASSED AT A HIGHER EDUCATION INSTITUTION (TSU OR BACHELOR\'S DEGREE)',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'OFFICIAL GED HIGH SCHOOL ORIGINAL TRANSCRIPTS',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'TRANSLATION OR EQUIVALENT HIGH SCHOOL OR GED BY RECOGNIZED INSTITUTION',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'CURRICULUM VITAE (PH D. ONLY)',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

            $wpdb->insert($table_documents, [
            'name' => 'THESIS IDEA TO DEVELOP (PH.D. ONLY)',
            'type_file' => '.pdf',
            'grade_id' => $grade->id,
            'is_required' => 0,
            'id_requisito' => '',
            'created_at' => date('Y-m-d H:i:s')
            ]);

        }
        }
    } else {
        // Si la tabla ya existe, aún puedes llamar a dbDelta para actualizar su estructura
        dbDelta(
        "CREATE TABLE " . $table_documents . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name TEXT NOT NULL,
            type_file TEXT NOT NULL,
            academic_scope JSON NULL,
            grade_id INT(11) NOT NULL,
            is_required INT(11) NOT NULL,
            is_visible BOOLEAN NOT NULL DEFAULT 1,
            id_requisito TEXT NOT NULL,
            updated_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id))$charset_collate;"
        );
    }

    // table_documents_for_teachers - MANTENER el if para la inserción inicial de datos
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
    } else {
        // Si la tabla ya existe, aún puedes llamar a dbDelta para actualizar su estructura
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
    }

    // table_pre_scholarship
    dbDelta(
        "CREATE TABLE " . $table_pre_scholarship . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        document_type TEXT NULL,
        document_id TEXT NULL,
        name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL,
        scholarship_type TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_scholarships_availables
    dbDelta(
        "CREATE TABLE " . $table_scholarships_availables . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        coupons JSON NOT NULL,
        fee_registration BOOLEAN DEFAULT 0,
        percent_registration INT(11) NOT NULL,
        program BOOLEAN DEFAULT 0,
        percent_program INT(11) NOT NULL,
        fee_graduation BOOLEAN DEFAULT 0,
        percent_graduation INT(11) NOT NULL,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id))$charset_collate;"
    );

    // table_expected_matrix - MANTENER el if para la inserción inicial de datos
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_expected_matrix}'") != $table_expected_matrix) {
        dbDelta(
        "CREATE TABLE " . $table_expected_matrix . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            grade_id INT(11) NOT NULL,
            initial_cut TEXT NOT NULL,
            available_periods INT(11) NOT NULL,
            max_expected INT(11) NOT NULL,
            expected_sequence TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id))$charset_collate;"
        );

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 1,
        'initial_cut' => 'A',
        'max_expected' => 1,
        'expected_sequence' => 'R,EA,R,EA,EP,R,EA,R,EA,EP,R,EA,R,EA,EA'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 1,
        'initial_cut' => 'B',
        'max_expected' => 1,
        'expected_sequence' => 'R,EA,R,EA,EP,R,EA,R,EA,EP,R,EA,R,EA'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 1,
        'initial_cut' => 'C',
        'max_expected' => 1,
        'expected_sequence' => 'R,EA,R,EA,EP,R,EA,R,EA,EP,R,EA,R'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 1,
        'initial_cut' => 'D',
        'max_expected' => 1,
        'expected_sequence' => 'R,EA,R,EA,EP,R,EA,R,EA,EP,R,R'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 1,
        'initial_cut' => 'E',
        'max_expected' => 1,
        'expected_sequence' => 'R,EA,R,EA,EP,R,EA,R,EP,R,R'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 2,
        'initial_cut' => 'A-E',
        'max_expected' => 1,
        'expected_sequence' => 'R,R,R,EA,EP,R,R,R,EA,EP'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 3,
        'initial_cut' => 'A-E',
        'max_expected' => 2,
        'expected_sequence' => 'R,R,EP,R,EP,R,R,R'
        ]);

        $wpdb->insert($table_expected_matrix, [
        'grade_id' => 4,
        'initial_cut' => 'A-E',
        'max_expected' => 2,
        'expected_sequence' => 'R,R,EP,R,EP,R,R,R'
        ]);
    } else {
        // Si la tabla ya existe, aún puedes llamar a dbDelta para actualizar su estructura
        dbDelta(
        "CREATE TABLE " . $table_expected_matrix . " (
            id INT(11) NOT NULL AUTO_INCREMENT,
            grade_id INT(11) NOT NULL,
            initial_cut TEXT NOT NULL,
            available_periods INT(11) NOT NULL,
            max_expected INT(11) NOT NULL,
            expected_sequence TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id))$charset_collate;"
        );
    }

    $sql = "CREATE TABLE $table_expected_matrix_school (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        terms_available smallint(5) NOT NULL,
        terms_config JSON NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY ix_terms_available (terms_available)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $matrix_data = [
        15 => [1 => 'R', 3 => 'R', 6 => 'R', 8 => 'R', 11 => 'R', 13 => 'R'],
        14 => [1 => 'R', 3 => 'R', 6 => 'R', 8 => 'R', 10 => 'R', 12 => 'R'],
        13 => [1 => 'R', 3 => 'R', 5 => 'R', 7 => 'R', 9 => 'R', 11 => 'R'],
        12 => [1 => 'R', 3 => 'R', 5 => 'R', 7 => 'R', 9 => 'R', 11 => 'R'],
        11 => [1 => 'R', 3 => 'R', 5 => 'R', 6 => 'R', 8 => 'R', 10 => 'R'],
        10 => [1 => 'R', 3 => 'R', 5 => 'R', 6 => 'R', 7 => 'R', 9 => 'R'],
        9  => [1 => 'R', 2 => 'R', 4 => 'R', 5 => 'R', 6 => 'R', 8 => 'R'],
        8  => [1 => 'R', 2 => 'R', 4 => 'R', 5 => 'R', 6 => 'R', 7 => 'R'],
        7  => [1 => 'R', 2 => 'R', 3 => 'R', 4 => 'R', 5 => 'R', 6 => 'R'],
        6  => [1 => 'R', 2 => 'R', 3 => 'R', 4 => 'RR', 5 => 'R'],
        5  => [1 => 'R', 2 => 'RR', 3 => 'R', 4 => 'R', 5 => 'R']
    ];

    foreach ($matrix_data as $terms_available_count => $terms_config_array) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_expected_matrix_school WHERE terms_available = %d",
            $terms_available_count
        ));

        if (!$exists) {
            $wpdb->insert(
                $table_expected_matrix_school,
                [
                    'terms_available' => $terms_available_count,
                    'terms_config'    => json_encode($terms_config_array)
                ],
                ['%d', '%s']
            );
        }
    }
}

register_activation_hook(__FILE__, 'create_tables');





