<?php

function add_admin_partners_content()
{
    global $current_user;
    $roles = $current_user->roles;

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_alliance') {

            global $wpdb;
            $table_alliances = $wpdb->prefix . 'alliances';

            $status_id = $_POST['status_id'];
            $alliance_id = $_POST['status_alliance_id'];

            $wpdb->update($table_alliances, [
                'status' => $status_id,
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'id' => $alliance_id
            ]);

            if ($status_id == 1) {
                $email_approved_alliance = WC()->mailer()->get_emails()['WC_Approved_Partner_Email'];
                $email_approved_alliance->trigger($alliance_id);

                $alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$alliance_id}");
                create_user_alliance($alliance);
            } else if ($status_id == 2) {
                $email_approved_alliance = WC()->mailer()->get_emails()['WC_Rejected_Partner_Email'];
                $email_approved_alliance->trigger($alliance_id);
            }

            wp_redirect(admin_url('admin.php?page=add_admin_partners_content'));
            exit;

        } else if ($_GET['action'] == 'save_setting_alliance') {
            global $wpdb;
            $table_alliances = $wpdb->prefix . 'alliances';
            $table_managers_by_alliance = $wpdb->prefix . 'managers_by_alliances';
            $alliance_id = isset($_POST['alliance_id']) ? intval($_POST['alliance_id']) : 0; // Asegurarse de que sea un entero y manejar si no está seteado

            // Recopilar y sanitizar todos los datos del formulario
            $name = sanitize_text_field($_POST['name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $legal_name = sanitize_text_field($_POST['legal_name']);
            $code = sanitize_text_field($_POST['code']);
            $type = sanitize_text_field($_POST['type']);
            $email = sanitize_email($_POST['email']);
            $phone_hidden = sanitize_text_field($_POST['phone_hidden']);
            $country = sanitize_text_field($_POST['country']);
            $state = sanitize_text_field($_POST['state']);
            $city = sanitize_text_field($_POST['city']);
            $address = sanitize_textarea_field($_POST['address']);
            $description = sanitize_textarea_field($_POST['description']);
            $fee = str_replace('%', '', sanitize_text_field($_POST['fee'])); // Asegúrate de que fee sea sanitizado correctamente si es un float

            // Obtener el manager seleccionado (ahora es un solo valor, no un array)
            if (in_array('owner', $roles) || in_array('administrator', $roles)) {
               $selected_manager_id = isset($_POST['manager_id']) ? intval($_POST['manager_id']) : 0;
            } else {
                $selected_manager_id = $current_user->ID;
            }

            if ($alliance_id > 0) { // Si existe alliance_id, estamos actualizando

                $wpdb->update($table_alliances, [
                    'code' => $code,
                    'type' => $type,
                    'name' => $name,
                    'last_name' => $last_name,
                    'name_legal' => $legal_name,
                    'phone' => $phone_hidden,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'address' => $address,
                    'description' => $description,
                    'fee' => $fee,
                    'updated_at' => current_time('mysql') // Usar current_time('mysql') para consistencia con WP
                ], [
                    'id' => $alliance_id
                ]);

                // --- Gestionar el manager (ahora es un solo manager) ---
                // 1. Eliminar cualquier manager previamente asociado a esta alianza
                $wpdb->delete($table_managers_by_alliance, ['alliance_id' => $alliance_id]);

                // 2. Insertar el nuevo manager seleccionado, si hay uno
                if ($selected_manager_id > 0) {
                    $wpdb->insert(
                        $table_managers_by_alliance,
                        [
                            'alliance_id' => $alliance_id,
                            'user_id' => $selected_manager_id,
                            'created_at' => current_time('mysql')
                        ],
                        ['%d', '%d', '%s'] // Formatos para los valores
                    );
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/'); // Reducí el tiempo del cookie a 10 segundos, 3600 es mucho para un mensaje temporal.
                wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id=' . $alliance_id . '&message=' . urlencode(__('Changes saved successfully', 'edusystem')))); // Usar urlencode para el mensaje en la URL
                exit;

            } else { // Si no existe alliance_id, estamos insertando una nueva alianza

                $user = get_user_by('email', $email);

                if (!$user) { // Si el email no existe, podemos crear la alianza
                    $wpdb->insert($table_alliances, [
                        'code' => $code,
                        'type' => $type,
                        'name' => $name,
                        'last_name' => $last_name,
                        'name_legal' => $legal_name,
                        'phone' => $phone_hidden,
                        'email' => $email,
                        'country' => $country,
                        'state' => $state,
                        'city' => $city,
                        'address' => $address,
                        'description' => $description,
                        'status' => 1,
                        'fee' => floatval($fee),
                        'created_at' => current_time('mysql')
                    ]);

                    $alliance_id = $wpdb->insert_id; // Obtener el ID de la alianza recién insertada

                    // --- Gestionar el manager (ahora es un solo manager) ---
                    // Insertar el manager seleccionado, si hay uno
                    if ($selected_manager_id > 0) {
                        $wpdb->insert(
                            $table_managers_by_alliance,
                            [
                                'alliance_id' => $alliance_id,
                                'user_id' => $selected_manager_id,
                                'created_at' => current_time('mysql')
                            ],
                            ['%d', '%d', '%s']
                        );
                    }

                    $alliance = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_alliances} WHERE id=%d", $alliance_id)); // Usar prepare para seguridad
                    create_user_alliance($alliance); // Asegúrate de que esta función maneje el nuevo esquema si es necesario

                    $email_approved_alliance = WC()->mailer()->get_emails()['WC_Approved_Partner_Email'];
                    $email_approved_alliance->trigger($alliance_id);
                    setcookie('message', sprintf(__('%s %s added successfully.', 'edusystem'), $name, $last_name), time() + 10, '/'); // Mensaje más descriptivo
                    wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'));
                    exit;

                } else { // Si el email ya existe
                    setcookie('message-error', __('Existing email, please enter another email', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=add_alliance')); // Quité el segundo argumento 'edusystem' ya que no es necesario para wp_redirect
                    exit;
                }
            }

            exit;
        } else if ($_GET['action'] == 'delete_alliance') {

            global $wpdb;
            $table_alliances = $wpdb->prefix . 'alliances';
            $delete_alliance = $_POST['delete_alliance_id'];

            $data_alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$delete_alliance}");

            $wpdb->delete($table_alliances, ['id' => $delete_alliance]);
            setcookie('message', $data_alliance->name . ' ' . $data_alliance->last_name, time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'));
            exit;
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {

        if ($_GET['section_tab'] == 'all_alliances') {

            $list_alliances = new TT_alliances_List_Table;
            $list_alliances->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-alliances.php');

        } else if ($_GET['section_tab'] == 'alliance_details') {

            $alliance_id = $_GET['alliance_id'];
            $alliance = get_alliance_detail($alliance_id);

            $countries = get_countries();
            $institutes = get_institutes_from_alliance($alliance_id);
            $managers = get_users_managers();

            $selected_manager_user_ids = [];
            if (isset($alliance_id) && !empty($alliance_id)) {
                global $wpdb;
                $table_managers_by_alliance = $wpdb->prefix . 'managers_by_alliances';

                // Obtener las alianzas ya asociadas a este instituto
                $existing_managers_for_institute = $wpdb->get_results($wpdb->prepare(
                    "SELECT user_id FROM {$table_managers_by_alliance} WHERE alliance_id = %d",
                    $alliance_id
                ));

                // Mapear los resultados a un array simple de IDs para facilitar la verificación en el HTML
                $selected_manager_user_ids = array_map(function ($item) {
                    return (int) $item->user_id;
                }, $existing_managers_for_institute);
            }

            include(plugin_dir_path(__FILE__) . 'templates/alliance-details.php');

        } else if ($_GET['section_tab'] == 'add_alliance') {

            $countries = get_countries();
            $managers = get_users_managers();

            include(plugin_dir_path(__FILE__) . 'templates/alliance-details.php');

        } else if ($_GET['section_tab'] == 'fee_alliance') {

            global $current_user;
            $roles = $current_user->roles;

            $alliance_id = $_GET['alliance_id'];
            $alliance = get_alliance_detail($alliance_id);
            $date = get_dates_search('today', '');
            $start_date = date('m/d/Y', strtotime('today'));
            $orders = get_order_alliance($date[0], $date[1]);
            include(plugin_dir_path(__FILE__) . 'templates/list-payment-alliance.php');

        } else if ($_GET['section_tab'] == 'payment-detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }

    } else {
        $list_alliances = new TT_alliances_review_List_Table;
        $list_alliances->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-alliances.php');
    }
}

function get_alliance_detail($alliance_id)
{

    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';

    $data = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$alliance_id}");
    return $data;
}

function get_alliance_detail_email($email)
{

    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';

    $data = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE email='{$email}'");
    return $data;
}

class TT_alliances_review_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'institute_review',
            'plural' => 'institute_reviews',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'full_name':
                return ucwords($item['name']) . ' ' . ucwords($item['last_name']);
            case 'phone':
            case 'email':
                // case 'state':
                // case 'city':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'created_at':
                $name = $item['created_at'];
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-visibility'></span>" . __('View', 'edusystem') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'full_name' => __('Full name', 'edusystem'),
            'phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            // 'state'         => __('State','edusystem'),
            // 'city'          => __('City','edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_alliances_review()
    {
        global $wpdb, $current_user; 

        // Define los nombres de las tablas de la base de datos.
        $table_alliances = $wpdb->prefix . 'alliances';
        $table_managers_by_alliance = $wpdb->prefix . 'managers_by_alliances'; 

        // Configuración de la paginación.
        $per_page = 20; // Número de elementos a mostrar por página.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Página actual, asegurando que sea un entero absoluto.
        $offset = ( $pagenum - 1 ) * $per_page; // Calcula el desplazamiento (offset) para la consulta SQL.

        // Construcción inicial de la consulta SQL. 
        // Siempre filtra por `status` = 0 (asumiendo que significa un estado específico, como "sin asignar" o "borrador").
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_alliances} WHERE `status` = %d";
        $params = [0]; // El valor del estado 0 se añade como un parámetro.
        $param_types = 'd'; // Indica que el parámetro anterior es un entero ('d' de decimal).

        // --- Lógica de filtrado por roles del usuario ---
        // Si el usuario NO es un 'owner' ni un 'administrator', filtramos las alianzas por su ID.
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtiene un array simple de `alliance_id` asociados al usuario actual.
            // `get_col` es eficiente para obtener una sola columna de resultados.
            $alliance_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT alliance_id FROM {$table_managers_by_alliance} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene alianzas asociadas, no hay resultados que mostrar.
            // Se devuelve un array vacío para evitar consultas innecesarias.
            if (empty($alliance_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Construye una lista de marcadores de posición `%d` para la cláusula `IN`.
            // Esto es necesario para usar `wpdb::prepare` de forma segura con un array de IDs.
            $ids_placeholder = implode(',', array_fill(0, count($alliance_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})"; // Añade la condición `IN` a la consulta.
            
            // Añade cada ID individualmente al array de parámetros y su tipo ('d').
            foreach ($alliance_ids_of_user as $id) {
                $params[] = $id;
                $param_types .= 'd'; 
            }
        }

        // --- Lógica de búsqueda si se proporciona un término ---
        if (isset($_POST['s']) && !empty($_POST['s'])) {
            // Sanitiza y escapa el término de búsqueda para seguridad SQL.
            $search = '%' . $wpdb->esc_like(sanitize_text_field($_POST['s'])) . '%';
            
            $sql .= " AND ("; // Inicia un grupo de condiciones para la búsqueda.
            $searchable_fields = [
                'name', 
                'last_name', 
                'name_legal', 
                'email'
            ];
            
            $conditions = [];
            // Itera sobre los campos y construye las condiciones `LIKE`.
            foreach ($searchable_fields as $field) {
                $conditions[] = "`{$field}` LIKE %s"; // Marcador de posición para string.
                $params[] = $search; // Añade el término de búsqueda a los parámetros.
                $param_types .= 's'; // Indica que el parámetro es un string ('s').
            }
            $sql .= implode(' || ', $conditions); // Une las condiciones con `OR` (`||`).
            $sql .= ")"; // Cierra el grupo de condiciones.
        }

        // --- Añadir LIMIT y OFFSET para la paginación ---
        $sql .= " LIMIT %d OFFSET %d"; // Añade los marcadores de posición para limit y offset.
        $params[] = $per_page; // Añade el número de elementos por página.
        $params[] = $offset; // Añade el valor del offset.
        $param_types .= 'dd'; // Indica que estos dos parámetros son enteros.

        // Prepara la consulta SQL final de forma segura usando `wpdb::prepare()`.
        // Esto previene inyecciones SQL al escapar correctamente todos los parámetros.
        $prepared_sql = $wpdb->prepare($sql, ...$params);

        // Ejecuta la consulta y obtiene los resultados como un array asociativo.
        $alliances_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        // Obtiene el conteo total de filas encontradas por la consulta anterior (antes de LIMIT y OFFSET).
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // Retorna los datos de las alianzas y el conteo total.
        return ['data' => $alliances_data, 'total_count' => $total_count];
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

        $data_alliances = $this->get_list_alliances_review();
        $data = $data_alliances['data'];
        $total_count = (int) $data_alliances['total_count'];

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

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

class TT_alliances_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'institute_review',
            'plural' => 'institute_reviews',
            'ajax' => true
        ));
    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'full_name':
                return ucwords($item['name']) . ' ' . ucwords($item['last_name']);
            case 'phone':
            case 'email':
                return $item[$column_name];
            case 'state':
            case 'city':
                return ucwords($item[$column_name]);
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'view_details':
                return "
                <a href='" . admin_url('/admin.php?page=add_admin_partners_content&section_tab=fee_alliance&alliance_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>" . __('Fees', 'edusystem') . "</a>
                <a href='" . admin_url('/admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id=' . $item['id']) . "' class='button button-primary'></span><span class='dashicons dashicons-edit'></span>" . __('Edit', 'edusystem') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'full_name' => __('Full name', 'edusystem'),
            'phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'state' => __('State', 'edusystem'),
            'city' => __('City', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_alliances()
    {
        global $wpdb, $current_user; 

        // Define los nombres de las tablas de la base de datos.
        $table_alliances = $wpdb->prefix . 'alliances';
        $table_managers_by_alliance = $wpdb->prefix . 'managers_by_alliances'; // Nueva tabla para managers de alianzas.

        // Configuración de la paginación.
        $per_page = 20; // Número de elementos a mostrar por página.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Página actual, asegurando que sea un entero absoluto.
        $offset = ( $pagenum - 1 ) * $per_page; // Calcula el desplazamiento (offset) para la consulta SQL.

        // Construcción inicial de la consulta SQL. 
        // Siempre filtra por `status` = 1 (asumiendo "activas" o "aprobadas").
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_alliances} WHERE `status` = %d";
        $params = [1]; // El valor del estado 1 se añade como un parámetro.
        $param_types = 'd'; // Indica que el parámetro anterior es un entero ('d' de decimal).

        // --- Lógica de filtrado por roles del usuario ---
        // Si el usuario NO es un 'owner' ni un 'administrator', filtramos las alianzas por su ID.
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtiene un array simple de `alliance_id` asociados al usuario actual.
            $alliance_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT alliance_id FROM {$table_managers_by_alliance} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene alianzas asociadas, no hay resultados que mostrar.
            // Se devuelve un array vacío para evitar consultas innecesarias.
            if (empty($alliance_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Construye una lista de marcadores de posición `%d` para la cláusula `IN`.
            $ids_placeholder = implode(',', array_fill(0, count($alliance_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})"; // Añade la condición `IN` a la consulta.
            
            // Añade cada ID individualmente al array de parámetros y su tipo ('d').
            foreach ($alliance_ids_of_user as $id) {
                $params[] = $id;
                $param_types .= 'd'; 
            }
        }

        // --- Lógica de búsqueda si se proporciona un término ---
        if (isset($_POST['s']) && !empty($_POST['s'])) {
            // Sanitiza y escapa el término de búsqueda para seguridad SQL.
            $search = '%' . $wpdb->esc_like(sanitize_text_field($_POST['s'])) . '%';
            
            $sql .= " AND ("; // Inicia un grupo de condiciones para la búsqueda.
            $searchable_fields = [
                'name', 
                'last_name', // Asumo 'last_name' es un campo de búsqueda válido aquí.
                'name_legal', 
                'email'
            ];
            
            $conditions = [];
            // Itera sobre los campos y construye las condiciones `LIKE`.
            foreach ($searchable_fields as $field) {
                $conditions[] = "`{$field}` LIKE %s"; // Marcador de posición para string.
                $params[] = $search; // Añade el término de búsqueda a los parámetros.
                $param_types .= 's'; // Indica que el parámetro es un string ('s').
            }
            $sql .= implode(' || ', $conditions); // Une las condiciones con `OR` (`||`).
            $sql .= ")"; // Cierra el grupo de condiciones.
        }

        // --- Añadir LIMIT y OFFSET para la paginación ---
        $sql .= " LIMIT %d OFFSET %d"; // Añade los marcadores de posición para limit y offset.
        $params[] = $per_page; // Añade el número de elementos por página.
        $params[] = $offset; // Añade el valor del offset.
        $param_types .= 'dd'; // Indica que estos dos parámetros son enteros.

        // Prepara la consulta SQL final de forma segura usando `wpdb::prepare()`.
        $prepared_sql = $wpdb->prepare($sql, ...$params);

        // Ejecuta la consulta y obtiene los resultados como un array asociativo.
        $alliances_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        // Obtiene el conteo total de filas encontradas por la consulta anterior (antes de LIMIT y OFFSET).
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // Retorna los datos de las alianzas y el conteo total.
        return ['data' => $alliances_data, 'total_count' => $total_count];
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

        $data_alliances = $this->get_list_alliances();

        $data = $data_alliances['data'];
        $total_count = (int) $data_alliances['total_count'];

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

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

function get_institutes_from_alliance($alliance_id)
{

    global $wpdb;
    $table_alliances_by_institute = $wpdb->prefix . 'alliances_by_institutes';
    $table_institutes = $wpdb->prefix . 'institutes';
    $institutes = [];
    $rows = $wpdb->get_results("SELECT * FROM {$table_alliances_by_institute} WHERE alliance_id={$alliance_id}");
    foreach ($rows as $key => $row) {
        $institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$row->institute_id}");
        $institutes[] = $institute;
    }

    return $institutes;
}

function get_name_status_alliance($status_id)
{
    $status = match ($status_id) {
        '0' => __('Pending', 'edusystem'),
        '1' => __('Approved', 'edusystem'),
        '2' => __('Declined', 'edusystem'),
        default => '',
    };

    return $status;
}

function get_name_type($type_id)
{

    $type = match ($type_id) {
        '1' => __('Junior', 'edusystem'),
        '2' => __('Senior', 'edusystem'),
        default => '',
    };

    return $type;
}

function create_user_alliance($alliance)
{

    $user = get_user_by('email', $alliance->email);

    if (!$user) {

        $password = generate_password_user();

        $userdata = [
            'user_login' => $alliance->email,
            'user_pass' => $password,
            'user_email' => $alliance->email,
            'first_name' => $alliance->name,
        ];

        $user_id = wp_insert_user($userdata);
        $user = new WP_User($user_id);
        $user->remove_role('subscriber');
        $user->add_role('alliance');

        update_user_meta($user_id, 'alliance_id', $alliance->id);

        wp_new_user_notification($user_id, null, 'both');
    } else {
        update_user_meta($user->id, 'alliance_id', $alliance->id);
    }
}