<?php

function add_admin_form_dynamic_link_content()
{
    global $wpdb;
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        $payment_plans = get_payment_plans();
        $programs = get_student_program();
        $dynamic_links_email_log = array();

        if ($_GET['section_tab'] == 'dynamic_link_details') {
            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $dynamic_link = get_dynamic_link_detail($dynamic_link_id);
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
            $transfer_cr = $_POST['transfer_cr'] ?? 0;

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
                    'transfer_cr' => $transfer_cr
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
                    'transfer_cr' => $transfer_cr
                ]);
                $dynamic_link_id = $wpdb->insert_id;
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=') . $dynamic_link_id);
            }

            if ($save_and_send_email == '1') {
                $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
                $wpdb->insert($table_dynamic_links_email_log, [
                    'dynamic_link_id' => $dynamic_link_id,
                    'email' => $email,
                ]);
            }

            exit;
        } else if ($_GET['action'] == 'delete_dynamic_link') {
            global $wpdb;
            $table = $wpdb->prefix . 'dynamic_links';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $wpdb->delete($table, ['id' => $dynamic_link_id]);

            setcookie('message', __('Dynamic link deleted.', 'edusystem'), time() + 10, '/');
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
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&action=copy_link&dynamic_link_id=' . $item['id']) . "' class='button button-secondary'>" . __('Copy Link', 'edusystem') . "</a>";
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
            'transfer_credits' => __('Transfer Credits', 'edusystem'),
            'payment_plan' => __('Scholarship', 'edusystem'),
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
            $query_search = "WHERE (`name` LIKE %s OR `last_name` LIKE %s OR `email` LIKE %s OR `id_document` LIKE %s)";
            $query_args = [$like, $like, $like, $like];
        }

        $table = $wpdb->prefix . 'dynamic_links';
        if (!empty($query_search)) {
            $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$table} {$query_search} ORDER BY id DESC LIMIT %d OFFSET %d", array_merge($query_args, [$per_page, $offset]));
        } else {
            $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset);
        }
        $dynamic_links = $wpdb->get_results($sql, "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($dynamic_links) {
            foreach ($dynamic_links as $dynamic_links_val) {
                $payment_plan = get_program_details_by_identificator($dynamic_links_val['payment_plan_identificator']);
                $program = get_student_program_details_by_identificator($dynamic_links_val['program_identificator']);
                array_push($dynamic_links_array, [
                    'id' => $dynamic_links_val['id'],
                    'program' => $program->name,
                    'student' => $dynamic_links_val['name'] . ' ' . $dynamic_links_val['last_name'],
                    'transfer_credits' => $dynamic_links_val['transfer_cr'] == 1 ? __('Yes', 'edusystem') : __('No', 'edusystem'),
                    'payment_plan' => $payment_plan->name,
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