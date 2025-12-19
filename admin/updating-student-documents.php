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
            case 'id':
            case 'document':
            case 'type_files':
                return isset($item[$column_name]) ? ucwords($item[$column_name]) : '';
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        return [
            'id' => __('ID', 'edusystem'),
            'document' => __('Document', 'edusystem'),
            'type_files' => __('Type Files', 'edusystem')
        ];
    }
        
    function get_sortable_columns() {
        $sortable_columns = [
            'id' => ['id', false],
            'document' => ['document', false],
            'type_files' => ['type_files', false],
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

    function get_students_documents() {
        
        $documents = [];

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        
        // Obtener la página actual
        $offset = ( ( $this->get_pagenum() - 1 ) * $this->per_page );
        
        /* // Inicializar condiciones WHERE
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
        } */
        
        
        /* // Agregar condiciones WHERE si existen
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }
         */
        /* 
        
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
        } */

        $students_documents = $wpdb->get_results( $wpdb->prepare(
            "SELECT `sd`.id, `sd`.document_id AS document_name,
                GROUP_CONCAT(DISTINCT `sd`.type_file ORDER BY `sd`.type_file SEPARATOR ', ') AS type_files
            FROM `{$table_student_documents}` AS `sd`
            GROUP BY `sd`.doc_id, `sd`.automatic
            LIMIT %d OFFSET %d ",
            $this->per_page,
            $offset
        ));
        

        if ( $students_documents ) {

            $this->total = count( $students_documents );

            foreach ($students_documents as $document) {
                $documents[] = array(
                    'id' => $document->id,
                    'document' => $document->document_name,
                    'type_files' => $document->type_files
                );
            }
        }

        return $documents;
    }
    
    function extra_tablenav( $which ) {

        if ( $which == 'top' ) {
            
                global $wpdb;
                $table_academic_periods = $wpdb->prefix . 'academic_periods';
                $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
                
                // Obtener valores 
                $academic_periods = $wpdb->get_results( "SELECT * FROM `{$table_academic_periods}`" );
                
                // Obtener valores actuales de los filtros
                $from_period = isset($_GET['from_period']) ? $_GET['from_period'] : '';
                $from_period_cut = isset($_GET['from_period_cut']) ? $_GET['from_period_cut'] : '';
                $to_period = isset($_GET['to_period']) ? $_GET['to_period'] : '';
                $to_period_cut = isset($_GET['to_period_cut']) ? $_GET['to_period_cut'] : '';

            ?>  
                <form id="accions-docummets" method="get" >

                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? esc_attr($_GET['page']) : ''; ?>" />

                    <div class="group" >

                        <h3><?= __('From:','edusystem') ?></h3>

                        <div>
                            <label for="from_period" >

                                <b><?= __('Academic Period:', 'edusystem'); ?></b>

                                <select id="from-period" name="from_period" >
                                    <option value=""><?= __('select academic period', 'edusystem'); ?></option>
                                    <?php foreach ($academic_periods as $period): ?>
                                        <option value="<?= esc_attr($period->code); ?>" <?php selected($from_period, $period->code); ?>>
                                            <?= esc_html($period->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                    
                            <label for="from_period_cut"  >

                                <b><?= __('Cuts:', 'edusystem'); ?></b>

                                <select id="from-period-cut" name="from_period_cut" data-selected="<?= esc_attr($from_period_cut); ?>" >
                                    <option value=""><?= __('select academic cut', 'edusystem'); ?></option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="group" >

                        <h3><?= __('To:','edusystem') ?></h3>
                        
                        <div>
                            <label for="to_period" >

                                <b><?= __('Academic Period:', 'edusystem'); ?></b>

                                <select id="to-period" name="to_period">
                                    <option value=""><?= __('select academic period', 'edusystem'); ?></option>
                                    <?php foreach ($academic_periods as $period): ?>
                                        <option value="<?= esc_attr($period->code); ?>" <?php selected($to_period, $period->code); ?>>
                                            <?= esc_html($period->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                    
                            <label for="to_period_cut" >

                                <b><?= __('Cuts:', 'edusystem'); ?></b>

                                <select id="to-period-cut" name="to_period_cut" data-selected="<?= esc_attr($to_period_cut); ?>">
                                    <option value=""><?= __('select academic cut', 'edusystem'); ?></option>
                                </select>
                            </label>
                        </div>
                        
                    </div>
                    
                    <div class="group-accion" >
                        <?php 
                            submit_button(__('Filter', 'edusystem'), 'primary', 'filter_action', false, array('id' => 'post-query-submit')); 
                        ?>

                        <?php if (!empty($_GET['filter_action']) || !empty($_GET['filter_action'])): ?>
                            <a href="<?= remove_query_arg(['filter_action','from_period', 'from_period_cut', 'to_period', 'to_period_cut', 'paged']); ?>" class="button">
                                <?= __('Clear Filters', 'edusystem'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    
                </form>
            <?php
        }
    }
    
    function no_items() {
        _e('No student documents found.', 'edusystem');
    }

    function prepare_items() {

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



