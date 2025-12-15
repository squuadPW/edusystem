<?php 

function updating_student_documents()
{   
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
            case 'id':
            case 'student':
            case 'document':
                return isset($item[$column_name]) ? ucwords($item[$column_name]) : '';
            case 'status':
                if (function_exists('get_status_document')) {
                    return ucwords(get_status_document($item['id']));
                }
                return isset($item['status']) ? ucwords($item['status']) : '';
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'document' => __('Document', 'edusystem'),
            'status' => __('Status', 'edusystem'),
        );
        return $columns;
    }
        
    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('sd.id', false),
        );
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

    function get_students_documents() {
        global $wpdb;
        
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        
        // Obtener la página actual
        $current_page = $this->get_pagenum();
        $offset = (($current_page - 1) * $this->per_page);
        
        // Inicializar condiciones WHERE
        $where_conditions = array();
        $where_values = array();
        
        // Filtrar por academic_period si está establecido
        if (isset($_GET['academic_period']) && $_GET['academic_period'] !== '') {
            $where_conditions[] = "s.academic_period = %s";
            $where_values[] = sanitize_text_field($_GET['academic_period']);
        }
        
        // Filtrar por initial_cut si está establecido
        if (isset($_GET['initial_cut']) && $_GET['initial_cut'] !== '') {
            $where_conditions[] = "s.initial_cut = %s";
            $where_values[] = sanitize_text_field($_GET['initial_cut']);
        }
        
        // CONCATENAR TODOS LOS CAMPOS DEL NOMBRE DEL ESTUDIANTE
        $sql = "SELECT sd.id, 
                       CONCAT_WS(' ', 
                           s.name, 
                           s.middle_name, 
                           s.last_name, 
                           s.middle_last_name
                       ) AS student_name,
                       sd.document_id AS document_identifier, 
                       sd.status  
                FROM {$table_student_documents} AS sd
                INNER JOIN {$table_students} AS s ON sd.student_id = s.id";
        
        // Agregar condiciones WHERE si existen
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $sql .= " LIMIT %d OFFSET %d";
        
        // Agregar límites a los valores
        $where_values[] = $this->per_page;
        $where_values[] = $offset;
        
        // Preparar y ejecutar consulta
        if (!empty($where_conditions)) {
            $students_documents = $wpdb->get_results(
                $wpdb->prepare($sql, $where_values)
            );
        } else {
            $students_documents = $wpdb->get_results(
                $wpdb->prepare($sql, $this->per_page, $offset)
            );
        }

        $documents = array();

        if ($students_documents) {
            foreach ($students_documents as $document) {
                $documents[] = array(
                    'id' => $document->id,
                    'student' => $document->student_name,
                    'document' => $document->document_identifier,
                    'status' => $document->status,
                );
            }
        }

        return $documents;
    }
    
    function get_total_count() {
        global $wpdb;
        
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        
        // Inicializar condiciones WHERE
        $where_conditions = array();
        $where_values = array();
        
        // Filtrar por academic_period si está establecido
        if (isset($_GET['academic_period']) && $_GET['academic_period'] !== '') {
            $where_conditions[] = "s.academic_period = %s";
            $where_values[] = sanitize_text_field($_GET['academic_period']);
        }
        
        // Filtrar por initial_cut si está establecido
        if (isset($_GET['initial_cut']) && $_GET['initial_cut'] !== '') {
            $where_conditions[] = "s.initial_cut = %s";
            $where_values[] = sanitize_text_field($_GET['initial_cut']);
        }
        
        $sql = "SELECT COUNT(*)
                FROM {$table_student_documents} AS sd
                INNER JOIN {$table_students} AS s ON sd.student_id = s.id";
        
        // Agregar condiciones WHERE si existen
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        if (!empty($where_conditions)) {
            return (int) $wpdb->get_var($wpdb->prepare($sql, $where_values));
        } else {
            return (int) $wpdb->get_var($sql);
        }
    }
    
    function extra_tablenav($which) {
        if ($which == 'top') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            
            // Obtener valores únicos para los filtros
            $academic_periods = $wpdb->get_col("SELECT DISTINCT academic_period FROM {$table_students} WHERE academic_period IS NOT NULL ORDER BY academic_period");
            $initial_cuts = $wpdb->get_col("SELECT DISTINCT initial_cut FROM {$table_students} WHERE initial_cut IS NOT NULL ORDER BY initial_cut");
            
            // Obtener valores actuales de los filtros
            $current_academic_period = isset($_GET['academic_period']) ? $_GET['academic_period'] : '';
            $current_initial_cut = isset($_GET['initial_cut']) ? $_GET['initial_cut'] : '';
            ?>
            <div class="alignleft actions">
                <label for="filter-academic-period" style="margin-right:10px;">
                    <?php _e('Academic Period:', 'edusystem'); ?>
                    <select name="academic_period" id="filter-academic-period" style="margin-left:5px;">
                        <option value=""><?php _e('All Periods', 'edusystem'); ?></option>
                        <?php foreach ($academic_periods as $period): ?>
                            <option value="<?php echo esc_attr($period); ?>" <?php selected($current_academic_period, $period); ?>>
                                <?php echo esc_html($period); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                
                <label for="filter-initial-cut" style="margin-right:10px; margin-left:15px;">
                    <?php _e('Initial Cut:', 'edusystem'); ?>
                    <select name="initial_cut" id="filter-initial-cut" style="margin-left:5px;">
                        <option value=""><?php _e('All Cuts', 'edusystem'); ?></option>
                        <?php foreach ($initial_cuts as $cut): ?>
                            <option value="<?php echo esc_attr($cut); ?>" <?php selected($current_initial_cut, $cut); ?>>
                                <?php echo esc_html($cut); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? esc_attr($_GET['page']) : ''; ?>" />
                <?php submit_button(__('Filter', 'edusystem'), 'secondary', 'filter_action', false, array('id' => 'post-query-submit')); ?>
                
                <?php if (!empty($_GET['academic_period']) || !empty($_GET['initial_cut'])): ?>
                    <a href="<?php echo remove_query_arg(array('academic_period', 'initial_cut', 'paged')); ?>" class="button" style="margin-left:5px;">
                        <?php _e('Clear Filters', 'edusystem'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php
        }
    }
    
    function no_items() {
        _e('No student documents found.', 'edusystem');
    }

    function prepare_items() {
        // Obtener columnas
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        
        // CALCULAR EL TOTAL
        $this->total = $this->get_total_count();
        
        // Configurar paginación
        $this->set_pagination_args(array(
            'total_items' => $this->total,
            'per_page' => $this->per_page,
            'total_pages' => ceil($this->total / $this->per_page)
        ));

        // Obtener elementos
        $this->items = $this->get_students_documents();
        
        // Ordenar si es necesario
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            usort($this->items, array($this, 'usort_reorder'));
        }
    }
}




