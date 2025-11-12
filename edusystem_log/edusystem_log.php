<?php 

// script y styles para el log
add_action('admin_enqueue_scripts', function () {

    wp_enqueue_style('styles-log', EDUSYSTEM_URL . '/edusystem_log/assets/css/styles.css');

    wp_enqueue_script('scripts-log', EDUSYSTEM_URL . '/edusystem_log/assets/js/scripts.js');

    // Encola Flatpickr para la selección de fechas
    wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js');

});

// tipos de log de edusistem
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

// craea la tabla en base de datos
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

// Registra log en la tabla de log de edusystem
function edusystem_get_log( $message, $type = 'info', $user_id = null) {

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

// obtiene los textos del tipo de log
function edusystem_get_log_type_label( $type ) {
    return isset( EDUSYSTEM_TYPE_LOGS[$type]) ? EDUSYSTEM_TYPE_LOGS[$type] : $type;
}

// agraga la seccion de log del edusystem
add_action('admin_menu', 'edusystem_add_logs_page');
function edusystem_add_logs_page() {
    add_menu_page(
        __('Edusystem Logs', 'edusystem'),     // Page title
        __('Edusystem Logs', 'edusystem'),               // Menu title
        'manage_options',
        'edusystem-logs',
        'edusystem_show_logs_table',
        'dashicons-list-view',
        80
    );
}

// muestra la tabla de los logs del sistema
function edusystem_show_logs_table() { 

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

// Añade el link de los logs de edusystem del usuario 
add_action('personal_options', 'mi_link_en_personal_options');
function mi_link_en_personal_options($user) {
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
}

// Registra el logeo del usuario
add_action('wp_login', function ($user_login, $user) {

    $first_name = get_user_meta($user->ID, 'first_name', true);
    $last_name  = get_user_meta($user->ID, 'last_name', true);

    $message = sprintf(__('User %s logged in', 'edusystem'), $first_name.' '.$last_name);
    edusystem_get_log( $message, 'login', $user->ID);

}, 10, 2);

// Guardamos el usuario que está a punto de cerrar sesión
add_action('clear_auth_cookie', function () {
    $user = wp_get_current_user();
    if ($user && $user->ID) {
        // Guardar el ID en un transient temporal
        set_transient('last_logout_user', $user->ID, 60); // dura 1 minuto
    }
});

// Registra que el usuario cerro seccion
add_action('wp_logout', function () {
    
    // Obtener el usuario actual antes de que se cierre la sesión
    $user_id = get_transient('last_logout_user');
    if ( $user_id ) {

        $user = get_userdata($user_id);

        $first_name   = get_user_meta( $user->ID, 'first_name', true );
        $last_name = get_user_meta( $user->ID, 'last_name', true );

        // Mensaje traducible con nombre y rol
        $message = sprintf(__('The user %s session closed', 'edusystem'), $first_name.' '.$last_name);

        // Registrar el log
        edusystem_get_log($message, 'logout', $user_id);

        // Limpiar el transient
        delete_transient('last_logout_user');
    }
});

// registra cuando un studiante es actualizado
add_action('edusystem_save_student_data', function ( $student_id ) {

    $user = wp_get_current_user();
    if ($user && $user->ID) {

        $first_name   = get_user_meta( $user->ID, 'first_name', true );
        $last_name = get_user_meta( $user->ID, 'last_name', true );
        $name_user = $first_name.' '.$last_name;
        
        $name_student = '';
        if( $student_id ){
            $first_name   = get_user_meta( (int) $student_id, 'first_name', true );
            $last_name = get_user_meta( (int) $student_id, 'last_name', true );
            $name_student = $first_name.' '.$last_name;
        }   

        // Mensaje traducible con nombre y rol
        $message = sprintf(__('User %s has updated the data for student %s', 'edusystem'), $name_user, $name_student);

        // Registrar el log
        edusystem_get_log($message, 'save_student_data', $user->ID);

    }
});






