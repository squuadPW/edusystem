<?php
/*
Plugin Name: EduSystem
Description: Transform your WordPress into a complete, professional and scalable educational ecosystem.
Version: 4.0.7
Author: EduSof
Author URI: https://edusof.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  edusystem
*/


// Include the required file for get_plugin_data()
if (!function_exists('get_plugin_data')) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$plugin_data = get_plugin_data(__FILE__);

// Definición de constantes del plugin
define('EDUSYSTEM_VERSION', $plugin_data['Version']);
define('EDUSYSTEM__FILE__', __FILE__); // ruta __FILE__
define('EDUSYSTEM_PATH', plugin_dir_path(__FILE__) ); // Ruta del directorio del plugin
define('EDUSYSTEM_URL', plugin_dir_url(__FILE__)); // URL del plugin
define('EDUSYSTEM_REMOTE_INFO_URL', 'https://versions.squuad.com/plugins/edusystem/info.json');


// ------  Sistema de actualizaciones ---------

// Verificar actualizaciones
add_filter('site_transient_update_plugins', 'EDUSYSTEM_check_update');
function EDUSYSTEM_check_update($transient){

    if (empty($transient->checked)) return $transient;

    if ($remote = wp_c_get_remote_info()) {

        $plugin_file = plugin_basename(__FILE__);
        $current_version = EDUSYSTEM_VERSION;

        if (
        version_compare($current_version, $remote->version, '<') &&
        version_compare($remote->requires, get_bloginfo('version'), '<') &&
        version_compare($remote->requires_php, PHP_VERSION, '<')
        ) {

            $update = new stdClass();
            $update->slug = $remote->slug;
            $update->plugin = $plugin_file;
            $update->new_version = $remote->version;
            $update->tested = $remote->tested;
            $update->package = $remote->download_url;

            $transient->response[$update->plugin] = $update;
        }
    }

    return $transient;
}

// Proporcionar información del plugin
add_filter('plugins_api', 'EDUSYSTEM_plugin_info', 20, 3);
function EDUSYSTEM_plugin_info($res, $action, $args) {
    if (
        'plugin_information' !== $action ||
        plugin_basename(__DIR__) !== $args->slug
    ) {
        return $res;
    }

    if ($remote = EDUSYSTEM_get_remote_info()) {
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->requires_php = $remote->requires_php;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = $remote->last_updated;

        $res->sections = [
            'description' => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog' => $remote->sections->changelog
        ];

        if ( !empty($remote->sections->screenshots) ) {
            $res->sections['screenshots'] = $remote->sections->screenshots;
        }

        $res->banners = [
            'low' => $remote->banners->low,
            'high' => $remote->banners->high
        ];
    }

    return $res;
}

// Obtener información remota con caché
function EDUSYSTEM_get_remote_info() {

    static $remote_info = null;

    if (null === $remote_info) {
        $remote = wp_remote_get(EDUSYSTEM_REMOTE_INFO_URL, [
            'timeout' => 10,
            'headers' => ['Accept' => 'application/json']
        ]);

        if (
            !is_wp_error($remote) &&
            200 === wp_remote_retrieve_response_code($remote) &&
            !empty($body = wp_remote_retrieve_body($remote))
        ) {
            $remote_info = json_decode($body);
        }
    }

    return $remote_info;
}

// ------  END Sistema de actualizaciones --------

// funciones de comisiones
include_once( plugin_dir_path(__FILE__).'payment_method_fees/payment_method_fees.php' );

// funciones de edusystem_log
include_once( plugin_dir_path(EDUSYSTEM__FILE__).'edusystem_log/edusystem_log.php' );

// funciones de actualizar la tabla de documentos del estudiante
//include_once( plugin_dir_path(EDUSYSTEM__FILE__).'update-student-document.php' ); 

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

