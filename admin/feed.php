<?php

function add_admin_form_feed_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'feed_details') {
            $feed_id = $_GET['feed_id'];
            $feed = get_feed_detail($feed_id);
            include(plugin_dir_path(__FILE__) . 'templates/feed-detail.php');
        }

        if ($_GET['section_tab'] == 'add_feed') {
            include(plugin_dir_path(__FILE__) . 'templates/feed-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_feed_details') {
            global $wpdb;

            $table = $wpdb->prefix . 'feed';
            $feed_id = $_POST['feed_id'];
            $feed = get_feed_detail($feed_id);
            $title = strtoupper($_POST['title']);
            $link = sanitize_text_field($_POST['link']);
            $max_date = (isset($_POST['max_date']) && $_POST['max_date'] !== '') ? $_POST['max_date'] : NULL;

            if (isset($_FILES['attach_id_desktop']) && !empty($_FILES['attach_id_desktop'])) {
                $file_temp_desktop = $_FILES['attach_id_desktop'];
            } else {
                $file_temp_desktop = [];
            }

            if (!empty($file_temp_desktop['tmp_name'])) {
                $upload_data = wp_handle_upload($file_temp_desktop, array('test_form' => FALSE));
                if ($upload_data && !is_wp_error($upload_data)) {
                    $attach_desktop_id = upload_file_attchment_edusystem($upload_data, 'DESKTOP');
                }
            } else {
                $attach_desktop_id = $feed->attach_id_desktop;
            }

            if (isset($_FILES['attach_id_mobile']) && !empty($_FILES['attach_id_mobile'])) {
                $file_temp_mobile = $_FILES['attach_id_mobile'];
            } else {
                $file_temp_mobile = [];
            }

            if (!empty($file_temp_mobile['tmp_name'])) {
                $upload_data = wp_handle_upload($file_temp_mobile, array('test_form' => FALSE));
                if ($upload_data && !is_wp_error($upload_data)) {
                    $attach_mobile_id = upload_file_attchment_edusystem($upload_data, 'DESKTOP');
                }
            } else {
                $attach_mobile_id = $feed->attach_id_mobile;
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            if (isset($feed_id) && !empty($feed_id)) {
                $wpdb->update($table, [
                    'title' => $title,
                    'attach_id_desktop' => $attach_desktop_id,
                    'attach_id_mobile' => $attach_mobile_id,
                    'link' => $link,
                    'max_date' => $max_date,
                ], ['id' => $feed_id]);
                wp_redirect(admin_url('admin.php?page=add_admin_form_feed_content&section_tab=feed_details&feed_id=') . $feed_id);
            } else {
                $wpdb->insert($table, [
                    'title' => $title,
                    'attach_id_desktop' => $attach_desktop_id,
                    'attach_id_mobile' => $attach_mobile_id,
                    'link' => $link,
                    'max_date' => $max_date,
                ]);
                wp_redirect(admin_url('admin.php?page=add_admin_form_feed_content'));
            }

            exit;
        } else if ($_GET['action'] == 'delete_feed') {
            global $wpdb;
            $table = $wpdb->prefix . 'feed';
            $feed_id = $_GET['feed_id'];
            $wpdb->delete($table, ['id' => $feed_id]);

            setcookie('message', __('Feed deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_feed_content'));
            exit;
        } else {
            $list_feed = new TT_Feed_all_List_Table;
            $list_feed->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-feed.php');
        }
    }
}

class TT_Feed_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
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
                $buttons = "<a href='" . admin_url('/admin.php?page=add_admin_form_feed_content&section_tab=feed_details&feed_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_feed_content&action=delete_feed&feed_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
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
            'title' => __('Title', 'edusystem'),
            'max_date' => __('Maximum visible date', 'edusystem'),
            'img_desktop' => __('Img desktop', 'edusystem'),
            'img_mobile' => __('Img mobile', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_feed()
    {
        global $wpdb;
        $feed_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $query_search = "";
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search = $_GET['s'];
            $query_search = "WHERE (`title` LIKE '%{$search}%')";
        }

        $feed = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM wp_feed {$query_search} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($feed) {
            foreach ($feed as $feed_val) {
                array_push($feed_array, [
                    'id' => $feed_val['id'],
                    'title' => $feed_val['title'],
                    'max_date' => $feed_val['max_date'] ?? 'N/A',
                    'created_at' => $feed_val['created_at'],
                    'img_desktop' => wp_get_attachment_url($feed_val['attach_id_desktop'], 'full'),
                    'img_mobile' => wp_get_attachment_url($feed_val['attach_id_mobile'], 'full'),
                ]);
            }
        }

        return ['data' => $feed_array, 'total_count' => $total_count];
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

        $data_feed = $this->get_feed();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_feed['data'];
        $total_count = (int) $data_feed['total_count'];

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

function get_feed_detail($feed_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'feed';

    $data = $wpdb->get_row("SELECT * FROM {$table} WHERE id={$feed_id}");
    return $data;
}