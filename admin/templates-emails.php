<?php

function add_admin_form_templates_emails_content()
{
    if($_GET['action'] == 'delete') {
        global $wpdb;
        $table_templates_email = $wpdb->prefix . 'templates_email';
        $template_id = $_GET['template_id'];

        $wpdb->delete($table_templates_email, ['id' => $template_id]);

        setcookie('message', __('Template deleted.', 'edusystem'), time() + 10, '/');
        wp_redirect(admin_url('admin.php?page=add_admin_form_templates_emails_content'));
        exit;
    }

    $list_templates_emails = new TT_Templates_Emails_List_Table;
    $list_templates_emails->prepare_items();
    include (plugin_dir_path(__FILE__) . 'templates/list-templates-emails.php');
}

class TT_Templates_Emails_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' href='" . admin_url('/admin.php?page=add_admin_form_templates_emails_content&action=delete&template_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
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
            'title' => __('title', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_templates()
    {
        global $wpdb;
        $templates_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_templates_email = $wpdb->prefix . 'templates_email';
        $templates = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_templates_email} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($templates) {
            foreach ($templates as $offer) {
                array_push($templates_array, [
                    'id' => $offer['id'],
                    'title' => $offer['title']
                ]);
            }
        }

        return ['data' => $templates_array, 'total_count' => $total_count];
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

        $data_academic_offers = $this->get_templates();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_academic_offers['data'];
        $total_count = (int) $data_academic_offers['total_count'];

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


