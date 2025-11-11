<?php 

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
        ]
    );
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
function edusystem_show_logs_table() { ?>
    <div class="wrap">
        <h1><?= esc_html(__('Edusystem Logs', 'edusystem')); ?></h1>

        <?php
            if (!class_exists('WP_List_Table')) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

            $logs_table = new Edusystem_Log_Table();
            $logs_table->prepare_items();
            $logs_table->display();
        ?>
    </div>
<?php }


if (!class_exists('WP_List_Table')) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Edusystem_Log_Table extends WP_List_Table {

    protected $total_items = 0;
    protected $per_page = 50;

    public function __construct() {
        parent::__construct([
            'singular' => __('Log', 'edusystem'),
            'plural'   => __('Logs', 'edusystem'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'id'         => __('ID', 'edusystem'),
            'user'       => __('User', 'edusystem'),
            'role'       => __('Role', 'edusystem'),
            'message'    => __('Description', 'edusystem'),
            'type'       => __('Type', 'edusystem'),
            'created_at' => __('Date', 'edusystem')
        ];
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'         => array('id', true) ,
            'user'       => array('user', false ),
            'role'       => array('role', false  ),
            'type'       => array('type', false  ) ,
            'message'    => array('message', false  ) ,
            'created_at' => array('created_at', false  ),
        );
        return $sortable_columns;
    }

    // Función de ordenamiento 
    public function usort_reorder($a, $b) { 
        // Si no hay ordenamiento, el valor predeterminado es user_login 
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'user_login'; 

        // Si no hay ordenamiento, el valor predeterminado es asc 
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc'; 

        // Determinar el ordenamiento 
        $result = strcmp($a[$orderby], $b[$orderby]); 

        // Enviar la dirección de ordenamiento final a usort 
        return ($order === 'asc') ? $result : -$result; 
    }

    public function get_data_log() {
        global $wpdb;
        $table = $wpdb->prefix . 'edusystem_log';

        /* $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page; */

        $this->$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        $logs = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

        // Enriquecer los logs con nombre y rol
        $data = [];
        if( $logs ){

            foreach ($logs as $log) {
                
                $user = get_userdata( (int) $log->user_id );
                $display_name = $user ? $user->display_name : __('Unknown', 'edusystem');
                $role = $user && !empty($user->roles) ? $user->roles[0] : __('None', 'edusystem');
               
                array_push($data,[
                    'id'         => $log->id,
                    'user'       => $display_name,
                    'role'       => $role,
                    'type'       => edusystem_get_log_type_label($log->type),
                    'message'    => $log->message,
                    'created_at' => $log->created_at,
                ]);
            }
        }
        return $data;
       
    }

    public function column_default($item, $column_name) {
        return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
    }

    public function prepare_items(){
        $data = $this->get_data_log();
    
        $columns = $this->get_columns(); 
        $hidden = array(); 
        usort( $data, array( &$this, 'usort_reorder' ) );
        $sortable = $this->get_sortable_columns(); 

        $this->_column_headers = array( $columns ,$hidden , $sortable );
        $this->process_bulk_action();

        $this->set_pagination_args( array(
            'total_items' => $this->total_items,
            'per_page'    => $this->per_page, 
            'total_pages' => ceil($this->total_items / $this->per_page)
        ) );

        $this->items = $data;
        
    }
}

// Registra el logeo del usuario
add_action('wp_login', function ($user_login, $user) {

    $message = sprintf(__('User %s logged in', 'edusystem'), $user_login);
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

        $display_name = $user->display_name;
        $rol = !empty($user->roles) ? $user->roles[0] : __('user','edusystem');

        // Mensaje traducible con nombre y rol
        $message = sprintf(__('The %s %s session closed', 'edusystem'), $rol, $display_name );

        // Registrar el log
        edusystem_get_log($message, 'logout', $user_id);

        // Limpiar el transient
        delete_transient('last_logout_user');
    }
});

// obtiene los textos del tipo de log
function edusystem_get_log_type_label($type) {
    $types = [
        'login'     => __('User logged in', 'edusystem'),
        'logout'    => __('User logged out', 'edusystem'),
        'error'     => __('System error', 'edusystem'),
        'warning'   => __('System warning', 'edusystem'),
        'info'      => __('Information', 'edusystem'),
        'update'    => __('Data updated', 'edusystem'),
        'create'    => __('Data created', 'edusystem'),
        'delete'    => __('Data deleted', 'edusystem'),
        'access'    => __('Access attempt', 'edusystem'),
        'permission'=> __('Permission change', 'edusystem'),
    ];

    return isset($types[$type]) ? $types[$type] : $type;
}


