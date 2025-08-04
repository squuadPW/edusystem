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
            
            // verifica y crea en caso de necesitar una categoria llamada programs;"
            $category_id = 0;
            $name_category = 'programs';
            $category = term_exists($name_category, 'product_cat');
            if ( $category ) {
                $category_id = (int) $category['term_id'];

            } else {
                // La categoría no existe, crearla
                $category = wp_insert_term($name_category, 'product_cat');
                if ( !is_wp_error($category) ) {
                    $category_id = (int) $category['term_id'];// Devolver el ID de la nueva categoría creada
                } 
            }

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

                // Asignar la categoría al producto
                wp_set_object_terms($program_product_id, $category_id, 'product_cat');

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

                    // Asignar la categoría al producto
                    wp_set_object_terms($program_product_id, (int) $category_id, 'product_cat');
                }
            }

            // obtiene los subprogramas y crea  los productos 
            // vinculados a ellos si los 
            $product = wc_get_product( (int) $program_product_id );
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

                        wp_set_object_terms($product_id, (int) $category_id, 'product_cat');

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

                            wp_set_object_terms($product_id, (int) $category_id, 'product_cat');
        
                            // Añadir el término al atributo "subprograms"
                            wp_set_object_terms($program_product_id, $name_subprogram, $attribute_name, true);
                            
                            // Actualizar el valor del atributo "subprogramas"
                            $current_values = get_post_meta($program_product_id, '_product_attributes', true);
                            if (isset($current_values[$attribute_name])) {
                                $current_values[$attribute_name]['value'] .= (empty($current_values[$attribute_name]['value']) ? '' : '| ') . $name_subprogram;
                            } 
                            update_post_meta($program_product_id, '_product_attributes', $current_values);
                        }
                        
                    }
                    
                    // crea el array con los datos del subprograma
                    $subprogram_data = [
                        'is_active' => $is_active_subprogram ? 1 : 0,
                        'name' => $name_subprogram,
                        'price' => $price,
                        'product_id'=> (string) $product_id ?? null,
                    ];

                    // actualiza en caso de que ya exista o anade un subprograma nuevo
                    if( $subprogram['id'] ) {
                        $subprograms[ $subprogram['id'] ] = $subprogram_data;
                    } else {
                        $subprograms[ ( array_key_last( $subprograms ) ?? 0 ) + 1] = $subprogram_data;
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
                    'subprogram' => json_encode( $subprograms ) ?? null,
                ], ['id' => $program_id] );

            } else {

                if ( !empty($subprograms) ) {
                    //pone indices a los subprogramas que serviran como ids
                    $index = range(1, count($subprograms));
                    $subprograms = array_combine($index, $subprograms);

                    $subprogram = json_encode($subprograms);
                } else {
                    $subprogram = null;
                }

                $wpdb->insert($table_programs, [
                    'identificator' => $identificator,
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active,
                    'product_id' => $program_product_id,
                    'subprogram' => $subprogram,
                ]);

                $program_id = $wpdb->insert_id;
            }
            
            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect( admin_url( 'admin.php?page=add_admin_form_program_content&section_tab=program_details&program_id='.$program_id ) );
            exit;

        } else if ( $_GET['action'] == 'save_quotas_rules') {
            
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
                    $frequency_value = $rule['frequency_value'];
                    $type_frequency = $rule['type_frequency'];
                    $position = $rule['position'] ?? 0;

                    // crea o actualiza el sub programa
                    if ( !empty( $rule_id ) ) {

                        $wpdb->update( $table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'position' => $position,
                        ], ['id' => $rule_id] );

                    } else {

                        $wpdb->insert( $table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                            'program_id' => $identificator,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'position' => $position,
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

        } else if ( $_GET['action'] == 'delete_quota_rule' ) {

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

        } else if ( $_GET['action'] == 'delete_subprogram' ) {

            $subprogram_id = $_POST['subprogram_id'];

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s",
                $subprogram_id
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ( $students == 0 ) {

                $separacion = strpos( $subprogram_id, '_' );
                if ( $separacion !== false ) {
                    $program_id = substr( $subprogram_id, 0, $separacion );
                    $subprogram_indice = substr( $subprogram_id, $separacion + 1 );
                }

                $subprogram_data = get_subprogram_by_identificador_program( $program_id );

                // obtiene el id del producto a eliminar
                $product_id = $subprogram_data[$subprogram_indice]['product_id'];

                // elimina el producto
                wp_delete_post($product_id, true);

                // elemina el subprograma
                unset( $subprogram_data[$subprogram_indice] );

                //guardar la subprogramas
                $table_programs = $wpdb->prefix . 'programs';
                $update = $wpdb->update($table_programs, [
                    'subprogram' => json_encode($subprogram_data) ?? null,
                ], ['identificator' => $program_id] );

                if( $update ) {
                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }
        
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ( $_GET['action'] == 'delete_program') {

            $program_id = $_POST['program_id'];

            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';
            $table_quotas_rules = $wpdb->prefix . 'quota_rules';
            $table_students = $wpdb->prefix . 'students';

            $program_data = $wpdb->get_row( $wpdb->prepare(
                "SELECT identificator, product_id FROM $table_programs WHERE id = %d ",
                $program_id,
            ));

            $students = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s || program_id LIKE %s",
                $program_data->identificator,
                $program_data->identificator.'_%'
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ( $students == 0 ) {

                $product = wc_get_product( $program_data->product_id );
                if ( $product ) $product->delete(true); 

                $deleted = $wpdb->delete(
                    $table_programs,
                    ['id' => $program_id], 
                    ['%d'] 
                );

                

                if( $deleted ) {
                    
                    // eliminar las reglas de los quotas
                    $wpdb->query($wpdb->prepare(
                        "DELETE FROM $table_quotas_rules WHERE program_id = %s OR program_id LIKE %s",
                        $program_data->identificator,
                        $program_data->identificator . '_%'
                    ));

                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }
        
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else {
            $list_program = new TT_All_Program_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-program.php');

            // modal de eleiminar un programa
            include(plugin_dir_path(__FILE__).'/templates/modal-delete-program.php');
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
                $buttons .= "<a class='button button-danger' data-program_id='".$item['id']."' onclick='modal_delete_program_js ( this )' ><span class='dashicons dashicons-trash'></span></a>";
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

function get_programs()
{
    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $program = $wpdb->get_results("SELECT * FROM {$table_programs} WHERE is_active=1");
    return $program;
}

function get_program_details($id)
{
    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $program = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE id={$id}");
    return $program;
}

function get_career_details($id)
{
    global $wpdb;
    $table_careers_by_program = $wpdb->prefix . 'careers_by_program';

    $career = $wpdb->get_row("SELECT * FROM {$table_careers_by_program} WHERE id={$id}");
    return $career;
}


function get_mention_details($id)
{
    global $wpdb;
    $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';

    $mention = $wpdb->get_row("SELECT * FROM {$table_mentions_by_career} WHERE id={$id}");
    return $mention;
}


function get_program_details_by_identificator($identificator)
{
    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $program = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE identificator='{$identificator}'");
    return $program;
}

function get_student_program_details_by_identificator($identificator)
{
    global $wpdb;
    $table_student_program = $wpdb->prefix . 'student_program';

    $program = $wpdb->get_row("SELECT * FROM {$table_student_program} WHERE identificator='{$identificator}'");
    return $program;
}

function get_career_details_by_identificator($identificator)
{
    global $wpdb;
    $table_careers_by_program = $wpdb->prefix . 'careers_by_program';

    $program = $wpdb->get_row("SELECT * FROM {$table_careers_by_program} WHERE identificator='{$identificator}'");
    return $program;
}

function get_mention_details_by_identificator($identificator)
{
    global $wpdb;
    $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';

    $program = $wpdb->get_row("SELECT * FROM {$table_mentions_by_career} WHERE identificator='{$identificator}'");
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

/**
 * Obtiene los sub-subprogramas asociados a un programa principal específico.
 * Realiza una consulta a la tabla de programas (programs)
 * filtrando por el identificador del programa especificado.
 * 
 * @param string $identificador Identificador del programa principal a consultar.
 * 
 * @return array Arreglo con los datos de los sub-subprogramas
 *               o un arreglo vacío si no hay resultados.
 * 
 */
function get_subprogram_by_identificador_program( $identificador ) {

    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $subprogram = $wpdb->get_var( $wpdb->prepare(
        "SELECT subprogram FROM $table_programs WHERE identificator LIKE %s",
        $identificador
    ));

    return json_decode( $subprogram, true ) ?? [];
}

function get_subprogram_by_id_program( $program_id ) {

    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $subprogram = $wpdb->get_var( $wpdb->prepare(
        "SELECT subprogram FROM $table_programs WHERE id LIKE %d",
        $program_id
    ));

    return json_decode( $subprogram, true ) ?? [];
}

function get_identificator_by_id_program( $program_id ) {

    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $identificator = $wpdb->get_var( $wpdb->prepare(
        "SELECT identificator FROM $table_programs WHERE id LIKE %d",
        $program_id
    ));

    return strtolower($identificator);
}

function get_product_id_by_identificador_program( $identificador ) {

    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $product_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT product_id FROM $table_programs WHERE identificator LIKE %s",
        $identificador
    ));

    return $product_id;
}

function get_product_id_by_id_program( $program_id ) {

    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $product_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT product_id FROM $table_programs WHERE id LIKE %d",
        $program_id
    ));

    return $product_id;
}

/**
 * Verifica si un identificador de programa ya existe en la base de datos.
 * Esta función se utiliza para comprobar la disponibilidad de un identificador
 * antes de que se realice una acción que dependa de su unicidad.
 * 
 * @return void
 * 
 * @throws WP_Error Si el identificador no es proporcionado o está vacío.
 * 
 * La función envía una respuesta JSON indicando si el identificador ya está en uso
 * o si está disponible. La respuesta incluye un mensaje que puede ser utilizado
 * para informar al usuario sobre el estado del identificador.
 */
add_action('wp_ajax_check_program_identificator_exists', 'check_program_identificator_exists');
add_action('wp_ajax_nopriv_check_program_identificator_exists', 'check_program_identificator_exists');
function check_program_identificator_exists() {
   
    if ( !isset($_POST['identificator']) || empty($_POST['identificator']) ) {
        wp_send_json_error('Identificador no proporcionado');
    }
    
    $identificator = sanitize_text_field($_POST['identificator']);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'programs';
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM $table_name WHERE identificator LIKE %s",
        $identificator
    ));

    if( $exists ){
        wp_send_json_success([
            'exists' => true,
            'message' => __('Identifier in use, please choose another.','edusystem'),
        ]);
    } else {
        wp_send_json_success([
            'exists' => false,
            'message' => __('Identifier is not in use.','edusystem'),
        ]);
    } 
}

add_action('wp_ajax_check_career_identificator_exists', 'check_career_identificator_exists');
add_action('wp_ajax_nopriv_check_career_identificator_exists', 'check_career_identificator_exists');
function check_career_identificator_exists() {
   
    if ( !isset($_POST['identificator']) || empty($_POST['identificator']) ) {
        wp_send_json_error('Identificador no proporcionado');
    }
    
    $identificator = sanitize_text_field($_POST['identificator']);
    
    global $wpdb;
    $table_careers_by_program = $wpdb->prefix . 'careers_by_program';
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM $table_careers_by_program WHERE identificator LIKE %s",
        $identificator
    ));

    if( $exists ){
        wp_send_json_success([
            'exists' => true,
            'message' => __('Identifier in use, please choose another.','edusystem'),
        ]);
    } else {
        wp_send_json_success([
            'exists' => false,
            'message' => __('Identifier is not in use.','edusystem'),
        ]);
    } 
}

