<?php 

// Registra los logs por defecto
include_once( plugin_dir_path(EDUSYSTEM__FILE__).'edusystem_log/logs.php' );

// Define los tipos de registros (logs) utilizados en Edusystem
define('EDUSYSTEM_TYPE_LOGS', [
    'login'     => __('User logged in', 'edusystem'),
    'logout'    => __('User logged out', 'edusystem'),
    'error'     => __('System error', 'edusystem'),
    'warning'   => __('System warning', 'edusystem'),
    'info'      => __('Information', 'edusystem'),
    'save_student_data'      => __('Student data saved', 'edusystem'),
    'califications'      => __('Grades viewed', 'edusystem'),
    'moodle_login'       => __('Moodle login', 'edusystem'),
    'error_moodle_login' => __('Moodle login error', 'edusystem'),
    'error_moodle'       => __('Moodle system error', 'edusystem'),
]);

// Scripts y estilos para la página de logs
add_action('admin_enqueue_scripts', function () {

    // valida que solo se activen en la pagina de log
    if ( !empty( $_GET['page'] ) || $_GET['page'] == 'edusystem-logs' || $_GET['page'] == 'edusystem-logs-delete' ) {

        wp_enqueue_style('styles-log', EDUSYSTEM_URL.'/edusystem_log/assets/css/styles.css');

        wp_register_script('scripts-log',  EDUSYSTEM_URL.'/edusystem_log/assets/js/scripts.js');
        wp_localize_script( 'scripts-log', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'translations' => [
                'select_user' => __('Select a user', 'split-payment-method'),
            ]
        ]);
        wp_enqueue_script('scripts-log');

        // Encola Flatpickr para la selección de fechas
        wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
        wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js');
    }

});

// Crea la tabla en la base de datos
register_activation_hook( EDUSYSTEM__FILE__, function () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_edusystem_log = $wpdb->prefix . 'edusystem_log';// tabla de log de edusystem

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta( "CREATE TABLE $table_edusystem_log (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `message` TEXT NOT NULL,
        `type` TEXT NOT NULL,
        `ip` VARCHAR(45) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;");
});

// Registra una capacidad personalizada al activar el plugin
register_activation_hook( EDUSYSTEM__FILE__, function () {
    $roles = array( 'administrator' );
    foreach ( $roles as $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->add_cap( 'manager_logs' );
        }
    }
});

// Elimina la capacidad personalizada al desactivar el plugin
register_deactivation_hook( EDUSYSTEM__FILE__, function () {
    $roles = array( 'administrator' );
    foreach ( $roles as $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->remove_cap( 'manager_logs' );
        }
    }
});

// Registra un log en la tabla de logs de Edusystem
function edusystem_get_log( $message, $type = 'info', $user_id = null ) {

    if (empty($user_id)) $user_id = get_current_user_id();
    
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'edusystem_log',
        [   
            'user_id' => (int) $user_id,
            'message' => $message,
            'type'    => $type,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
        ]
    );
}

// Obtiene los textos asociados al tipo de log
function edusystem_get_log_type_label( $type ) {
    return isset( EDUSYSTEM_TYPE_LOGS[$type]) ? EDUSYSTEM_TYPE_LOGS[$type] : $type;
}

// Añade el enlace a los logs de Edusystem de un usuario
add_action('personal_options', function ($user) {
    $user_id = is_object($user) ? (int) $user->ID : (int) $user;
    $url = admin_url('admin.php?page=edusystem-logs&user_id=' . $user_id);
    ?>
    <tr>
        <th scope="row"><?php esc_html_e('Edusystem Logs', 'edusystem'); ?></th>
        <td>
            <a href="<?= esc_url( $url ); ?>" rel="noopener">
                <?php esc_html_e("View this user's logs", 'edusystem'); ?>
            </a>
        </td>
    </tr>
    <?php
});

// Agrega la sección de logs de Edusystem
add_action('admin_menu', function () {
    // Menú principal (sin callback)
    add_menu_page(
        __('Edusystem Logs', 'edusystem'),     // Page title
        __('Edusystem Logs', 'edusystem'),     // Menu title
        'manager_logs',                        // Capability
        'edusystem-logs',                      // Slug
        '',                                    // Callback vacío
        'dashicons-list-view',
        80
    );

    // Lista de log
    add_submenu_page(
        'edusystem-logs',                      // Slug
        __('Edusystem Logs', 'edusystem'),     // Page title
        __('Edusystem Logs', 'edusystem'),     // Menu title
        'manager_logs',                        // Capability
        'edusystem-logs',                      // Slug i
        'edusystem_show_logs_table'            // Callback
    );

    // Eliminar
    $current_user = wp_get_current_user(); // solo para administradores
    if ( in_array( 'administrator', (array) $current_user->roles ) ) {

        add_submenu_page(
            'edusystem-logs',
            __('Delete Logs', 'edusystem'),
            __('Delete Logs', 'edusystem'),
            'manager_logs',
            'edusystem-logs-delete',
            'edusystem_show_logs_delete_page'
        );
    }
    
});

// Muestra la tabla de los logs del sistema
function edusystem_show_logs_table() { 

    if ( ! current_user_can( 'manager_logs' ) ) {
        wp_die( __( 'You do not have permission to view this page.', 'edusystem' ) );
    }

    include_once(EDUSYSTEM_PATH . '/edusystem_log/template/logs_table.php');

}

// Muestra la pagina para eliminar logs
function edusystem_show_logs_delete_page() {

    if ( ! current_user_can('manage_options') ) {
        wp_die(__('Solo administradores pueden acceder a esta página.', 'edusystem'));
    }

    include_once(EDUSYSTEM_PATH . '/edusystem_log/template/delete_logs.php');
}

// Ajax para el selct2 del usuario
add_action('wp_ajax_edusystem_search_users', function() {
    global $wpdb;
    $term = sanitize_text_field($_GET['q'] ?? '');

    // Dividir el término en palabras (para soportar "nombre apellido")
    $parts = preg_split('/\s+/', $term);

    $sql = "
        SELECT u.ID, u.user_email,
               fn.meta_value AS first_name,
               ln.meta_value AS last_name
        FROM {$wpdb->users} u
        LEFT JOIN {$wpdb->usermeta} fn ON fn.user_id = u.ID AND fn.meta_key = 'first_name'
        LEFT JOIN {$wpdb->usermeta} ln ON ln.user_id = u.ID AND ln.meta_key = 'last_name'
        WHERE u.user_email LIKE %s
           OR fn.meta_value LIKE %s
           OR ln.meta_value LIKE %s
    ";

    $params = [
        '%'.$wpdb->esc_like($term).'%',
        '%'.$wpdb->esc_like($term).'%',
        '%'.$wpdb->esc_like($term).'%'
    ];

    // Si hay dos palabras (ej: nombre apellido), buscar combinación
    if (count($parts) >= 2) {
        $sql .= " OR (fn.meta_value LIKE %s AND ln.meta_value LIKE %s)";
        $params[] = '%'.$wpdb->esc_like($parts[0]).'%';
        $params[] = '%'.$wpdb->esc_like($parts[1]).'%';
    }

    $sql .= " LIMIT 20";

    $users = $wpdb->get_results($wpdb->prepare($sql, ...$params));

    $results = [];
    foreach ($users as $user) {
        $full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $results[] = [
            'ID'          => $user->ID,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'user_email'  => $user->user_email,
            'text'        => ($full_name ?: $user->user_email) . ' (' . $user->user_email . ')'
        ];
    }

    wp_send_json($results);
});



