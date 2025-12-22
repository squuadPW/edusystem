<?php 

function updating_student_documents() {   
    $students_documents_table = new Students_Documents_Table();
    $students_documents_table->prepare_items();

    include(plugin_dir_path(__FILE__) . 'templates/updating-student-documents.php');
}


if (!class_exists('WP_List_Table')) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Students_Documents_Table extends WP_List_Table {

    protected $total = 0;
    protected $per_page = 20;

    function __construct() {
        parent::__construct(array(
            'singular' => 'student_document',
            'plural' => 'student_documents',
            'ajax' => false
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'document':
            case 'type_files':
            case 'required':
                return isset($item[$column_name]) ? ucwords($item[$column_name]) : '';
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'document' => __('Document', 'edusystem'),
            'type_files' => __('Type Files', 'edusystem'),
            'required' => __('Required', 'edusystem'),
        ];
    }
        
    function get_sortable_columns() {
        $sortable_columns = [
            'document' => ['document', false],
            'type_files' => ['type_files', false],
            'required' => ['required', false],
        ];
        return $sortable_columns;
    }

    function usort_reorder($a, $b) {
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id';
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
        
        if (!isset($a[$orderby]) || !isset($b[$orderby])) {
            return 0;
        }
        
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : -$result;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="documents[]" value="%d-%d" />',
            $item['doc_id'],
            $item['automatic']
        );
    }

    function column_required($item) {

        $required = $item['required'];
        if ( $item['required'] ) {  
            $required = 'Yes';
        } else {
            $required = 'No';
        }

        return $required;
    }

    function get_bulk_actions() {
        return [
            'mark_required'   => __('Mark as Required', 'edusystem'),
            'mark_not_required' => __('Mark as Not Required', 'edusystem'),
        ];
    }

    function process_bulk_action() {

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';

        if ( !empty($_GET['documents']) ) {

            $where = '';

            foreach ( $_GET['documents'] as $document ) {

                list($doc_id, $automatic) = explode( "-", $document );

                if ( !empty($_GET['from_period']) ) {

                    $where = $wpdb->prepare(
                        " AND `s`.academic_period LIKE %s ",
                        $_GET['from_period']
                    );

                    if (!empty($_GET['from_period_cut'])) {
                        $where .= $wpdb->prepare(
                            " AND `s`.initial_cut LIKE %s ",
                            $_GET['from_period_cut']
                        );
                    }
                }

                // Acción masiva
                switch ( $this->current_action() ) {
                    case 'mark_required':
                        $is_required = 1;
                        break;

                    case 'mark_not_required':
                        $is_required = 0;
                        break;
                }

                // actualizar el documento
                $wpdb->query( $wpdb->prepare(
                    "UPDATE {$table_student_documents} `sd` 
                    INNER JOIN {$table_students} `s` ON `sd`.student_id = `s`.id
                    SET `sd`.is_required = %d
                    WHERE `sd`.doc_id = %d
                    AND `sd`.automatic = %d 
                    {$where} ",
                    (int) $is_required,
                    (int) $doc_id,    
                    (int) $automatic
                ));
            }
        }
    }

    function get_students_documents() {
        
        $documents = [];

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
        
        // Obtener la página actual
        $offset = ( ( $this->get_pagenum() - 1 ) * $this->per_page );
        
        $where = '';
        
        // Obtener fechas de inicio y fin de los cortes seleccionados
        if ( !empty($_GET['from_period']) ) {
            
            $where = $wpdb->prepare(
                " WHERE `s`.academic_period LIKE %s ", 
                $_GET['from_period'],
            );

            if ( !empty($_GET['from_period_cut']) ) {
                $where .= $wpdb->prepare(
                    " AND `s`.initial_cut LIKE %s ",
                    $_GET['from_period_cut']
                );
            }

        }

        $students_documents = $wpdb->get_results( $wpdb->prepare(
            "SELECT `sd`.document_id AS document_name, `sd`.doc_id, `sd`.automatic,
                MIN(`sd`.is_required) AS required,    
                GROUP_CONCAT(DISTINCT `sd`.type_file ORDER BY `sd`.type_file SEPARATOR ', ') AS type_files
            FROM `{$table_student_documents}` AS `sd`
            iNNER JOIN `{$table_students}` AS `s` ON `sd`.student_id = `s`.id
            {$where}
            GROUP BY `sd`.doc_id, `sd`.automatic
            LIMIT %d OFFSET %d ",
            $this->per_page,
            $offset
        ));
        
        if ( $students_documents ) {

            $this->total = count( $students_documents );

            foreach ($students_documents as $document) {
                $documents[] = array(
                    'document' => $document->document_name,
                    'type_files' => $document->type_files,
                    'required' => $document->required,
                    'doc_id' => $document->doc_id,
                    'automatic' => $document->automatic
                );
            }
        }

        return $documents;
    }
    
    function no_items() {
        _e('No student documents found.', 'edusystem');
    }

    function prepare_items() {

        // acciones de bulk
        $this->process_bulk_action();

        // Obtener columnas
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [ $columns, $hidden, $sortable];
        
        // Configurar paginación
        $this->set_pagination_args(array(
            'total_items' => $this->total,
            'per_page' => $this->per_page,
            'total_pages' => ceil($this->total / $this->per_page)
        ));

        // Obtener elementos
        $this->items = $this->get_students_documents();

    }
}


add_action('wp_ajax_get_cuts_by_period', 'get_cuts_by_period_callback');
function get_cuts_by_period_callback() {

    global $wpdb;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

    // Recibir el período enviado por JS
    $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : '';

    if ( $period ) {

        // Consultar cortes asociados al período
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, cut 
             FROM `{$table_academic_periods_cut}` 
             WHERE code LIKE %s",
            $period
        ));

        wp_send_json($results);

    } else {
        wp_send_json([]); // Si no hay período, devolver vacío
    }
}

/* 

SELECT *
FROM wp_academic_periods_cut
WHERE start_date >= '2023-08-12'
  AND end_date <= '2024-12-08';
 */

