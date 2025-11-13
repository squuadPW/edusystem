<?php

if (!class_exists('WP_List_Table')) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Edusystem_Log_Table extends WP_List_Table {

    protected $total_items = 0;
    protected $per_page = 20;

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
            'message'    => __('Description', 'edusystem'),
            'type'       => __('Type', 'edusystem'),
            'created_at' => __('Date', 'edusystem')
        ];
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'         => array('id', true) ,
            'user'       => array('user', false ),
            'type'       => array('type', false  ) ,
            'message'    => array('message', false  ) ,
            'created_at' => array('created_at', false  ),
        );
        return $sortable_columns;
    }

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

    public function column_default($item, $column_name) {
        return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
    }

    public function column_user($item) {
        $user_id = $item['user_id']; 

        $url = add_query_arg(
            array( 'user_id' => $user_id ),
            admin_url('user-edit.php')
        );

        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($url),
            esc_html($item['user'])
        );
    }


    public function column_message($item) {
        // Permitir solo etiquetas seguras como <a>
        return wp_kses(
            $item['message'],
            array(
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                ),
            )
        );
    }

    public function get_data_log() {

        global $wpdb;
        $table_edusystem_log = $wpdb->prefix . 'edusystem_log';

        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $this->per_page;
        $conditions = [];

        if ( ! empty( $_GET['user_id'] ) ) {
            $user_id = intval( $_GET['user_id'] );
            $conditions[] = $wpdb->prepare( " l.user_id = %d", $user_id );
        }

        if ( isset($_GET['s']) && !empty($_GET['s']) ) {
            global $wpdb;
            $search = trim($_GET['s']);
            $like   = '%' . $wpdb->esc_like($search) . '%';

            // JOIN con usermeta para obtener first_name y last_name
            $conditions[] = $wpdb->prepare("
                (
                    CONCAT_WS(' ', fn.meta_value, ln.meta_value) LIKE %s
                    OR u.display_name LIKE %s
                    OR u.user_login LIKE %s
                    OR u.user_nicename LIKE %s
                )
            ", $like, $like, $like, $like);
        }

        // Filtro por tipo
        if ( isset($_GET['type']) && !empty($_GET['type']) ) {
            $conditions[] = $wpdb->prepare(" l.type LIKE %s ", $_GET['type']);
        }

        // Filtro por fecha
        switch ($_GET['date'] ?? "") {
            case 'today':
                $conditions[] = " DATE(l.created_at) = CURDATE()";
                break;
            case 'last_week':
                $conditions[] = " l.created_at >= CURDATE() - INTERVAL 7 DAY AND l.created_at < CURDATE() + INTERVAL 1 DAY ";
                break;
            case 'last_3_months':
                $conditions[] = " l.created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND l.created_at < CURDATE() + INTERVAL 1 DAY ";
                break;
            case 'custom':
                $start_date = $_GET['startDate'] ?? "";
                $end_date   = $_GET['endDate'] ?? "";
                $conditions[] = $wpdb->prepare("
                    l.created_at >= STR_TO_DATE(%s, '%%m-%%d-%%Y')
                    AND l.created_at < DATE_ADD(STR_TO_DATE(%s, '%%m-%%d-%%Y'), INTERVAL 1 DAY)
                ", $start_date, $end_date);
                break;
            default:
                $conditions[] = " l.created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND l.created_at < CURDATE() + INTERVAL 1 DAY ";
        }

        $where = "";
        if ($conditions) {
            $where = " WHERE " . implode(" AND ", $conditions);
        }

        // consulta principal
        $logs = $wpdb->get_results(
            "SELECT l.*, 
                    u.display_name, 
                    fn.meta_value AS first_name, 
                    ln.meta_value AS last_name, 
                    COUNT(*) OVER() AS total
            FROM {$table_edusystem_log} l
            INNER JOIN {$wpdb->users} u ON u.ID = l.user_id
            LEFT JOIN {$wpdb->usermeta} fn ON fn.user_id = u.ID AND fn.meta_key = 'first_name'
            LEFT JOIN {$wpdb->usermeta} ln ON ln.user_id = u.ID AND ln.meta_key = 'last_name'
            $where
            ORDER BY l.created_at DESC 
            LIMIT {$this->per_page} OFFSET {$offset};"
        );

        // Enriquecer los logs con nombre y rol
        $data = [];
        if( $logs ){

            $this->total_items = $logs[0]->total;

            foreach ($logs as $log) {
                
                $first_name = $log->first_name ?? '';
                $last_name  = $log->last_name ?? '';
                $user_name  = trim($first_name . ' ' . $last_name);

                if (empty($user_name)) {
                    $user_name = $log->display_name ?: __('Unknown', 'edusystem');
                }

                $data[] = [
                    'id'         => $log->id,
                    'user_id'       =>  $log->user_id,
                    'user'       => $user_name,
                    'type'       => edusystem_get_log_type_label($log->type),
                    'message'    => $log->message,
                    'created_at' => $log->created_at,
                ];
            }
        }
        return $data;
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