add_action('wp_ajax_check_mention_identificator_exists', 'check_mention_identificator_exists');
add_action('wp_ajax_nopriv_check_mention_identificator_exists', 'check_mention_identificator_exists');
function check_mention_identificator_exists() {
   
    if ( !isset($_POST['identificator']) || empty($_POST['identificator']) ) {
        wp_send_json_error('Identificador no proporcionado');
    }
    
    $identificator = sanitize_text_field($_POST['identificator']);
    
    global $wpdb;
    $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM $table_mentions_by_career WHERE identificator LIKE %s",
        $identificator
    ));

    if( $exists ){
        wp_send_json_success([
            'exists' => true,
            'message' => __('Identifier in use, please choose another.','edusystem'),
        ]);
    } else {
        wp_send_json_success([
            'exists' => false,
            'message' => __('Identifier is not in use.','edusystem'),
        ]);
    } 
}

add_action('wp_ajax_check_student_program_identificator_exists', 'check_student_program_identificator_exists');
add_action('wp_ajax_nopriv_check_student_program_identificator_exists', 'check_student_program_identificator_exists');
function check_student_program_identificator_exists() {
   
    if ( !isset($_POST['identificator']) || empty($_POST['identificator']) ) {
        wp_send_json_error('Identificador no proporcionado');
    }
    
    $identificator = sanitize_text_field($_POST['identificator']);
    
    global $wpdb;
    $table_student_program = $wpdb->prefix . 'student_program';
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM $table_student_program WHERE identificator LIKE %s",
        $identificator
    ));

    if( $exists ){
        wp_send_json_success([
            'exists' => true,
            'message' => __('Identifier in use, please choose another.','edusystem'),
        ]);
    } else {
        wp_send_json_success([
            'exists' => false,
            'message' => __('Identifier is not in use.','edusystem'),
        ]);
    } 
}




