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
    if ( empty( $_GET['page'] ) || $_GET['page'] !== 'edusystem-logs' ) return;

    wp_enqueue_style('styles-log', EDUSYSTEM_URL . '/edusystem_log/assets/css/styles.css');

    wp_enqueue_script('scripts-log', EDUSYSTEM_URL . '/edusystem_log/assets/js/scripts.js');

    // Encola Flatpickr para la selección de fechas
    wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js');

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

// Agrega la sección de logs de Edusystem
add_action('admin_menu', 'edusystem_add_logs_page');
function edusystem_add_logs_page() {
    add_menu_page(
        __('Edusystem Logs', 'edusystem'),     // Page title
        __('Edusystem Logs', 'edusystem'),               // Menu title
        'manager_logs',
        'edusystem-logs',
        'edusystem_show_logs_table',
        'dashicons-list-view',
        80
    );
}

// Muestra la tabla de los logs del sistema
function edusystem_show_logs_table() { 

    if ( ! current_user_can( 'manager_logs' ) ) {
        wp_die( __( 'You do not have permission to view this page.', 'edusystem' ) );
    }

    include_once(EDUSYSTEM_PATH . '/edusystem_log/Edusystem_Log_Table.php');
    
    $logs_table = new Edusystem_Log_Table();
    $logs_table->prepare_items();

    ?>
        <div id="edusystem_logs" class="wrap">
            <h1><?= esc_html(__('Edusystem Logs', 'edusystem')); ?></h1>

            <form method="get" class="filters_container" >

                <?php
                    $date = isset($_GET['date']) ? $_GET['date'] : "last_month";
                    $start_date = isset($_GET['startDate']) ? $_GET['startDate'] : "";
                    $end_date = isset($_GET['endDate']) ? $_GET['endDate'] : "";
                
                    $date_custom = '';
                    if($date !== 'custom') $date_custom = 'display: none;'; 
                    
                ?>

                <input type="text" id="date-range" class="input-text" style="<?= $date_custom ?>" value="<?= $start_date.' to '.$end_date ?>" />

                <select id="select-date" class="woocommerce-Input input-text" data-date="<?=$date?>" onchange ="edusystem_date_filter_transactions(this.value);" >
                    <option value="today" <?=  selected( $date, 'today' ); ?> ><?= __('Today', 'edusystem') ?></option>
                    <option value="last_week" <?=  selected( $date, 'last_week'); ?>  ><?= __('Last Week', 'edusystem') ?></option>
                    <option value="last_month" <?=  selected( $date, 'last_month' ); ?>><?= __('Last Month', 'edusystem') ?></option>
                    <option value="last_3_months" <?=  selected( $date, 'last_3_months' ); ?>><?= __('Last 3 months', 'edusystem') ?></option>
                    <option value="custom" <?=  selected( $date, 'custom' ); ?>><?= __('Custom', 'edusystem') ?></option>
                </select>
                
                <?php 

                    // Traer todos los tipos únicos
                    global $wpdb;
                    $types_logs = $wpdb->get_col("
                        SELECT DISTINCT type
                        FROM `{$wpdb->prefix}edusystem_log`
                        ORDER BY type
                    ");

                    // Unir las llaves de la constante con el array
                    $types = array_unique(array_merge(array_keys(EDUSYSTEM_TYPE_LOGS), $types_logs));

                ?>                
                <select name="type" onchange ="edusystem_filters_transactions('type',this.value);">
                    
                    <option value="" <?=  selected( $_GET['type'] ?? '', '' ); ?>><?= __('Select type', 'edusystem') ?></option>

                    <?php foreach( $types AS $type ): ?>
                        
                        <option value="<?=$type?>" <?= selected( $_GET['type'] ?? '', $type ); ?> >
                            <?= edusystem_get_log_type_label( $type ) ?>
                        </option>
                    <?php endforeach; ?>
                    
                </select>    
                
                <input type="hidden" name="page" value="<?=$_REQUEST['page']?>" />

                <?php if ( empty( $_GET['user_id'] ) ): ?>
                    <?php $logs_table->search_box('search', 'search_id'); ?>
                <?php endif ?>

            </form>

            <?php $logs_table->display(); ?>
        </div>
    <?php
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