register_activation_hook(__FILE__, 'create_tables');
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
    $table_pensum = $wpdb->prefix . 'pensum';
    $table_feed = $wpdb->prefix . 'feed';
    $table_dynamic_links = $wpdb->prefix . 'dynamic_links';
    $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
    $table_templates_email = $wpdb->prefix . 'templates_email';
    $table_programs = $wpdb->prefix . 'programs';
    $table_quota_rules = $wpdb->prefix . 'quota_rules';
    $table_advanced_quota_rules = $wpdb->prefix . 'advanced_quota_rules';
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
    $table_grade_config = $wpdb->prefix . 'grade_config';


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
            `description` TEXT DEFAULT '',
            `position` INT NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )$charset_collate;"
    );

    // table_advanced_quota_rules
    dbDelta(
        "CREATE TABLE $table_advanced_quota_rules (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `quota_id` INT(1) NOT NULL ,
            `quote_price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            `quote_price_sale` DECIMAL(15, 2) NULL DEFAULT null,
            `quotas_quantity` INT(11) NOT NULL DEFAULT 1,
            `frequency_value` INT NOT NULL,
            `type_frequency` TEXT NOT NULL,
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
        subprogram_identificator INT NULL,
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
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` TEXT NOT NULL,
        `matrix` JSON NULL,
        `type` TEXT NOT NULL,
        `status` INT(11) NOT NULL,
        `program_id` TEXT NULL,
        `institute_id` INT(11) NULL,
        `expected_matrix_id` INT(11) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
        `price` double NOT NULL,
        `currency` text NOT NULL,
        `product_id` int(11) DEFAULT NULL,
        `description` text NOT NULL,
        `min_pass` double NOT NULL,
        `max_students` int(11) NOT NULL DEFAULT 25,
        `matrix_position` int(11) DEFAULT 0,
        `hc` int(11) NOT NULL,
        `moodle_course_id` int(11) DEFAULT NULL,
        `teacher_id` int(11) DEFAULT NULL,
        `type` text DEFAULT NULL,
        `is_elective` tinyint(1) NOT NULL DEFAULT 0,
        `retake_limit` int(11) DEFAULT NULL,
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
        doc_id INT(11) NOT NULL,
        type_file TEXT NOT NULL,
        id_requisito TEXT NOT NULL,
        attachment_id BIGINT NOT NULL,
        approved_by INT(11) NULL,
        status INT(11) NOT NULL,
        description TEXT NULL,
        is_required INT(11) NOT NULL DEFAULT 0,
        is_visible BOOLEAN NOT NULL DEFAULT 1,
        automatic BOOLEAN NOT NULL DEFAULT 0,
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
            academic_department JSON NULL,
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
            academic_department JSON NULL,
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

    // table_grade_config
    dbDelta(
        "CREATE TABLE " . $table_grade_config . " (
        id INT(11) NOT NULL AUTO_INCREMENT,
        min_score DECIMAL(5,2) NOT NULL,
        literal_grade VARCHAR(5) NOT NULL,
        calc_grade DECIMAL(3,2) NOT NULL,
        sort_order INT(11) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_min_score (min_score)
        )$charset_collate;"
    );

    // Insert default grade configurations if table is empty
    $grade_configs = [
        ['min_score' => 94.00, 'literal_grade' => 'A', 'calc_grade' => 4.00, 'sort_order' => 1],
        ['min_score' => 90.00, 'literal_grade' => 'A-', 'calc_grade' => 3.70, 'sort_order' => 2],
        ['min_score' => 87.00, 'literal_grade' => 'B+', 'calc_grade' => 3.33, 'sort_order' => 3],
        ['min_score' => 83.00, 'literal_grade' => 'B', 'calc_grade' => 3.00, 'sort_order' => 4],
        ['min_score' => 80.00, 'literal_grade' => 'B-', 'calc_grade' => 2.70, 'sort_order' => 5],
        ['min_score' => 76.00, 'literal_grade' => 'C+', 'calc_grade' => 2.30, 'sort_order' => 6],
        ['min_score' => 73.00, 'literal_grade' => 'C', 'calc_grade' => 2.00, 'sort_order' => 7],
        ['min_score' => 70.00, 'literal_grade' => 'C-', 'calc_grade' => 1.70, 'sort_order' => 8],
        ['min_score' => 67.00, 'literal_grade' => 'D+', 'calc_grade' => 1.30, 'sort_order' => 9],
        ['min_score' => 60.00, 'literal_grade' => 'D', 'calc_grade' => 1.00, 'sort_order' => 10],
        ['min_score' => 0.00, 'literal_grade' => 'F', 'calc_grade' => 0.00, 'sort_order' => 11],
    ];

    foreach ($grade_configs as $config) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_grade_config WHERE min_score = %f",
            $config['min_score']
        ));

        if (!$exists) {
            $wpdb->insert(
                $table_grade_config,
                $config,
                ['%f', '%s', '%f', '%d']
            );
        }
    }

    $sql = "CREATE TABLE $table_expected_matrix (
        id INT(11) NOT NULL AUTO_INCREMENT,
        key_condition TEXT NOT NULL,
        value_condition TEXT NOT NULL,
        matrix_config JSON NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // elimina esta tabla antigua
    $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}expected_matrix_school`;");

    /* 1 => [
        'max_HC' => 1, // maximo de unidades de credito para inscribir
        'term_HC' => 0, // minimo de HC que deberia tener para para inscribir
        'max_HC_student' => // maximo de unidades de credito del estudiante incluyendo las materias que esta incribiendo
    ], */

    $matrix_data = [  
        15 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, 
                'max_HC_student' => 2
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 2
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ],
            7 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 3
            ], 
            8 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            9 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            10 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            11 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            12 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            13 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            14 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ], 
            15 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ]
        ],
        14 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, // hc acumuladas <= a este valor
                'max_HC_student' => 2 //< hc acumuladas debe se menor a este valor
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 2
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            7 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 3
            ],
            8 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            9 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            10 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            11 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            12 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            13 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ], 
            14 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ],
        ],
        13 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, // hc acumuladas <= a este valor
                'max_HC_student' => 2 //< hc acumuladas debe se menor a este valor
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 3,
                'max_HC_student' => 3
            ], 
            7 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 4
            ],
            8 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            9 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            10 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            11 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            12 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ], 
            13 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ]
        ],
        12 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, 
                'max_HC_student' => 2 
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 3,
                'max_HC_student' => 3
            ],
            7 => [
                'max_HC' => 1, 
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            8 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            9 => [
                'max_HC' => 2, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            10 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            11 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            12 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ]
        ],
        11 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2,
                'max_HC_student' => 2 
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 3,
                'max_HC_student' => 4
            ],
            7 => [
                'max_HC' => 1, 
                'term_HC' => 4,
                'max_HC_student' => 4
            ], 
            8 => [
                'max_HC' => 2, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            9 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            10 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            11 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ]
        ],
        10 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 1
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, 
                'max_HC_student' => 2
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            6 => [
                'max_HC' => 1,
                'term_HC' => 3,
                'max_HC_student' => 4
            ],
            7 => [
                'max_HC' => 2, 
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            8 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 5
            ], 
            9 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            10 => [
                'max_HC' => 2, 
                'term_HC' => 6,
                'max_HC_student' => 6
            ]
        ],
        9 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, 
                'max_HC_student' => 3 
            ], 
            5 => [
                'max_HC' => 1,
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            6 => [
            'max_HC' => 2,
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            7 => [
                'max_HC' => 2, 
                'term_HC' =>5,
                'max_HC_student' => 5
            ], 
            8 => [
            'max_HC' => 2,
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            9 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ]
        ], 
        8 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 2
            ], 
            4 => [
                'max_HC' => 1, 
                'term_HC' => 2, 
                'max_HC_student' => 3 
            ], 
            5 => [
                'max_HC' => 2,
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            6 => [
            'max_HC' => 2,
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            7 => [
                'max_HC' => 2, 
                'term_HC' => 5, 
                'max_HC_student' => 6
            ], 
            8 => [
                'max_HC' => 2, 
                'term_HC' => 5, 
                'max_HC_student' => 6
            ]
        ],   
        7 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            4 => [
                'max_HC' => 2, 
                'term_HC' => 3, // hc acumuladas <= a este valor
                'max_HC_student' => 4 //< hc acumuladas debe se menor a este valor
            ], 
            5 => [
                'max_HC' => 2,
                'term_HC' => 4,
                'max_HC_student' => 5
            ], 
            6 => [
            'max_HC' => 2,
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            7 => [
                'max_HC' => 2, 
                'term_HC' => 5,
                'max_HC_student' => 6
            ]
        ],
        6 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 1,
                'term_HC' => 1,
                'max_HC_student' => 2
            ], 
            3 => [
                'max_HC' => 1,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            4 => [
                'max_HC' => 2, 
                'term_HC' => 4, 
                'max_HC_student' => 5 
            ], 
            5 => [
                'max_HC' => 2,
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
            6 => [
                'max_HC' => 2,
                'term_HC' => 5,
                'max_HC_student' => 6
            ]
        ],
        5 => [
            1 => [
                'max_HC' => 1, 
                'term_HC' => 0,
                'max_HC_student' => 1
            ], 
            2 => [
                'max_HC' => 2,
                'term_HC' => 2,
                'max_HC_student' => 3
            ], 
            3 => [
                'max_HC' => 2,
                'term_HC' => 3,
                'max_HC_student' => 4
            ], 
            4 => [
                'max_HC' => 2, 
                'term_HC' => 4, 
                'max_HC_student' => 5
            ], 
            5 => [
                'max_HC' => 2,
                'term_HC' => 5,
                'max_HC_student' => 6
            ], 
        ]
    ];

    foreach ( $matrix_data as $terms_available_count => $matrix_config_array ) {

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM {$table_expected_matrix} WHERE key_condition = 'terms' AND value_condition = %s ",
            $terms_available_count
        ));

        if ( !$exists ) {
            $wpdb->insert(
                $table_expected_matrix,
                [   
                    'key_condition' => 'terms',
                    'value_condition' => $terms_available_count,
                    'matrix_config'    => json_encode($matrix_config_array)
                ],
                ['%s', '%s', '%s']
            );
        }
    }
}


add_action( 'woocommerce_account_dashboard', function () {
    ?>
        <div style="background-color: #f7f7f7; padding: 20px; border-radius: 5px; border: 1px solid #ddd;">
            <h3>¡Bienvenido a tu zona VIP!</h3>
            <p>Aquí puedes consultar tus ofertas exclusivas basadas en tus compras anteriores.</p>

            <pre>
                <?php //test_generate_projection_student(258,true) ?>
                
                <?php 
                    generate_expectation_matrix( 258 )
                ?>
            </pre>
        </div>
    <?php
});


function test_generate_projection_student( $student_id ) {

    // Validar el ID del estudiante
    if ( !is_numeric($student_id) || $student_id <= 0 ) return false;

    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_students = $wpdb->prefix . 'students';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $table_expected_matrix_school = $wpdb->prefix . 'expected_matrix_school';
    $table_pensum = $wpdb->prefix . 'pensum';

    // Obtener información del estudiante incluyendo expected_graduation_date y academic_period
    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, academic_period, initial_cut FROM {$table_students} WHERE id = %d",
        $student_id
    ));
    if ( !$student ) return false;

    // Obtener pensum del programa y proyección actual
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];
    
    // También obtener la lista de regulares 
    $matrix_regular = only_pensum_regular($program->identificator);

    // Calcular matriz basada en expected_graduation_date
    $calculated_matrix = null;
    $terms_available = null;

    //  analizar si esta validacion se puede reducir
    if ( !empty($student->expected_graduation_date) ) {

        try {

            // Convertir expected_graduation_date de MM/YYYY a fecha
            list($month, $year) = explode('/', $student->expected_graduation_date);
            $graduation_date = new DateTime("$year-$month-01");
            $graduation_date->modify('last day of this month');

            // Crear rango desde academic_period hasta expected_graduation_date
            $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
            $registration_date = new DateTime($period->start_date);

            // Contar períodos académicos únicos en ese rango
            $periods_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$table_academic_periods_cut} 
                 WHERE start_date >= %s AND max_date <= %s",
                $registration_date->format('Y-m-d'),
                $graduation_date->format('Y-m-d')
            ));

            // Aplicar límites: min 5, max 15
            $terms_available = min(15, max(5, intval($periods_count)));

            // Obtener matriz correspondiente de expected_matrix_school
            $matrix_config = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_expected_matrix_school} 
                 WHERE terms_available = %d",
                $terms_available
            ));

            if ($matrix_config) {

                $terms_config_decoded = json_decode($matrix_config->terms_config, true);

                // Build detailed matrix (in-memory array). We will persist it to `student_expected_matrix` later.
                $detailed_matrix = test_build_detailed_matrix($terms_config_decoded, $matrix_config->terms_available, $matrix_regular, $student_id);
                
                // Keep an encoded copy for backward compatibility if other code reads it; but we will not store it as primary source.
                $calculated_matrix = !empty($detailed_matrix) ? json_encode($detailed_matrix) : null;
            }
        } catch (Exception $e) {
            throw new Exception($wpdb->last_error);
        }
    } else {
        $full_name_student = student_names_lastnames_helper($student_id);
        edusystem_get_log('Expected graduation date is empty for student: ' . $full_name_student, 'Automatically enrollment');
    }
 
    //var_dump($detailed_matrix);
    return false;


    // Obtener inscripciones del estudiante
    $inscriptions = get_inscriptions_by_student($student_id);
    $inscriptions_by_code = [];
    $elective_inscriptions = [];

    // Se procesan todas las inscripciones del estudiante y se elige la de mayor precedencia (3=Aprobada, luego 1=Activa, luego 4=Reprobada) para cada 'code_subject'.
    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            // Obtener los detalles de la materia
            $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

            if ($subject && $subject->type === 'elective') {
                // Las electivas se mantienen separadas para el procesamiento posterior de solo las completadas.
                $elective_inscriptions[] = $inscription;
            } else {
                $code = $inscription->code_subject;
                $current_status = (int) $inscription->status_id;

                if ( isset($inscriptions_by_code[$code]) ) {

                    $existing_status = (int) $inscriptions_by_code[$code]->status_id;

                    // Priorizar estado Aprobado (3) sobre cualquier otro.
                    if ( $existing_status === 3 ) continue; // Ya tenemos el mejor estado posible.

                    // Si el nuevo estado es Aprobado (3), sobrescribir inmediatamente.
                    if ( $current_status === 3 ) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si el estado existente es Activo (1) y el nuevo es Reprobado (4), mantener Activo (1).
                    if ($existing_status === 1 && $current_status === 4) continue;

                    // Si el nuevo estado es Activo (1) y el existente es Reprobado (4) o To begin (0), sobrescribir con Activo (1).
                    if ($current_status === 1 && ($existing_status === 4 || $existing_status === 0)) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si ambos son Reprobado (4), To begin (0), o si el nuevo estado es mejor que el existente, tomar el nuevo.
                    if ($current_status > $existing_status && $current_status !== 2) {
                        $inscriptions_by_code[$code] = $inscription;
                    }
                    
                } else {
                    // Si no existe entrada, simplemente agregar, siempre que no sea Unsubscribed (2)
                    if ($current_status !== 2) {
                        $inscriptions_by_code[$code] = $inscription;
                    }
                }
            }
        }
    }

    // Obtener el pensum activo para el programa (matriz completa)
    $pensum = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = %s", $program->identificator));
    if (!$pensum) return false;

    $pensum_matrix = json_decode( $pensum->matrix );
    if ( empty($pensum_matrix) ) return false;

    // Generar proyección base usando la matriz COMPLETA del pensum (incluye regulares y otros tipos)
    $projection = [];
    foreach ($pensum_matrix as $m) {
        // $m contiene elementos con 'id', 'name', 'code_subject', 'type', etc. (según cómo se guardó la matriz)
        $subject_details = get_subject_details($m->id);
        if (!$subject_details) {
            // Si no existe la materia en school_subjects, saltar
            continue;
        }

        $inscription = $inscriptions_by_code[$subject_details->code_subject] ?? null;
        $status_id = $inscription ? (int) $inscription->status_id : null;

        // Determinar si la materia está 'completada' (solo si está APROBADA = 3)
        $is_completed = ($status_id === 3);

        // Determinar si es 'this_cut' (solo si está ACTIVA = 1)
        $is_this_cut = ($status_id === 1);

        $projection[] = [
            'hc' => isset($m->hc) ? $m->hc : (isset($subject_details->hc) ? $subject_details->hc : ''),
            // La información de corte solo se llena si está Aprobada (3) o Activa (1).
            'cut' => $is_completed || $is_this_cut ? $inscription->cut_period : "",
            'type' => isset($m->type) ? strtolower($m->type) : strtolower($subject_details->type),
            'subject' => $subject_details->name,
            'this_cut' => $is_this_cut, // Solo Activa
            'subject_id' => (string) $subject_details->id,
            'code_period' => $is_completed || $is_this_cut ? $inscription->code_period : "",
            // La calificación solo se llena si está Aprobada (3).
            'calification' => $is_completed ? $inscription->calification : "",
            'code_subject' => $subject_details->code_subject,
            'is_completed' => $is_completed, // Solo Aprobada
            'welcome_email' => $is_completed || $is_this_cut, // True si está Approved/Active
            'assigned_slots' => $subject_details->retake_limit ?? 0,
        ];
    }

    // Agregar materias electivas a la proyección
    foreach ($elective_inscriptions as $inscription) {
        // abandonar si es reprobada
        if ((int) $inscription->status_id === 4) {
            continue;
        }

        // Obtener detalles de la materia electiva
        $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

        if ($subject) {
            $projection[] = [
                'code_subject' => $subject->code_subject,
                'subject_id' => $subject->id,
                'subject' => $subject->name,
                'hc' => $subject->hc,
                'cut' => $inscription->cut_period,
                'code_period' => $inscription->code_period,
                'calification' => $inscription->calification ?? 0,
                'is_completed' => $inscription->status_id == 3 ? true : false,
                'this_cut' => $inscription->status_id == 1 ? true : false,
                'welcome_email' => true,
                'type' => 'elective',
                'assigned_slots' => $subject->retake_limit ?? 0,
            ];
        }
    }

    return false;

    // Si es forzado, actualizar registros
    if ($force) {
        $wpdb->query('START TRANSACTION');

        try {
            // Actualizar estudiante y eliminar proyecciones existentes en una sola transacción
            $wpdb->update($table_students, ['elective' => 0, 'terms_available' => $terms_available], ['id' => $student_id]);
            $wpdb->delete($table_student_academic_projection, ['student_id' => $student_id]);

            // Insertar nueva proyección (terms_available now stored on student)
            $result = $wpdb->insert($table_student_academic_projection, [
                'student_id' => $student_id,
                'projection' => json_encode($projection),
                'matrix' => $calculated_matrix
            ]);

            if ($result === false) {
                throw new Exception('Error al insertar la proyección');
            }

            // Persist expected matrix rows if available
            if (!empty($detailed_matrix)) {
                clear_expected_matrix_for_student($student_id);
                persist_expected_matrix($student_id, $detailed_matrix);
            }

            // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

            // 1. Inscripciones regulares (ya con precedencia aplicada)
            foreach ($inscriptions_by_code as $inscription) {
                // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
                if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            // 2. Inscripciones electivas (solo si son Aprobadas, ya que son el único estado que se incluye)
            foreach ($elective_inscriptions as $inscription) {
                if ((int) $inscription->status_id === 3) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    // Store terms_available on student record (projection no longer holds it)
    if (!is_null($terms_available)) {
        $wpdb->update($table_students, ['terms_available' => $terms_available], ['id' => $student_id]);
    }

    // Insertar nueva proyección sin forzar con matriz calculada
    $result = $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection),
        'matrix' => $calculated_matrix
    ]);

    if ($result !== false) {

        // If we have a detailed matrix, persist it into `student_expected_matrix`.
        if (!empty($detailed_matrix)) {
            // If this operation was forced, clear previous expected matrix rows for the student to avoid duplicates.
            if ($force === true) {
                clear_expected_matrix_for_student($student_id);
            }
            persist_expected_matrix($student_id, $detailed_matrix);
        }

        // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

        // 1. Inscripciones regulares (ya con precedencia aplicada)
        foreach ($inscriptions_by_code as $inscription) {
            // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
            if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        // 2. Inscripciones electivas (solo si son Aprobadas)
        foreach ($elective_inscriptions as $inscription) {
            if ((int) $inscription->status_id === 3) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        return true;
    }

    return false;
}

function test_build_detailed_matrix($terms_config, $terms_available, $matrix_regular, $student_id)
{
    if (empty($terms_config) || empty($matrix_regular)) return [];

    global $wpdb;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

    $detailed_matrix = [];
    $subject_index = 0;

    // Obtener detalles del estudiante y fecha de creación
    $student = get_student_detail($student_id);
    if ( !$student ) return [];
    
    $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
    $registration_date = new DateTime($period->start_date);

    $future_periods = $wpdb->get_results( $wpdb->prepare(
        "SELECT DISTINCT code, cut FROM {$table_academic_periods_cut} 
        WHERE start_date >= %s
        ORDER BY start_date ASC LIMIT 20",
        $registration_date->format('Y-m-d')
    ));

    // Obtener inscripciones del estudiante y clasificar por estado
    $inscriptions = get_inscriptions_by_student($student_id);
    $completed_subjects = [];
    $enrolled_subjects = [];

    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->status_id == 3) {
                $completed_subjects[] = $inscription->subject_id;
            } elseif ($inscription->status_id == 1) {
                // Aunque no se usan directamente en el loop principal, se mantienen por si se necesitan.
                $enrolled_subjects[] = $inscription->subject_id; 
            }
        }
    }

    $period_index = 0;
    for ($i = 0; $i < $terms_available; $i++) {
        $term_number = $i + 1;
        $term_type = $terms_config[$term_number] ?? 'N/A';

        // Obtener datos del período futuro
        $period_data = ($period_index < count($future_periods)) ? $future_periods[$period_index] : null;

        if ($term_type === 'RR') {
            // Este es un período que contiene 2 asignaturas
            $subjects_to_process = 2;
            $new_entries = [];

            for ($j = 0; $j < $subjects_to_process; $j++) {
                if ($subject_index < count($matrix_regular)) {
                    $subject = $matrix_regular[$subject_index];
                    $is_completed = in_array($subject->subject_id, $completed_subjects);

                    $entry = [
                        'cut' => $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : ''),
                        'type' => 'R', // Se considera 'R' (regular) a nivel de asignatura
                        'subject_id' => $subject->subject_id,
                        'code_period' => $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : ''),
                        'completed' => $is_completed
                    ];
                    $new_entries[] = $entry;
                    $subject_index++; // Avanzar al siguiente sujeto
                } else {
                    // Rellenar con datos vacíos si no hay más asignaturas en $matrix_regular
                    $new_entries[] = [
                        'cut' => ($period_data ? $period_data->cut : ''), 
                        'type' => 'R', 
                        'subject_id' => '', 
                        'code_period' => ($period_data ? $period_data->code : ''), 
                        'completed' => false
                    ];
                }
            }
            
            // Si el período fue usado, avanzar el índice del período
            if ($period_data) {
                $period_index++; 
            }
            
            // Agregar las dos entradas individuales a la matriz detallada
            $detailed_matrix = array_merge($detailed_matrix, $new_entries);

        } elseif ($term_type === 'R') {
            // Período que contiene 1 asignatura
            $term_entry = [];
            if ($subject_index < count($matrix_regular)) {
                $subject = $matrix_regular[$subject_index];
                $is_completed = in_array($subject->subject_id, $completed_subjects);

                $term_entry = [
                    'cut' => $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : ''),
                    'type' => 'R',
                    'subject_id' => $subject->subject_id,
                    'code_period' => $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : ''),
                    'completed' => $is_completed
                ];
                $subject_index++; // Avanzar al siguiente sujeto
            } else {
                // Rellenar con datos vacíos si no hay más asignaturas
                $term_entry = [
                    'cut' => ($period_data ? $period_data->cut : ''), 
                    'type' => 'R', 
                    'subject_id' => '', 
                    'code_period' => ($period_data ? $period_data->code : ''), 
                    'completed' => false
                ];
            }
            
            if ($period_data) {
                $period_index++;
            }
            
            // Agregar la entrada individual a la matriz
            if ($term_entry) {
                $detailed_matrix[] = $term_entry;
            }

        } else {
            // Tipo de término no reconocido ('N/A'). Consume un período pero no una asignatura regular.
            $term_entry = [
                'cut' => ($period_data ? $period_data->cut : ''), 
                'type' => 'N/A', 
                'subject_id' => '', 
                'code_period' => ($period_data ? $period_data->code : ''), 
                'completed' => false
            ];
            
            if ($period_data) {
                $period_index++;
            }

            $detailed_matrix[] = $term_entry;
        }
    }

    return $detailed_matrix;
}


function generate_academic_projection_student( $student_id ) {

    // Validar el ID del estudiante
    if ( !is_numeric($student_id) || $student_id <= 0 ) return false;

    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_students = $wpdb->prefix . 'students';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $table_expected_matrix_school = $wpdb->prefix . 'expected_matrix_school';
    $table_pensum = $wpdb->prefix . 'pensum';

    // Obtener información del estudiante incluyendo expected_graduation_date y academic_period
    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, academic_period, initial_cut FROM {$table_students} WHERE id = %d",
        $student_id
    ));
    if ( !$student ) return false;

    // Obtener pensum del programa y proyección actual
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];

    // Obtener inscripciones del estudiante
    $inscriptions = get_inscriptions_by_student($student_id);
    $inscriptions_by_code = [];
    $elective_inscriptions = [];

    // Se procesan todas las inscripciones del estudiante y se elige la de mayor precedencia (3=Aprobada, luego 1=Activa, luego 4=Reprobada) para cada 'code_subject'.
    if ( !empty($inscriptions) ) {

        foreach ( $inscriptions as $inscription ) {

            // Obtener los detalles de la materia
            $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

            if ( $subject && $subject->type === 'elective' ) {
                
                // Las electivas se mantienen separadas para el procesamiento posterior de solo las completadas.
                $elective_inscriptions[] = $inscription;

            } else {

                $code = $inscription->code_subject;
                $status_id = (int) $inscription->status_id;

                if ( isset($inscriptions_by_code[$code]) && ( $status_id == 1 || $status_id == 3 ) ) {

                    $existing_status = (int) $inscriptions_by_code[$code]->status_id;

                    // Priorizar estado Aprobado (3) sobre cualquier otro.
                    if ( $existing_status === 3 ) continue; 

                    // Si el nuevo estado es Aprobado (3), sobrescribir inmediatamente.
                    if ( $status_id === 3 ) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si el estado existente es Activo (1) y el nuevo es Reprobado (4), mantener Activo (1).
                    if ($existing_status === 1 && $status_id === 4) continue;

                    // Si el nuevo estado es Activo (1) y el existente es Reprobado (4) o To begin (0), sobrescribir con Activo (1).
                    if ($status_id === 1 && ($existing_status === 4 || $existing_status === 0)) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si ambos son Reprobado (4), To begin (0), o si el nuevo estado es mejor que el existente, tomar el nuevo.
                    if ($status_id > $existing_status && $status_id !== 2) {
                        $inscriptions_by_code[$code] = $inscription;
                    }
                    
                } else if ( $status_id == 1 || $status_id == 3  ) { // Si no existe entrada, simplemente agregar, siempre que no sea Unsubscribed (2)
                    $inscriptions_by_code[$code] = $inscription;
                }
            }
        }
    }

    // Obtener el pensum activo para el programa (matriz completa)
    $pensum = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = %s", $program->identificator));
    if (!$pensum) return false;

    $pensum_matrix = json_decode( $pensum->matrix );
    if ( empty($pensum_matrix) ) return false;

    // Generar proyección base usando la matriz COMPLETA del pensum (incluye regulares y otros tipos)
    $projection = [];
    foreach ( $pensum_matrix as $m ) {

        // $m contiene elementos con 'id', 'name', 'code_subject', 'type', etc. (según cómo se guardó la matriz)
        $subject_details = get_subject_details($m->id);
        if (!$subject_details) continue;


        $inscription = $inscriptions_by_code[$subject_details->code_subject] ?? null;
        $status_id = $inscription ? (int) $inscription->status_id : null;

        // Determinar si la materia está 'completada' (solo si está APROBADA = 3)
        $is_completed = ($status_id === 3);

        // Determinar si es 'this_cut' (solo si está ACTIVA = 1)
        $is_this_cut = ($status_id === 1);

        $projection[] = [
            'hc' => isset($m->hc) ? $m->hc : (isset($subject_details->hc) ? $subject_details->hc : ''),
            // La información de corte solo se llena si está Aprobada (3) o Activa (1).
            'cut' => $is_completed || $is_this_cut ? $inscription->cut_period : "",
            'type' => isset($m->type) ? strtolower($m->type) : strtolower($subject_details->type),
            'subject' => $subject_details->name,
            'this_cut' => $is_this_cut, // Solo Activa
            'subject_id' => (string) $subject_details->id,
            'code_period' => $is_completed || $is_this_cut ? $inscription->code_period : "",
            // La calificación solo se llena si está Aprobada (3).
            'calification' => $is_completed ? $inscription->calification : "",
            'code_subject' => $subject_details->code_subject,
            'is_completed' => $is_completed, // Solo Aprobada
            'welcome_email' => $is_completed || $is_this_cut, // True si está Approved/Active
            'assigned_slots' => $subject_details->retake_limit ?? 0,
        ];
    }

    // Agregar materias electivas a la proyección
    foreach ( $elective_inscriptions as $inscription ) {

        // abandonar si es reprobada
        if ((int) $inscription->status_id === 4) continue;

        // Obtener detalles de la materia electiva
        $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

        if ($subject) {

            $projection[] = [
                'code_subject' => $subject->code_subject,
                'subject_id' => $subject->id,
                'subject' => $subject->name,
                'hc' => $subject->hc,
                'cut' => $inscription->cut_period,
                'code_period' => $inscription->code_period,
                'calification' => $inscription->calification ?? 0,
                'is_completed' => $inscription->status_id == 3 ? true : false,
                'this_cut' => $inscription->status_id == 1 ? true : false,
                'welcome_email' => true,
                'type' => 'elective',
                'assigned_slots' => $subject->retake_limit ?? 0,
            ];
        }
    }

    // Store terms_available on student record (projection no longer holds it)
    if (!is_null($terms_available)) {
        $wpdb->update($table_students, ['terms_available' => $terms_available], ['id' => $student_id]);
    }

    // Insertar nueva proyección sin forzar con matriz calculada
    $result = $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection),
        'matrix' => $calculated_matrix
    ]);

    return false;

    // Si es forzado, actualizar registros
    if ($force) {
        $wpdb->query('START TRANSACTION');

        try {
            // Actualizar estudiante y eliminar proyecciones existentes en una sola transacción
            $wpdb->update($table_students, ['elective' => 0, 'terms_available' => $terms_available], ['id' => $student_id]);
            $wpdb->delete($table_student_academic_projection, ['student_id' => $student_id]);

            // Insertar nueva proyección (terms_available now stored on student)
            $result = $wpdb->insert($table_student_academic_projection, [
                'student_id' => $student_id,
                'projection' => json_encode($projection),
                'matrix' => $calculated_matrix
            ]);

            if ($result === false) {
                throw new Exception('Error al insertar la proyección');
            }

            // Persist expected matrix rows if available
            if (!empty($detailed_matrix)) {
                clear_expected_matrix_for_student($student_id);
                persist_expected_matrix($student_id, $detailed_matrix);
            }

            // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

            // 1. Inscripciones regulares (ya con precedencia aplicada)
            foreach ($inscriptions_by_code as $inscription) {
                // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
                if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            // 2. Inscripciones electivas (solo si son Aprobadas, ya que son el único estado que se incluye)
            foreach ($elective_inscriptions as $inscription) {
                if ((int) $inscription->status_id === 3) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }


    if ($result !== false) {

        // If we have a detailed matrix, persist it into `student_expected_matrix`.
        if (!empty($detailed_matrix)) {
            // If this operation was forced, clear previous expected matrix rows for the student to avoid duplicates.
            if ($force === true) {
                clear_expected_matrix_for_student($student_id);
            }
            persist_expected_matrix($student_id, $detailed_matrix);
        }

        // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

        // 1. Inscripciones regulares (ya con precedencia aplicada)
        foreach ($inscriptions_by_code as $inscription) {
            // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
            if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        // 2. Inscripciones electivas (solo si son Aprobadas)
        foreach ($elective_inscriptions as $inscription) {
            if ((int) $inscription->status_id === 3) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        return true;
    }

    return false;
}

// $hc es unidades de credito que tiene el estudiante actualmente
function generate_expectation_matrix( $student_id ) {

    global $wpdb;
    $table_academic_periods_cut = "{$wpdb->prefix}academic_periods_cut";
    $table_expected_matrix = "{$wpdb->prefix}expected_matrix";
    $table_students = "{$wpdb->prefix}students";
    $table_pensum = $wpdb->prefix . 'pensum';

    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, academic_period, initial_cut 
        FROM `{$table_students}` WHERE id = %d ",
        $student_id
    ));
    if ( !$student ) return false;

    // fecha de inicio del studiante
    $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
    $registration_date = new DateTime($period->start_date);

    // data de periodos futuros a la fecha de registro
    $future_periods = $wpdb->get_results( $wpdb->prepare(
        "SELECT DISTINCT code, cut FROM `{$table_academic_periods_cut}`
        WHERE start_date >= %s
        ORDER BY start_date ASC LIMIT 20",
        $registration_date->format('Y-m-d')
    ));

    // Obtener pensum del programa y proyección actual
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];

    // Obtener el pensum activo para el programa
    $pensum = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = %s", $program->identificator));
    if (!$pensum) return false;

    // lista de materias del pensum
    $pensum_matrix = json_decode( $pensum->matrix );
    if ( empty($pensum_matrix) ) return false;
    
    // lista de materias regulares a ver segun el pensum
    $subject_pensum = only_pensum_regular($program->identificator);
    $subjects = $subject_pensum;

    // obtiene la configuracion por el periodo actual
    if( !empty($student->expected_graduation_date) ) {

        // Convertir expected_graduation_date de MM/YYYY a fecha
        list($month, $year) = explode('/', $student->expected_graduation_date);
        $graduation_date = new DateTime("$year-$month-01");
        $graduation_date->modify('last day of this month');

        // Contar períodos académicos únicos en ese rango
        $periods_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
            FROM `{$table_academic_periods_cut}`
            WHERE start_date >= %s AND max_date <= %s",
            $registration_date->format('Y-m-d'),
            $graduation_date->format('Y-m-d')
        ));

        // Aplicar límites: min 5, max 15 // consultar el terminos del programa
        $terms_available = min(15, max(5, intval($periods_count)));
       
        $matrix_config_json = $wpdb->get_var( $wpdb->prepare(
            "SELECT matrix_config FROM `$table_expected_matrix`
            WHERE key_condition LIKE 'terms' AND value_condition = %s ;",
            $terms_available
        ));

    } else {
        $matrix_config_json = $wpdb->get_var( $wpdb->prepare(
            "SELECT matrix_config FROM `$table_expected_matrix` WHERE id = %d ;",
            $pensum->expected_matrix_id
        ));
    }

    $matrix_config = json_decode( $matrix_config_json );

    // Obtener inscripciones del estudiante y clasificar por estado
    /* $inscriptions = get_inscriptions_by_student($student_id);
    $completed_subjects = [];
    $enrolled_subjects = [];

    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->status_id == 3) {
                $completed_subjects[] = $inscription->subject_id;
            } elseif ($inscription->status_id == 1) {
                // Aunque no se usan directamente en el loop principal, se mantienen por si se necesitan.
                $enrolled_subjects[] = $inscription->subject_id; 
            }
        }
    } */

    $accumulated_hc = 0;
    $matrix = [];
    foreach( $matrix_config as $key => $matrix_config_data ) {

        // Obtener datos del período futuro
        $period_index = $key - 1;
        $period_data = ($period_index < count($future_periods)) ? $future_periods[$period_index] : null;

        $registered_hc = 0;
        foreach ( $subjects as $id => $subject ) {

            if( 
                $accumulated_hc < $matrix_config_data->max_HC_student && 
                $accumulated_hc <= $matrix_config_data->term_HC && 
                $registered_hc < $matrix_config_data->max_HC
            ) {

                // inscribe materia
                $matrix[$key][] = [
                    'subject' => $subject->subject,
                    'subject_id' => $subject->subject_id,
                    'code_period' => $period_data ? $period_data->code : '', 
                    'cut' => $period_data ? $period_data->cut : '', 
                    'type' => 'R',
                ];

                $subject_hc = $subject->hc;
                $registered_hc += $subject_hc;
                $accumulated_hc += $subject_hc;

                unset($subjects[$id]);

            } 
        }
    }

    echo "<h1>terminos actuales del estudiante {$terms_available}</h1>";
    var_dump($matrix);
}


 /* persist_expected_matrix($student_id, $detailed_matrix) */

/* 
{
        "hc": "1",
        "cut": "",
        "type": "equivalence",
        "subject": "PHYSICAL EDUCATION PERSONAL HEALTH",
        "this_cut": false,
        "subject_id": "66",
        "code_period": "",
        "calification": "",
        "code_subject": "PEH1215",
        "is_completed": false,
        "welcome_email": false
    },

*/
 
 
