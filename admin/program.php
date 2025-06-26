<?php

function add_admin_form_program_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'program_details') {
            global $wpdb;
            $program_id = $_GET['program_id'];
            $program = get_program_details($program_id);
            include(plugin_dir_path(__FILE__) . 'templates/program-details.php');

        } else if ($_GET['section_tab'] == 'quotas_rules_programs') {

            global $wpdb;
            $program_id = $_GET['program_id'];
            $identificator = $_GET['identificator'];
            $rules = get_quotas_rules_programs( $identificator );
            include(plugin_dir_path(__FILE__) . 'templates/quotas-rules-programs.php');
        }

    } else {

        if ($_GET['action'] == 'save_program_details') {
            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';
            
            // Sanitizar valores
            $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
            $program_product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $description = strtoupper(sanitize_text_field($_POST['description']));
            $total_price = floatval( sanitize_text_field($_POST['total_price']) );
            $is_active = $_POST['is_active'] ? true : false;
            $subprograms_post = $_POST['subprogram'] ?? '';

            $subprograms = [];// array para guardas los subprogramas

            //crea o actualiza el producto
            if ( !empty($program_id) ) {

                wp_update_post( array(
                    'ID'         => $program_product_id,
                    'post_title' => $name,
                    'post_content' => $description,
                ), true );


                update_post_meta( $program_product_id, '_regular_price', $total_price );
                update_post_meta( $program_product_id, '_price', $total_price );

                // guarda el stock en caso de que este activo o no
                update_post_meta($program_product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');

            } else {

                // Función para crear un producto
                $program_product_id = wp_insert_post([
                    'post_title'   => $name,
                    'post_content' => $description, // Descripción del producto
                    'post_status'  => 'publish',
                    'post_type'    => 'product',
                ]);

                // Verificar si el producto se creó correctamente
                if ( ! is_wp_error( $program_product_id ) ) {

                    update_post_meta( $program_product_id, '_sku', $identificator);
                    update_post_meta( $program_product_id, '_regular_price', $total_price );
                    update_post_meta( $program_product_id, '_price', $total_price );

                    // guarda el stock en caso de que este activo o no
                    update_post_meta($program_product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');
                }
            }

            // obtiene los subprogramas y crea  los productos 
            // vinculados a ellos si los 
            $product = wc_get_product( $program_product_id );
            if( $product && !empty( $subprograms_post ) ){

                $attribute_name = 'subprograms';

                if ( !$product->is_type('variable') ) {

                    // Establecer como producto variable
                    wp_set_object_terms($program_product_id, 'variable', 'product_type');
                    
                    // Crear atributo "subprograma" vacío
                    $product_attributes = array(
                        $attribute_name => array(
                            'name' => __('Subprograms', 'edusystem'),
                            'value' => '',
                            'is_visible' => true,
                            'is_variation' => true,
                            'is_taxonomy' => false
                        )
                    );

                    update_post_meta($program_product_id, '_product_attributes', $product_attributes);
                }

                foreach( $subprograms_post AS $subprogram ){

                    $name_subprogram = $subprogram['name'];
                    $price = $subprogram['price'];
                    $is_active_subprogram = $subprogram['is_active'] ? true : false;
                    
                    // crea o actualiza el producto 
                    if( $subprogram['product_id'] ){

                        $product_id = $subprogram['product_id'];

                        wp_update_post( array(
                            'ID'         => $product_id,
                            'post_title' => $name_subprogram,
                        ), true );

                        update_post_meta( $product_id, '_regular_price', $price );
                        update_post_meta( $product_id, '_price', $price );

                        // guarda el stock en caso de que este activo o no
                        update_post_meta($product_id, '_stock_status', ( $is_active && $is_active_subprogram ) ? 'instock' : 'outofstock');

                    } else  {

                        // Función para crear un producto
                        $product_id = wp_insert_post([
                            'post_title'   => $name_subprogram,
                            'post_name'    => $name_subprogram,
                            'post_status'  => 'publish',
                            'post_type'    => 'product_variation',
                            'post_parent'  => $program_product_id,
                        ]);

                        // Verificar si el producto se creó correctamente
                        if ( ! is_wp_error( $product_id ) ) {
                                
                            update_post_meta( $product_id, '_regular_price', $price );
                            update_post_meta( $product_id, '_price', $price );
                            update_post_meta( $product_id, '_stock_status', 'instock' ); // Estado del stock
                            update_post_meta( $product_id, 'attribute_'.$attribute_name , sanitize_title($name_subprogram));
        
                            // Añadir el término al atributo "subprograms"
                            wp_set_object_terms($program_product_id, $name_subprogram, $attribute_name, true);
                        }
                        
                    }
                    
                    // crea el array con los datos del subprograma
                    $subprogram_data = [
                        'is_active' => $is_active_subprogram ? 1 : 0,
                        'name' => $name_subprogram,
                        'price' => $price,
                        'product_id'=> $product_id ?? null,
                    ];

                    // actualiza en caso de que ya exista o anade un subprograma nuevo
                    if( $subprogram['id'] ) {
                        $subprograms[ $subprogram['id'] ] = $subprogram_data;
                    } else {
                        $subprograms[] = $subprogram_data;
                        update_post_meta( $product_id, '_sku', $identificator."-".( array_key_last( $subprograms ) + 1 ) );
                    }
                }
            }

            // crea o actualiza el sub programa
            if ( !empty($program_id) ) {

                $wpdb->update($table_programs, [
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active,
                    'subprogram' => json_encode($subprograms),
                ], ['id' => $program_id] );

            } else {

                //pone indices a los subprogramas que serviran como ids
                $indices = range(1, count($subprograms));
                $subprograms = array_combine($indices, $subprograms);

                $wpdb->insert($table_programs, [
                    'identificator' => $identificator,
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active,
                    'product_id' => $program_product_id,
                    'subprogram' => json_encode($subprograms),
                ]);
            }
            
            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect( $_SERVER['HTTP_REFERER'] );
            exit;

        } else if ($_GET['action'] == 'save_quotas_rules') {
            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';
            
            // Sanitizar 
            $program_id = $_POST['program_id'] ?? '';
            $identificator = isset($_POST['identificator']) ? sanitize_text_field($_POST['identificator']) : '';

            if( !empty($identificator) ) {
                $rules_post = $_POST['rules'] ?? '';

                foreach( $rules_post AS $rule ){
                    
                    $rule_id = $rule['id'] ?? '';
                    $is_active = $rule['is_active'] ? true : false;
                    $name = $rule['name'];
                    $initial_price = $rule['initial_price'];
                    $quantity = $rule['quantity'];
                    $price = $rule['price'];

                    // crea o actualiza el sub programa
                    if ( !empty( $rule_id ) ) {

                        $wpdb->update( $table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                        ], ['id' => $rule_id] );

                    } else {

                        $wpdb->insert( $table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                            'program_id' => $identificator,
                        ]);
                    }
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect( $_SERVER['HTTP_REFERER'] );

            } else {
                setcookie('message-error', __('Identifier not found', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url("admin.php?page=add_admin_form_program_content&section_tab=program_details"));
            }

            exit;

        } else if ($_GET['action'] == 'delete_quota_rule') {

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';
           
            $rule_id = $_POST['quota_rule_id'];

            $deleted = $wpdb->delete(
                $table_quota_rules,
                ['id' => $rule_id], 
                ['%d'] 
            );

            if( $deleted ) {
                setcookie('message', __('The quota rule has been deleted successfully.', 'edusystem'), time() + 10, '/');
            } else {
                setcookie('message-error', __('The quota rule has not been deleted correctly.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'delete_subprogram') {

            $subprogram_id = $_POST['subprogram_id'];

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';
            $table_students = $wpdb->prefix . 'students';

            // Verificar si hay registros en table_y que coincidan con x_id
            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s",
                $subprogram_id
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ( $exists == 0 ) {

                $separacion = strpos($subprogram_id, '_');
                if ( $posicion !== false) {
                    $program_id = substr($subprogram_id, 0, $separacion);
                    $subprogram_indice = substr($subprogram_id, $separacion + 1);
                }

                echo $program_id . ' ' . $subprogram_indice;

            }

            /* if( $deleted ) {
                setcookie('message', __('The applet has been successfully removed.', 'edusystem'), time() + 10, '/');
            } else {
                setcookie('message-error', __('The applet could not be removed successfully.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;
            */

        } else {
            $list_program = new TT_All_Program_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-program.php');
        }
    }
}

class TT_All_Program_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'program',
                'plural' => 'programs',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_program_content&section_tab=program_details&program_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'identificator' => __('Identificator', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'price' => __('Price', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $programs_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_programs = $wpdb->prefix . 'programs';
        $programs = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_programs} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($programs) {
            foreach ($programs as $pensum) {
                array_push($programs_array, [
                    'id' => $pensum['id'],
                    'identificator' => $pensum['identificator'],
                    'status' => $pensum['is_active'] ? 'Active' : 'Inactive',
                    'name' => $pensum['name'],
                    'price' => $pensum['total_price'],
                    'description' => $pensum['description'],
                    'created_at' => $pensum['created_at'],
                ]);
            }
        }

        return ['data' => $programs_array, 'total_count' => $total_count];
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items()
    {

        $other_data = $this->get_pensum();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $other_data['data'];
        $total_count = (int) $other_data['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }

}

function get_program_details($id)
{
    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $program = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE id={$id}");
    return $program;
}

/**
 * Obtiene las reglas de cuotas asociadas a un programa específico
 * Realiza una consulta a la tabla de reglas de cuotas (quota_rules)
 * filtrando por el identificador del programa especificado
 * 
 * @param string $program_id Identificador del programa a consultar
 * 
 * @return object|false Objeto con los resultados de la consulta
 *                     o false si no hay resultados
 */
function get_quotas_rules_programs($program_id) {

    global $wpdb;
    $table_quota_rules = $wpdb->prefix . 'quota_rules';
    
    $rules = $wpdb->get_results(
        "SELECT * FROM {$table_quota_rules} WHERE program_id = '$program_id'", 
        ARRAY_A
    );
    
    return $rules ?? false;
}


