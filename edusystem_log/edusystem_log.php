<?php 

register_activation_hook( EDUSYSTEM__FILE__, function () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_edusystem_log = $wpdb->prefix . 'edusystem_log';// tabla de log de edusystem
    dbDelta( "CREATE TABLE $table_edusystem_log (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `message` TEXT NOT NULL,
        `type` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;");
});

function edusystem_get_log( $message, $type = 'info', $user_id = false ) {

    // Si no se proporciona user_id, usar el actual
    $user_id = $user_id ?? get_current_user_id();
    
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'edusystem_log',
        [   
            'user_id' => intval($user_id),
            'message' => $message,
            'type'    => $type,
        ]
    );
}

add_action('admin_menu', 'edusystem_agregar_pagina_logs');
function edusystem_agregar_pagina_logs() {
    add_menu_page(
        'Logs de Edusystem',
        'Logs Edusystem',
        'manage_options',
        'edusystem-logs',
        'edusystem_mostrar_tabla_logs',
        'dashicons-list-view',
        80
    );
}

function edusystem_mostrar_tabla_logs() {

    echo '<div class="wrap">';
        echo '<h1>Logs de Edusystem</h1>';

        if (!class_exists('WP_List_Table')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }

        $tabla_logs = new Edusystem_Log_Table();
        $tabla_logs->prepare_items();
        $tabla_logs->display();

    echo '</div>';
}

if (!class_exists('WP_List_Table')) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Edusystem_Log_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => 'log',
            'plural'   => 'logs',
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'id'      => 'ID',
            'message' => 'Mensaje',
            'type'    => 'Tipo',
            'data'    => 'Fecha'
        ];
    }

    public function prepare_items() {
        global $wpdb;
        $tabla = $wpdb->prefix . 'edusystem_log';

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $tabla");
        $logs = $wpdb->get_results("SELECT * FROM $tabla ORDER BY data DESC LIMIT $offset, $per_page", ARRAY_A);

        $this->items = $logs;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }
}

// 
add_action('wp_login', 'edusystem_usuario_logeado', 10, 2);
function edusystem_usuario_logeado($user_login, $user) {
    // Aquí puedes registrar el log
    error_log($user_login);
    // edusystem_guardar_log("El usuario {$user_login} inició sesión", 'login');
}





