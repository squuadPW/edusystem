<?php

function add_admin_form_dynamic_link_content()
{
    global $wpdb;
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        $payment_plans = [];
        $programs = get_student_program();
        $current_user = wp_get_current_user();
        $roles = (array) $current_user->roles;
        $is_manager = in_array('manager', $roles);
        $is_admin = in_array('administrator', $roles);
        if (!$is_admin && !$is_manager) {
            $manager_user_id = get_user_meta($current_user->ID, 'manager_user_id', true);
            if (!empty($manager_user_id)) {
                $all_managers = get_users_managers();
                $managers = array_filter($all_managers, function($m) use ($manager_user_id) {
                    return $m->ID == $manager_user_id;
                });
            } else {
                $managers = [];
            }
        } else {
            $managers = get_users_managers();
        }
        $dynamic_links_email_log = array();

        if ($_GET['section_tab'] == 'dynamic_link_details') {
            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $dynamic_link = get_dynamic_link_detail($dynamic_link_id);
            if ($dynamic_link) {
                $table_programs = $wpdb->prefix . 'programs';
                $associateds = get_associated_plans_by_program_id($dynamic_link->program_identificator);
                foreach ($associateds as $key => $plan) {
                    $plan = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE identificator='{$plan}'");
                    if ($plan) {
                        $payment_plans[] = $plan;
                    }
                }
            }
            $dynamic_links_email_log = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_dynamic_links_email_log} WHERE dynamic_link_id=%d ORDER BY id DESC", $dynamic_link_id));
            include(plugin_dir_path(__FILE__) . 'templates/dynamic-links-detail.php');
        }

        if ($_GET['section_tab'] == 'add_dynamic_link') {
            include(plugin_dir_path(__FILE__) . 'templates/dynamic-links-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_dynamic_link_details') {
            global $wpdb;

            $table = $wpdb->prefix . 'dynamic_links';
            $dynamic_link_id = $_POST['dynamic_link_id'];
            $type_document = sanitize_text_field($_POST['type_document']);
            $id_document = sanitize_text_field($_POST['id_document']);
            $name = sanitize_text_field($_POST['name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $email = sanitize_text_field($_POST['email']);
            $program_identificator = sanitize_text_field($_POST['program_identificator']);
            $payment_plan_identificator = sanitize_text_field($_POST['payment_plan_identificator']);
            $save_and_send_email = sanitize_text_field($_POST['save_and_send_email']);
            $manager_id = $_POST['manager_id'];
            $current_user = wp_get_current_user();
            $created_by = $current_user->ID;
            $transfer_cr = $_POST['transfer_cr'] ?? 0;
            $fee_payment_completed = $_POST['fee_payment_completed'] ?? 0;

            // Generar un token corto aleatorio para el link
            $link = substr(bin2hex(random_bytes(6)), 0, 10);

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');

            if (isset($dynamic_link_id) && !empty($dynamic_link_id)) {
                $wpdb->update($table, [
                    'link' => $link,
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'name' => $name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'program_identificator' => $program_identificator,
                    'payment_plan_identificator' => $payment_plan_identificator,
                    'transfer_cr' => $transfer_cr,
                    'fee_payment_completed' => $fee_payment_completed,
                    'manager_id' => $manager_id,
                    'created_by' => $created_by,
                ], ['id' => $dynamic_link_id]);
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=') . $dynamic_link_id);
            } else {
                $wpdb->insert($table, [
                    'link' => $link,
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'name' => $name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'program_identificator' => $program_identificator,
                    'payment_plan_identificator' => $payment_plan_identificator,
                    'transfer_cr' => $transfer_cr,
                    'fee_payment_completed' => $fee_payment_completed,
                    'manager_id' => $manager_id,
                    'created_by' => $created_by,
                ]);
                $dynamic_link_id = $wpdb->insert_id;
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=') . $dynamic_link_id);
            }

            if ($save_and_send_email == '1') {
                $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
                $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
                $html .= '<p>' . __('We are pleased to inform you that a dynamic link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
                $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
                $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
                $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
                $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
                $sender_email->trigger($email, 'Link', $html);

                $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
                $wpdb->insert($table_dynamic_links_email_log, [
                    'dynamic_link_id' => $dynamic_link_id,
                    'email' => $email,
                    'created_by' => $created_by,
                ]);
            }

            exit;
        } else if ($_GET['action'] == 'upload_document') {
            global $wpdb;

            if (isset($_FILES['document_upload_file']) && !empty($_FILES['document_upload_file'])) {
                $file_temp = $_FILES['document_upload_file'];
                if (($handle = fopen($file_temp['tmp_name'], 'r')) !== false) {
                    // Detectar delimitador automáticamente (coma o punto y coma)
                    $first_line = fgets($handle);
                    rewind($handle);
                    $delimiter = ',';
                    if (substr_count($first_line, ';') > substr_count($first_line, ',')) {
                        $delimiter = ';';
                    }
                    $header = fgetcsv($handle, 0, $delimiter);
                    // Esperados: type_document, id_document, name, last_name, email, program_identificator, payment_plan_identificator, transfer_cr, send_email
                    $expected = ['type_document', 'id_document', 'name', 'last_name', 'email', 'program_identificator', 'payment_plan_identificator', 'transfer_cr', 'send_email'];
                    $header_map = array_flip($header);

                    $missing = array_diff($expected, $header);
                    if (!empty($missing)) {
                        setcookie('message', __('CSV missing columns: ', 'edusystem') . implode(', ', $missing), time() + 10, '/');
                        wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                        exit;
                    }
                    $current_user = wp_get_current_user();
                    $created_by = $current_user->ID;
                    $manager_user_id = get_user_meta($created_by, 'manager_user_id', true);
                    $manager_id = !empty($manager_user_id) ? $manager_user_id : 0;
                    $table = $wpdb->prefix . 'dynamic_links';
                    $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
                    $count_inserted = 0;
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        // Limpiar y mapear valores
                        foreach ($header_map as $col => $idx) {
                            $row[$idx] = isset($row[$idx]) ? trim($row[$idx]) : '';
                        }
                        $type_document = sanitize_text_field($row[$header_map['type_document']]);
                        $id_document = sanitize_text_field($row[$header_map['id_document']]);
                        $name = sanitize_text_field($row[$header_map['name']]);
                        $last_name = sanitize_text_field($row[$header_map['last_name']]);
                        $email = sanitize_text_field($row[$header_map['email']]);
                        $program_identificator = sanitize_text_field($row[$header_map['program_identificator']]);
                        $payment_plan_identificator = sanitize_text_field($row[$header_map['payment_plan_identificator']]);
                        $transfer_cr = isset($row[$header_map['transfer_cr']]) ? intval($row[$header_map['transfer_cr']]) : 0;
                        $send_email = isset($row[$header_map['send_email']]) ? $row[$header_map['send_email']] : '0';
                        $link = substr(bin2hex(random_bytes(6)), 0, 10);
                        $wpdb->insert($table, [
                            'link' => $link,
                            'type_document' => $type_document,
                            'id_document' => $id_document,
                            'name' => $name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'program_identificator' => $program_identificator,
                            'payment_plan_identificator' => $payment_plan_identificator,
                            'transfer_cr' => $transfer_cr,
                            'manager_id' => $manager_id,
                            'created_by' => $created_by,
                        ]);
                        $dynamic_link_id = $wpdb->insert_id;
                        if ($send_email == '1') {
                            $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
                            $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
                            $html .= '<p>' . __('We are pleased to inform you that a dynamic link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
                            $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
                            $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
                            $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
                            $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
                            $sender_email->trigger($email, 'Link', $html);
                            $wpdb->insert($table_dynamic_links_email_log, [
                                'dynamic_link_id' => $dynamic_link_id,
                                'email' => $email,
                                'created_by' => $created_by,
                            ]);
                        }
                        $count_inserted++;
                    }
                    fclose($handle);
                    setcookie('message', sprintf(__('CSV processed. %d records inserted.', 'edusystem'), $count_inserted), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                    exit;
                } else {
                    setcookie('message', __('Could not open CSV file.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                    exit;
                }
            } else {
                setcookie('message', __('No file uploaded.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                exit;
            }
        } else if ($_GET['action'] == 'delete_dynamic_link') {
            global $wpdb;
            $table = $wpdb->prefix . 'dynamic_links';
            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $wpdb->delete($table, ['id' => $dynamic_link_id]);
            $wpdb->delete($table_dynamic_links_email_log, ['dynamic_link_id' => $dynamic_link_id]);

            setcookie('message', __('Dynamic link deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
            exit;
        } else if ($_GET['action'] == 'send_email') {
            global $wpdb;
            $table = $wpdb->prefix . 'dynamic_links';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $dynamic_link_data = get_dynamic_link_detail($dynamic_link_id);
            $link = $dynamic_link_data->link;
            $name = $dynamic_link_data->name;
            $last_name = $dynamic_link_data->last_name;
            $email = $dynamic_link_data->email;
            $current_user = wp_get_current_user();
            $created_by = $current_user->ID;

            $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
            $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
            $html .= '<p>' . __('We are pleased to inform you that a dynamic link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
            $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
            $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
            $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
            $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
            $sender_email->trigger($email, 'Link', $html);

            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $wpdb->insert($table_dynamic_links_email_log, [
                'dynamic_link_id' => $dynamic_link_id,
                'email' => $email,
                'created_by' => $created_by
            ]);

            setcookie('message', __('Dynamic link send to email.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
            exit;
        } else {
            $list_dynamic_links = new TT_Dynamic_all_List_Table;
            $list_dynamic_links->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-dynamic-links.php');
        }
    }
}

class TT_Dynamic_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'dynamic_link',
                'plural' => 'dynamic_links',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {
        global $current_user;
        switch ($column_name) {
            case 'img_desktop':
                return $item['img_desktop'] ? '<img src="' . $item['img_desktop'] . '" width="100px" height="50px" />' : 'N/A';
            case 'img_mobile':
                return $item['img_mobile'] ? '<img src="' . $item['img_mobile'] . '" width="100px" height="50px" />' : 'N/A';
            case 'view_details':
                $buttons = "<a href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&action=delete_dynamic_link&dynamic_link_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&action=send_email&dynamic_link_id=' . $item['id']) . "' class='button button-success'>" . __('Send Email', 'edusystem') . "</a>";
                // Copiar link al portapapeles usando JS (usando el campo 'link' como token)
                $dynamic_link_token = isset($item['link']) ? $item['link'] : '';
                $dynamic_link_url = site_url('/registration-link?token=' . $dynamic_link_token);
                $buttons .= "<a href='javascript:void(0);' onclick=\"copyToClipboard('{$dynamic_link_url}', this)\" style='margin-left: 4px' class='button button-secondary'>" . __('Copy Link', 'edusystem') . "</a>";
                return $buttons;
            default:
                return ucwords($item[$column_name]);
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
            'program' => __('Program', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'payment_plan' => __('Scholarship', 'edusystem'),
            'created_by' => __('Created by', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_dynamic_links()
    {
        global $wpdb;
        $dynamic_links_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $query_search = "";
        $query_args = [];
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search = $wpdb->esc_like($_GET['s']);
            $like = "%{$search}%";
            $query_search = "(`name` LIKE %s OR `last_name` LIKE %s OR `email` LIKE %s OR `id_document` LIKE %s)";
            $query_args = [$like, $like, $like, $like];
        }

        // Filtrado por rol
        $current_user = wp_get_current_user();
        $roles = (array) $current_user->roles;
        $is_manager = in_array('manager', $roles);
        $is_admin = in_array('administrator', $roles);
        $table = $wpdb->prefix . 'dynamic_links';

        $where = [];
        $args = [];
        if (!empty($query_search)) {
            $where[] = $query_search;
            $args = $query_args;
        }

        if (!$is_admin) {
            if ($is_manager) {
                // Manager: ve los que creó y los asignados a él
                $where[] = "(created_by = %d OR manager_id = %d)";
                $args[] = $current_user->ID;
                $args[] = $current_user->ID;
            } else {
                // Otros roles: ve los que creó y los de su manager
                $manager_user_id = get_user_meta($current_user->ID, 'manager_user_id', true);
                if (!empty($manager_user_id)) {
                    $where[] = "(created_by = %d OR manager_id = %d)";
                    $args[] = $current_user->ID;
                    $args[] = $manager_user_id;
                } else {
                    $where[] = "created_by = %d";
                    $args[] = $current_user->ID;
                }
            }
        }

        $where_sql = '';
        if (!empty($where)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where);
        }

        if (!empty($args)) {
            $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$table} {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d", array_merge($args, [$per_page, $offset]));
        } else {
            $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$table} {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset);
        }
        $dynamic_links = $wpdb->get_results($sql, "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($dynamic_links) {
            foreach ($dynamic_links as $dynamic_links_val) {
                $payment_plan = get_program_details_by_identificator($dynamic_links_val['payment_plan_identificator']);
                $program = get_student_program_details_by_identificator($dynamic_links_val['program_identificator']);
                $created_by_user = get_user_by('id', $dynamic_links_val['created_by']);
                array_push($dynamic_links_array, [
                    'id' => $dynamic_links_val['id'],
                    'program' => $program->name . ' (' . $program->identificator . ')',
                    'student' => $dynamic_links_val['name'] . ' ' . $dynamic_links_val['last_name'],
                    'payment_plan' => $payment_plan->name . ' (' . $payment_plan->identificator . ')',
                    'link' => $dynamic_links_val['link'],
                    'created_by' => $created_by_user->first_name . ' ' . $created_by_user->last_name,
                    'created_at' => $dynamic_links_val['created_at']
                ]);
            }
        }

        return ['data' => $dynamic_links_array, 'total_count' => $total_count];
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

        $data_dynamic_link = $this->get_dynamic_links();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_dynamic_link['data'];
        $total_count = (int) $data_dynamic_link['total_count'];

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

function get_dynamic_link_detail($dynamic_link_id)
{
    global $wpdb;

    $table = $wpdb->prefix . 'dynamic_links';
    $data = $wpdb->get_row("SELECT * FROM {$table} WHERE id={$dynamic_link_id}");
    return $data;
}

function get_dynamic_link_detail_by_link($dynamic_link)
{
    global $wpdb;

    $table = $wpdb->prefix . 'dynamic_links';
    $data = $wpdb->get_row("SELECT * FROM {$table} WHERE link='{$dynamic_link}'");
    return $data;
}

function get_hidden_payment_methods_by_plan($payment_plan_identificator)
{
    global $wpdb;

    // Obtener las filas existentes para el plan
    $payment_methods_by_plan = $wpdb->prefix . 'payment_methods_by_plan';
    $rows = $wpdb->get_results($wpdb->prepare("SELECT payment_method_identificator FROM {$payment_methods_by_plan} WHERE payment_plan_identificator=%s", $payment_plan_identificator));

    // Construir un set de identificadores existentes
    $existing = [];
    $connected_account = ''; // stripe
    $flywire_portal_code = ''; // stripe
    $zelle_account = ''; // stripe
    $bank_transfer_account = ''; // stripe
    if ($rows) {
        foreach ($rows as $r) {
            if (isset($r->payment_method_identificator)) {
                $existing[$r->payment_method_identificator] = true;
                switch ($r->payment_method_identificator) {
                    case 'woo_squuad_stripe':
                        $connected_account = $r->account_identificator;
                        break;
                    case 'flywire':
                        $flywire_portal_code = $r->account_identificator;
                        break;
                    case 'zelle_payment':
                        $zelle_account = $r->account_identificator;
                        break;
                    case 'aes_payment':
                        $bank_transfer_account = $r->account_identificator;
                        break;
                }
            }
        }
    }

    // Intentar obtener todos los métodos de pago de WooCommerce
    $missing = [];
    if (function_exists('WC')) {
        try {
            $gateways = WC()->payment_gateways()->payment_gateways();
            if (is_array($gateways)) {
                foreach ($gateways as $gateway_id => $gateway_obj) {
                    // $gateway_id es el identificador del gateway
                    if (!isset($existing[$gateway_id])) {
                        $missing[] = $gateway_id;
                    }
                }
            }
        } catch (Exception $e) {
            // En caso de error, devolver vacío
            return [];
        }
    } else {
        // Si WooCommerce no está disponible, devolver vacío
        return [];
    }

    // Preparar CSV de identificadores (por ejemplo: "paypal,stripe")
    $missing_csv = '';
    if (!empty($missing)) {
        $missing_csv = implode(',', $missing);
    }

    // Devolver un array asociativo con compatibilidad hacia atrás
    return array(
        'hidden_methods' => $missing, // array de identificadores
        'hidden_methods_csv' => $missing_csv, // CSV para compatibilidad con templates existentes
        'connected_account' => $connected_account,
        'flywire_portal_code' => $flywire_portal_code,
        'zelle_account' => $zelle_account,
        'bank_transfer_account' => $bank_transfer_account,
    );
}

// Agregar función JS para copiar al portapapeles solo en la página de dynamic links
if (!function_exists('edusystem_dynamic_links_copy_js')) {
    function edusystem_dynamic_links_copy_js() {
        if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_dynamic_link_content') {
            ?>
            <script>
            function copyToClipboard(text, el) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(function() {
                        el.innerText = 'Copied!';
                        setTimeout(function(){ el.innerText = '<?php echo esc_js(__('Copy Link', 'edusystem')); ?>'; }, 1500);
                    });
                } else {
                    var tempInput = document.createElement('input');
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    el.innerText = 'Copied!';
                    setTimeout(function(){ el.innerText = '<?php echo esc_js(__('Copy Link', 'edusystem')); ?>'; }, 1500);
                }
            }
            </script>
            <?php
        }
    }
    add_action('admin_footer', 'edusystem_dynamic_links_copy_js');
}