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

    public function get_data_log() {

        $conditions = [];
        if ( isset( $_GET['s'] )) {
            
            $keywords = explode(" ", $_GET['s']);

            $meta_query = array('relation' => 'OR');

            foreach ( $keywords as $keyword ) {
                $meta_query[] = array(
                    'key'     => 'first_name',
                    'value'   => $keyword,
                    'compare' => 'LIKE'
                );
                $meta_query[] = array(
                    'key'     => 'last_name',
                    'value'   => $keyword,
                    'compare' => 'LIKE'
                );
            }

            $args = array(
                'fields'     => 'ID', // solo devolver IDs
                'meta_query' => $meta_query
            );

            $user_query = new WP_User_Query($args);
            $user_ids = $user_query->get_results();

            $ids_placeholder = implode(',', array_map('intval', $user_ids));
            $conditions[] = " user_id IN ($ids_placeholder)";
        }

        if ( isset( $_GET['type'] ) && !empty($_GET['type'] ) ) {
            $conditions[] = " type LIKE '{$_GET['type']}' ";
        }

        switch ( $_GET['date'] ?? "" ) {
            case 'today':
                $conditions[] = " DATE(created_at ) = CURDATE()";
                break;
            case 'last_week':
                $conditions[] = " created_at  >= CURDATE() - INTERVAL 7 DAY AND created_at  < CURDATE() + INTERVAL 1 DAY ";
                break;
            case 'last_3_months':
                $conditions[] = " created_at  >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND created_at  < CURDATE() + INTERVAL 1 DAY ";
                break;
            case 'custom':
                $start_date = isset($_GET['startDate']) ? $_GET['startDate'] : "";
                $end_date = isset($_GET['endDate']) ? $_GET['endDate'] : "";
                $conditions[] = "
                    created_at >= STR_TO_DATE('{$start_date}', '%m-%d-%Y')
                    AND created_at < DATE_ADD(STR_TO_DATE('{$end_date}', '%m-%d-%Y'), INTERVAL 1 DAY)
                ";
                break;
            default:
                $conditions[] = " created_at  >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND created_at  < CURDATE() + INTERVAL 1 DAY ";
        }

        $where = "";
        if ($conditions) {
            $where .= " WHERE " . implode(" AND ", $conditions);
        }  
        
        /* $sql .= " ORDER BY `t`.id DESC ";
        $sql .= " LIMIT {$this->per_page} OFFSET {$paged};";  */

        global $wpdb;
        $table_edusystem_log = $wpdb->prefix . 'edusystem_log';

        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $this->per_page;

        $logs = $wpdb->get_results(
           "SELECT *, COUNT(*) OVER() AS total
            FROM $table_edusystem_log 
            $where
            ORDER BY created_at DESC 
            LIMIT {$this->per_page} OFFSET {$offset};
        ");

        // Enriquecer los logs con nombre y rol
        $data = [];
        if( $logs ){

            $this->total_items = $logs[0]->total;

            foreach ($logs as $log) {

                $user_name = __('Unknown', 'edusystem');

                $user = get_userdata( (int) $log->user_id );
                if( $user ) {
                    $first_name = get_user_meta( $user->ID, 'first_name', true );
                    $last_name = get_user_meta( $user->ID, 'last_name', true );

                    $user_name = $first_name.' '.$last_name;
                }

                array_push($data,[
                    'id'         => $log->id,
                    'user'       => $user_name,
                    'type'       => edusystem_get_log_type_label($log->type),
                    'message'    => $log->message,
                    'created_at' => $log->created_at,
                ]);
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
