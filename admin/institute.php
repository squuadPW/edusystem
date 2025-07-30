<?php

function add_admin_institutes_content()
{
    global $current_user;
    $roles = $current_user->roles;

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_institute') {

            global $wpdb;
            $table_institutes = $wpdb->prefix . 'institutes';

            $institute_id = $_POST['change_status_institute_id'];
            $status_id = $_POST['change_status_id'];

            $wpdb->update($table_institutes, [
                'status' => $status_id,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $institute_id]);

            if ($status_id == 1) {
                $email_approved_institute = WC()->mailer()->get_emails()['WC_Approved_Institution_Email'];
                $email_approved_institute->trigger($institute_id);

                $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
                create_user_institute($data_institute);
            } else if ($status_id == 2) {
                $email_rejected_institute = WC()->mailer()->get_emails()['WC_Rejected_Institution_Email'];
                $email_rejected_institute->trigger($institute_id);
            }

            wp_redirect(admin_url('admin.php?page=add_admin_institutes_content'));
            exit;
        }

        if ($_GET['action'] == 'save_institute_details') {
            global $wpdb;
            $table_institutes = $wpdb->prefix . 'institutes';
            $table_alliances_by_institute = $wpdb->prefix . 'alliances_by_institutes';
            $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

            $institute_id = isset($_POST['institute_id']) ? intval($_POST['institute_id']) : 0;

            // Recopilar y sanitizar datos del formulario principal del instituto
            $name = sanitize_text_field($_POST['name']);
            $phone = sanitize_text_field($_POST['phone_hidden']);
            $email = sanitize_email($_POST['email']);
            $country = sanitize_text_field($_POST['country']);
            $state = sanitize_text_field($_POST['state']);
            $city = sanitize_text_field($_POST['city']);
            $level = intval($_POST['level']);
            $fee = str_replace('%', '', sanitize_text_field($_POST['fee']));
            $rector_name = sanitize_text_field($_POST['rector_name']);
            $rector_last_name = sanitize_text_field($_POST['rector_last_name']);
            $rector_phone = sanitize_text_field($_POST['rector_phone_hidden']);
            $contact_name = sanitize_text_field($_POST['contact_name']);
            $contact_last_name = sanitize_text_field($_POST['contact_last_name']);
            $contact_phone = sanitize_text_field($_POST['contact_phone_hidden']);
            $address = sanitize_textarea_field($_POST['address']);
            $description = sanitize_textarea_field($_POST['description']);
            $reference = sanitize_text_field($_POST['reference']);
            $business_name = sanitize_text_field($_POST['business_name']);
            $type_calendar = sanitize_text_field($_POST['type_calendar']);
            $lower_text = sanitize_textarea_field($_POST['lower_text']);
            $middle_text = sanitize_textarea_field($_POST['middle_text']);
            $upper_text = sanitize_textarea_field($_POST['upper_text']);
            $graduated_text = sanitize_textarea_field($_POST['graduated_text']);

            // Obtener las alianzas seleccionadas (IDs del select múltiple)
            $selected_alliances = isset($_POST['alliances']) ? array_map('intval', (array) $_POST['alliances']) : [];
            // Obtener los datos de montos de las alianzas (array asociativo de arrays)
            $alliances_fees_data = isset($_POST['alliances_fees']) ? (array) $_POST['alliances_fees'] : [];

            // Obtener el manager seleccionado (ahora es un solo valor, no un array)
            if (in_array('owner', $roles) || in_array('administrator', $roles)) {
                $selected_manager = isset($_POST['manager']) ? intval($_POST['manager']) : 0;
            } else {
                $selected_manager = $current_user->ID;
            }

            // --- Actualizar Instituto ---
            if ($institute_id > 0) {
                $wpdb->update(
                    $table_institutes,
                    [
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'country' => $country,
                        'state' => $state,
                        'city' => $city,
                        'level_id' => $level,
                        'type_calendar' => $type_calendar,
                        'fee' => $fee,
                        'name_rector' => $rector_name,
                        'lastname_rector' => $rector_last_name,
                        'phone_rector' => $rector_phone,
                        'name_contact' => $contact_name,
                        'lastname_contact' => $contact_last_name,
                        'phone_contact' => $contact_phone,
                        'address' => $address,
                        'description' => $description,
                        'business_name' => $business_name,
                        'lower_text' => $lower_text,
                        'middle_text' => $middle_text,
                        'upper_text' => $upper_text,
                        'graduated_text' => $graduated_text,
                        'updated_at' => current_time('mysql')
                    ],
                    ['id' => $institute_id]
                );

                // --- Gestionar alianzas en la tabla independiente (alliances_by_institutes) ---
                $wpdb->delete($table_alliances_by_institute, ['institute_id' => $institute_id]);
                foreach ($selected_alliances as $alliance_id) {
                    $alliance_fee = 0.0;
                    if (isset($alliances_fees_data[$alliance_id])) {
                        $alliance_fee = floatval(sanitize_text_field($alliances_fees_data[$alliance_id]['alliance_fee']));
                    }
                    $wpdb->insert(
                        $table_alliances_by_institute,
                        [
                            'institute_id' => $institute_id,
                            'alliance_id' => $alliance_id,
                            'alliance_fee' => $alliance_fee,
                            'institute_fee' => $fee,
                            'created_at' => current_time('mysql')
                        ],
                        ['%d', '%d', '%f', '%f', '%s']
                    );
                }

                // --- Gestionar manager (ahora es un solo manager) ---
                // Primero, eliminamos cualquier manager asociado previamente para este instituto
                $wpdb->delete($table_managers_by_institute, ['institute_id' => $institute_id]);
                // Luego, insertamos el nuevo manager seleccionado, si hay uno
                if ($selected_manager > 0) {
                    $wpdb->insert(
                        $table_managers_by_institute,
                        [
                            'institute_id' => $institute_id,
                            'user_id' => $selected_manager,
                            'created_at' => current_time('mysql')
                        ],
                        ['%d', '%d', '%s']
                    );
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $institute_id));
                exit;

            } else { // --- Insertar Nuevo Instituto ---
                $user = get_user_by('email', $email);

                if (!$user) {
                    $wpdb->insert(
                        $table_institutes,
                        [
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $email,
                            'country' => $country,
                            'state' => $state,
                            'city' => $city,
                            'level_id' => $level,
                            'type_calendar' => $type_calendar,
                            'fee' => $fee,
                            'name_rector' => $rector_name,
                            'lastname_rector' => $rector_last_name,
                            'phone_rector' => $rector_phone,
                            'name_contact' => $contact_name,
                            'lastname_contact' => $contact_last_name,
                            'phone_contact' => $contact_phone,
                            'reference' => $reference,
                            'address' => $address,
                            'description' => $description,
                            'business_name' => $business_name,
                            'lower_text' => $lower_text,
                            'middle_text' => $middle_text,
                            'upper_text' => $upper_text,
                            'graduated_text' => $graduated_text,
                            'status' => 1,
                            'created_at' => current_time('mysql')
                        ]
                    );

                    $institute_id = $wpdb->insert_id;

                    // --- Gestionar manager (ahora es un solo manager) ---
                    if ($selected_manager > 0) {
                        $wpdb->insert(
                            $table_managers_by_institute,
                            [
                                'institute_id' => $institute_id,
                                'user_id' => $selected_manager,
                                'created_at' => current_time('mysql')
                            ],
                            ['%d', '%d', '%s']
                        );
                    }

                    // --- Gestionar alianzas en la tabla independiente (alliances_by_institutes) ---
                    foreach ($selected_alliances as $alliance_id) {
                        $alliance_fee = 0.0;
                        if (isset($alliances_fees_data[$alliance_id])) {
                            $alliance_fee = floatval(sanitize_text_field($alliances_fees_data[$alliance_id]['alliance_fee']));
                        }
                        $wpdb->insert(
                            $table_alliances_by_institute,
                            [
                                'institute_id' => $institute_id,
                                'alliance_id' => $alliance_id,
                                'alliance_fee' => $alliance_fee,
                                'institute_fee' => $fee,
                                'created_at' => current_time('mysql')
                            ],
                            ['%d', '%d', '%f', '%f', '%s']
                        );
                    }

                    $data_institute = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_institutes} WHERE id=%d", $institute_id));
                    create_user_institute($data_institute);

                    $email_approved_institute = WC()->mailer()->get_emails()['WC_Approved_Institution_Email'];
                    $email_approved_institute->trigger($institute_id);
                    setcookie('message', __('Institute added successfully.', 'edusystem'), time() + 3600, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'));
                    exit;

                } else {
                    setcookie('message-error', __('Existing email, please enter another email', 'edusystem'), time() + 3600, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=add_institute'));
                    exit;
                }
            }
        }

        if ($_GET['action'] == 'delete_institute') {

            global $wpdb;
            $table_institutes = $wpdb->prefix . 'institutes';

            $institute_id = $_POST['delete_institute_id'];

            $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");

            $user = get_user_by('email', $data_institute->email);

            if ($user) {
                wp_delete_user($user->ID);
            }

            $wpdb->delete($table_institutes, ['id' => $institute_id]);
            setcookie('message', $data_institute->name, time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'));
            exit;
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {

        if ($_GET['section_tab'] == 'all_institutes') {
            $list_institutes = new TT_institutes_List_Table;
            $list_institutes->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-institutes.php');
        }

        if ($_GET['section_tab'] == 'all_declined_institutes') {
            $list_institutes = new TT_institutes_declined_List_Table;
            $list_institutes->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-institutes.php');
        }

        if ($_GET['section_tab'] == 'all_suspended_institutes') {
            $list_institutes = new TT_institutes_suspended_List_Table;
            $list_institutes->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-institutes.php');
        }

        if ($_GET['section_tab'] == 'institute_details') {
            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $countries = get_countries();
            $states = get_states_by_country_code($institute->country);
            $alliances = get_alliances();
            $managers = get_users_managers();

            // $selected_manager_user_ids = [];
            // if (isset($institute_id) && !empty($institute_id)) {
            //     global $wpdb;
            //     $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

            //     // Obtener las alianzas ya asociadas a este instituto
            //     $existing_managers_for_institute = $wpdb->get_results($wpdb->prepare(
            //         "SELECT user_id FROM {$table_managers_by_institute} WHERE institute_id = %d",
            //         $institute_id
            //     ));

            //     // Mapear los resultados a un array simple de IDs para facilitar la verificación en el HTML
            //     $selected_manager_user_ids = array_map(function ($item) {
            //         return (int) $item->user_id;
            //     }, $existing_managers_for_institute);
            // }

            $selected_manager_user_ids = get_managers_institute($institute_id);

            $selected_alliance_fees_data = [];
            if (isset($institute_id) && !empty($institute_id)) {
                global $wpdb;
                $table_alliances_by_institute = $wpdb->prefix . 'alliances_by_institutes';

                // Obtener las alianzas ya asociadas a este instituto, incluyendo los campos de monto
                $existing_alliances_for_institute = $wpdb->get_results($wpdb->prepare(
                    "SELECT alliance_id, alliance_fee, institute_fee FROM {$table_alliances_by_institute} WHERE institute_id = %d",
                    $institute_id
                ));

                // Mapear los resultados a un array asociativo para facilitar la verificación y el acceso en JavaScript
                foreach ($existing_alliances_for_institute as $item) {
                    $selected_alliance_fees_data[(int) $item->alliance_id] = [
                        'alliance_fee' => (double) $item->alliance_fee,
                        'institute_fee' => (double) $item->institute_fee,
                    ];
                }
            }

            include(plugin_dir_path(__FILE__) . 'templates/institute-details.php');
        }

        if ($_GET['section_tab'] == 'fee_institute') {

            global $current_user;
            $roles = $current_user->roles;
            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $date = get_dates_search('this-month', '');
            $start_date = date('01/m/Y', strtotime('first day of this month'));
            $orders = get_order_institutes($date[0], $date[1]);
            include(plugin_dir_path(__FILE__) . 'templates/list-payments-institutes.php');
        }

        if ($_GET['section_tab'] == 'payment-detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }

        if ($_GET['section_tab'] == 'add_institute') {
            $countries = get_countries();
            $alliances = get_alliances();
            $managers = get_users_managers();
            include(plugin_dir_path(__FILE__) . 'templates/institute-details.php');
        }

    } else {
        $list_institutes = new TT_institutes_review_List_Table;
        $list_institutes->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-institutes.php');
    }
}

function get_name_status_institute($status_id)
{
    $status = match ($status_id) {
        '0' => __('Pending', 'edusystem'),
        '1' => __('Approved', 'edusystem'),
        '2' => __('Declined', 'edusystem'),
        '3' => __('Suspended', 'edusystem'),
        default => '',
    };

    return $status;
}

function get_name_country($country_id)
{

    $countries = get_countries();
    $name = "";

    foreach ($countries as $key => $country) {
        if ($key == $country_id) {
            $name = $country;
        }
    }

    return $name;
}

function get_name_state($country_id, $state_id)
{

    $states = get_states_by_country_code($country_id);
    $name = "";

    foreach ($states as $key => $state) {
        if ($key == $state_id) {
            $name = $state;
        }
    }

    return $name;
}

function get_name_reference($reference_id)
{

    $reference = match ($reference_id) {
        '3' => __('Email', 'edusystem'),
        '4' => __('Internet search', 'edusystem'),
        '5' => __('On-site Event', 'edusystem'),
        default => '',
    };

    return $reference;
}

function get_institute_details($institute_id)
{

    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';

    $institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
    return $institute;
}

function get_alliances()
{

    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';

    $alliances = $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 1");
    return $alliances;
}

function get_users_managers()
{
    $args = array(
        'role' => 'manager', // Especifica el rol 'manager'
        'orderby' => 'display_name', // Opcional: ordena por nombre de visualización
        'order' => 'ASC' // Opcional: orden ascendente
    );

    $managers = get_users($args);

    return $managers;
}

function get_name_level($level_id)
{

    $level = match ($level_id) {
        '1' => __('Primary', 'edusystem'),
        '2' => __('High School', 'edusystem'),
        default => "",
    };

    return $level;
}

function get_type_calendar($type_calendar)
{

    $level = match ($type_calendar) {
        1 => __('Calendar A (Jan - Dec)', 'edusystem'),
        2 => __('Calendar B (Sep - Aug)', 'edusystem'),
        default => "N/A",
    };

    return $level;
}

class TT_institutes_review_List_Table extends WP_List_Table
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
            case 'name':
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'created_at':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'name_contact':
                return ucwords($item['name_contact']) . ' ' . ucwords($item['lastname_contact']);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-visibility'></span>" . __('View', 'edusystem') . "</a>";
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
            'name' => __('Name', 'edusystem'),
            'number_phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'name_rector' => __('Name Rector', 'edusystem'),
            'name_contact' => __('Name Contact', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_institutes_review()
    {
        global $wpdb, $current_user;

        // Define los nombres de las tablas de la base de datos.
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

        // Configuración de la paginación.
        $per_page = 20; // Número de elementos a mostrar por página.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Página actual, asegurando que sea un entero absoluto.
        $offset = ($pagenum - 1) * $per_page; // Calcula el desplazamiento (offset) para la consulta SQL.

        // Construcción inicial de la consulta SQL. 
        // Siempre filtra por `status` = 0 (asumiendo que significa "no asignado" o "sin estado definido").
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status` = %d";
        $params = [0]; // El valor del estado 0 se añade como un parámetro.
        $param_types = 'd'; // Indica que el parámetro anterior es un entero ('d' de decimal).

        // --- Lógica de filtrado por roles del usuario ---
        // Si el usuario NO es un 'owner' ni un 'administrator', filtramos los institutos por su ID.
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtiene un array simple de `institute_id` asociados al usuario actual.
            $institute_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT institute_id FROM {$table_managers_by_institute} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene institutos asociados, no hay resultados que mostrar.
            // Se devuelve un array vacío para evitar consultas innecesarias.
            if (empty($institute_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Construye una lista de marcadores de posición `%d` para la cláusula `IN`.
            $ids_placeholder = implode(',', array_fill(0, count($institute_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})"; // Añade la condición `IN` a la consulta.

            // Añade cada ID individualmente al array de parámetros y su tipo ('d').
            foreach ($institute_ids_of_user as $id) {
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
                'email',
                'name_rector',
                'lastname_rector',
                'name_contact',
                'lastname_contact'
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
        $institutes_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        // Obtiene el conteo total de filas encontradas por la consulta anterior (antes de LIMIT y OFFSET).
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // Retorna los datos de los institutos y el conteo total.
        return ['data' => $institutes_data, 'total_count' => $total_count];
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

        $pending_institutes = $this->get_list_institutes_review();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data_institutes = $pending_institutes['data'];
        $data = $pending_institutes['data'];
        $total_count = (int) $pending_institutes['total_count'];

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

class TT_institutes_List_Table extends WP_List_Table
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
            case 'name':
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'created_at':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'name_contact':
                return ucwords($item['name_contact']) . ' ' . ucwords($item['lastname_contact']);
            case 'view_details':
                return "
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=fee_institute&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>" . __('Fees', 'edusystem') . "</a>
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-edit'></span>" . __('Edit', 'edusystem') . "</a>
                ";
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
            'name' => __('Name', 'edusystem'),
            'number_phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'name_rector' => __('Name Rector', 'edusystem'),
            'name_contact' => __('Name Contact', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_institutes()
    {
        global $wpdb, $current_user;
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

        $per_page = 20; // Número de elementos por página
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = ($pagenum - 1) * $per_page;

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status` = 1";
        $params = [];
        $param_types = '';

        // Lógica para filtrar por institutes_id si el usuario no es 'owner' o 'administrator'
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtener los institutes_id asociados a este usuario
            $institute_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT institute_id FROM {$table_managers_by_institute} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene institutos asignados, no hay resultados que mostrar
            if (empty($institute_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Convertir el array de IDs a una lista separada por comas para la cláusula IN
            $ids_placeholder = implode(',', array_fill(0, count($institute_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})";

            // Añadir los IDs a los parámetros. Usamos array_merge para combinarlos correctamente.
            // Aquí no se añade a $param_types directamente porque implode ya maneja el %d.
            // La ventaja de get_col es que ya nos da un array de strings/int, y wpdb::prepare
            // se encarga de tratarlos como %d si se usan con implode.
            foreach ($institute_ids_of_user as $id) {
                $params[] = $id;
                $param_types .= 'd'; // Aseguramos que se traten como enteros
            }
        }

        // Lógica de búsqueda (igual que antes)
        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $search = '%' . $wpdb->esc_like(sanitize_text_field($_POST['s'])) . '%';
            $sql .= " AND (";
            $searchable_fields = ['name', 'email', 'name_rector', 'lastname_rector', 'name_contact', 'lastname_contact'];

            $conditions = [];
            foreach ($searchable_fields as $field) {
                $conditions[] = "`{$field}` LIKE %s";
                $params[] = $search;
                $param_types .= 's';
            }
            $sql .= implode(' || ', $conditions);
            $sql .= ")";
        }

        // Añadir LIMIT y OFFSET al final
        $sql .= " LIMIT %d OFFSET %d";
        $params[] = $per_page;
        $params[] = $offset;
        $param_types .= 'dd'; // 'd' para entero (per_page, offset)

        // Prepara la consulta usando wpdb::prepare para seguridad
        // Asegúrate de que el número de marcadores de posición en $sql coincida con el número de elementos en $params
        $prepared_sql = $wpdb->prepare($sql, ...$params);

        $institutes_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        return ['data' => $institutes_data, 'total_count' => $total_count];
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
        $all_institutes = $this->get_list_institutes();

        $data = $all_institutes['data'];
        $total_count = (int) $all_institutes['total_count'];
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

class TT_institutes_declined_List_Table extends WP_List_Table
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
            case 'name':
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'created_at':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'name_contact':
                return ucwords($item['name_contact']) . ' ' . ucwords($item['lastname_contact']);
            case 'view_details':
                return "
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=fee_institute&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>" . __('Fees', 'edusystem') . "</a>
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-edit'></span>" . __('Edit', 'edusystem') . "</a>
                ";
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
            'name' => __('Name', 'edusystem'),
            'number_phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'name_rector' => __('Name Rector', 'edusystem'),
            'name_contact' => __('Name Contact', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_institutes()
    {
        global $wpdb, $current_user;

        // Define los nombres de las tablas de la base de datos.
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

        // Configuración de la paginación.
        $per_page = 20; // Número de elementos a mostrar por página.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Página actual, asegurando que sea un entero absoluto.
        $offset = ($pagenum - 1) * $per_page; // Calcula el desplazamiento (offset) para la consulta SQL.

        // Construcción inicial de la consulta SQL. 
        // Siempre filtra por `status` = 2 (asumiendo que significa "pendiente" o "inactivo").
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status` = %d";
        $params = [2]; // El valor del estado 2 se añade como un parámetro.
        $param_types = 'd'; // Indica que el parámetro anterior es un entero ('d' de decimal).

        // --- Lógica de filtrado por roles del usuario ---
        // Si el usuario NO es un 'owner' ni un 'administrator', filtramos los institutos por su ID.
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtiene un array simple de `institute_id` asociados al usuario actual.
            // `get_col` es eficiente para obtener una sola columna de resultados.
            $institute_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT institute_id FROM {$table_managers_by_institute} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene institutos asociados, no hay resultados que mostrar.
            // Se devuelve un array vacío para evitar consultas innecesarias.
            if (empty($institute_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Construye una lista de marcadores de posición `%d` para la cláusula `IN`.
            // Esto es necesario para usar `wpdb::prepare` de forma segura con un array de IDs.
            $ids_placeholder = implode(',', array_fill(0, count($institute_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})"; // Añade la condición `IN` a la consulta.

            // Añade cada ID individualmente al array de parámetros y su tipo ('d').
            foreach ($institute_ids_of_user as $id) {
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
                'email',
                'name_rector',
                'lastname_rector',
                'name_contact',
                'lastname_contact'
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
        $institutes_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        // Obtiene el conteo total de filas encontradas por la consulta anterior (antes de LIMIT y OFFSET).
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // Retorna los datos de los institutos y el conteo total.
        return ['data' => $institutes_data, 'total_count' => $total_count];
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
        $all_institutes = $this->get_list_institutes();
        $data_institutes = $all_institutes['data'];
        $data = $all_institutes['data'];
        $total_count = (int) $all_institutes['total_count'];
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

class TT_institutes_suspended_List_Table extends WP_List_Table
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
            case 'name':
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'created_at':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']) . ' ' . ucwords($item['lastname_rector']);
            case 'name_contact':
                return ucwords($item['name_contact']) . ' ' . ucwords($item['lastname_contact']);
            case 'view_details':
                return "
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=fee_institute&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>" . __('Fees', 'edusystem') . "</a>
                    <a style='margin:3px;' href='" . admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $item['id']) . "' class='button button-primary'><span class='dashicons dashicons-edit'></span>" . __('Edit', 'edusystem') . "</a>
                ";
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
            'name' => __('Name', 'edusystem'),
            'number_phone' => __('Phone', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'name_rector' => __('Name Rector', 'edusystem'),
            'name_contact' => __('Name Contact', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_list_institutes()
    {

        global $wpdb, $current_user;

        // Define los nombres de las tablas de la base de datos.
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

        // Configuración de la paginación.
        $per_page = 20; // Número de elementos a mostrar por página.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Página actual, asegurando que sea un entero absoluto.
        $offset = ($pagenum - 1) * $per_page; // Calcula el desplazamiento (offset) para la consulta SQL.

        // Construcción inicial de la consulta SQL. 
        // Siempre filtra por `status` = 3 (asumiendo que significa "inactivo" o "desactivado").
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status` = %d";
        $params = [3]; // El valor del estado 3 se añade como un parámetro.
        $param_types = 'd'; // Indica que el parámetro anterior es un entero ('d' de decimal).

        // --- Lógica de filtrado por roles del usuario ---
        // Si el usuario NO es un 'owner' ni un 'administrator', filtramos los institutos por su ID.
        if (!in_array('owner', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            // Obtiene un array simple de `institute_id` asociados al usuario actual.
            // `get_col` es eficiente para obtener una sola columna de resultados.
            $institute_ids_of_user = $wpdb->get_col($wpdb->prepare(
                "SELECT institute_id FROM {$table_managers_by_institute} WHERE user_id = %d",
                $current_user->ID
            ));

            // Si el usuario no tiene institutos asociados, no hay resultados que mostrar.
            // Se devuelve un array vacío para evitar consultas innecesarias.
            if (empty($institute_ids_of_user)) {
                return ['data' => [], 'total_count' => 0];
            }

            // Construye una lista de marcadores de posición `%d` para la cláusula `IN`.
            // Esto es necesario para usar `wpdb::prepare` de forma segura con un array de IDs.
            $ids_placeholder = implode(',', array_fill(0, count($institute_ids_of_user), '%d'));
            $sql .= " AND `id` IN ({$ids_placeholder})"; // Añade la condición `IN` a la consulta.

            // Añade cada ID individualmente al array de parámetros y su tipo ('d').
            foreach ($institute_ids_of_user as $id) {
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
                'email',
                'name_rector',
                'lastname_rector',
                'name_contact',
                'lastname_contact'
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
        $institutes_data = $wpdb->get_results($prepared_sql, 'ARRAY_A');

        // Obtiene el conteo total de filas encontradas por la consulta anterior (antes de LIMIT y OFFSET).
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // Retorna los datos de los institutos y el conteo total.
        return ['data' => $institutes_data, 'total_count' => $total_count];
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
        $all_institutes = $this->get_list_institutes();
        $data_institutes = $all_institutes['data'];
        $data = $all_institutes['data'];
        $total_count = (int) $all_institutes['total_count'];
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

function create_user_institute($institute)
{
    $user = get_user_by('email', $institute->email);

    if (!$user) {
        $password = wp_generate_password(12, false); // Generate a strong password, not necessarily unique
        // You had 'generate_password_user()'. Ensure this function returns a plaintext password.
        // If it's your custom function, it might be the culprit.
        // Using wp_generate_password is safer and recommended.

        $userdata = [
            'user_login'    => $institute->email,
            'user_pass'     => $password, // Pass the plaintext password here
            'user_email'    => $institute->email,
            'first_name'    => $institute->name,
            'role'          => 'institutes', // Assign the role directly during user creation
        ];

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            // Handle error during user creation (e.g., log it or display a message)
            error_log('Error creating institute user: ' . $user_id->get_error_message());
            return; // Exit if user creation failed
        }

        // No need to remove and add role if set directly above
        // $user = new WP_User($user_id);
        // $user->remove_role('subscriber');
        // $user->add_role('institutes');

        update_user_meta($user_id, 'institute_id', $institute->id);

        // Pass the plaintext password as the second argument
        wp_new_user_notification($user_id, $password, 'both');

    } else {
        // If user already exists, just update their institute_id and ensure their role.
        // You might want to add logic here to ensure the existing user has the 'institutes' role
        // if they don't already, or handle cases where the email already belongs to a
        // non-institute user.
        update_user_meta($user->ID, 'institute_id', $institute->id);

        // Optionally, ensure the role is correct for an existing user
        $existing_user_obj = new WP_User($user->ID);
        if (!in_array('institutes', (array) $existing_user_obj->roles)) {
            $existing_user_obj->add_role('institutes');
            // Consider if you need to remove other roles here, e.g., 'subscriber'
            // $existing_user_obj->remove_role('subscriber');
        }
    }
}

function get_students_institute($institute_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $query = $wpdb->prepare(
        "SELECT * FROM {$table_students} WHERE institute_id = %d ORDER BY id DESC",
        $institute_id
    );
    $students = $wpdb->get_results($query);
    return $students;
}

function get_all_students_institute($institute_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $query = $wpdb->prepare(
        "SELECT * FROM {$table_students} WHERE institute_id = %d ORDER BY id DESC",
        $institute_id
    );
    $students = $wpdb->get_results($query);
    return $students;
}

function get_name_institute($institute_id)
{
    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';
    $institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id = " . $institute_id);
    return $institute->name;
}

function get_managers_institute($institute_id)
{
    $selected_manager_user_ids = [];
    if (isset($institute_id) && !empty($institute_id)) {
        global $wpdb;
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';

        // Obtener las alianzas ya asociadas a este instituto
        $existing_managers_for_institute = $wpdb->get_results($wpdb->prepare(
            "SELECT user_id FROM {$table_managers_by_institute} WHERE institute_id = %d",
            $institute_id
        ));

        // Mapear los resultados a un array simple de IDs para facilitar la verificación en el HTML
        $selected_manager_user_ids = array_map(function ($item) {
            return (int) $item->user_id;
        }, $existing_managers_for_institute);
    }
    return $selected_manager_user_ids;
}