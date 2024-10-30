<?php

function add_admin_form_school_subjects_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'subject_details') {
            $subject_id = $_GET['subject_id'];
            $subject = get_subject_details($subject_id);
            include (plugin_dir_path(__FILE__) . 'templates/school-subject-detail.php');
        }
        if ($_GET['section_tab'] == 'add_subject') {
            include (plugin_dir_path(__FILE__) . 'templates/school-subject-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_subject_details') {
            global $wpdb;
            $table_school_subjects = $wpdb->prefix . 'school_subjects';

            $subject_id = $_POST['subject_id'];
            $name = strtoupper($_POST['name']);
            $code_subject = strtoupper($_POST['code_subject']);
            $description = $_POST['description'];
            $hc = $_POST['hc'];
            $is_elective = $_POST['is_elective'];

            //update
            if (isset($subject_id) && !empty($subject_id)) {

                $wpdb->update($table_school_subjects, [
                    'name' => $name,
                    'code_subject' => $code_subject,
                    'description' => $description,
                    'hc' => $hc,
                    'is_elective' => $is_elective == 'on' ? 1 : 0
                ], ['id' => $subject_id]);

                setcookie('message', __('Changes saved successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_school_subjects_content&section_tab=subject_details&subject_id=' . $subject_id));
                exit;
            } else {

                $wpdb->insert($table_school_subjects, [
                    'name' => $name,
                    'code_subject' => $code_subject,
                    'description' => $description,
                    'hc' => $hc,
                    'is_elective' => $is_elective == 'on' ? 1 : 0
                ]);

                wp_redirect(admin_url('admin.php?page=add_admin_form_school_subjects_content'));
                exit;

            }
        } else {
            $list_school_subjects = new TT_school_subjects_all_List_Table;
            $list_school_subjects->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-school-subjects.php');
        }
    }
}

class TT_school_subjects_all_List_Table extends WP_List_Table
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
            case 'code_subject':
                return ucwords($item[$column_name]);
            case 'name':
                return ucwords($item[$column_name]);
            case 'is_elective':
                switch ($item[$column_name]) {
                    case 1:
                        return 'Yes';
                        break;

                    default:
                        return 'No';
                        break;
                }
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_school_subjects_content&section_tab=subject_details&subject_id=' . $item['school_subject_id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
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
            'code_subject' => __('Code of subject', 'aes'),
            'name' => __('Name', 'aes'),
            'is_elective' => __('Is elective', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_school_subject_pendings()
    {
        global $wpdb;
        $school_subjects_array = [];

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $search = $_POST['s'];
            $school_subjects = $wpdb->get_results("SELECT * FROM wp_school_subjects WHERE (`name` LIKE '%{$search}%' || code_subject LIKE '%{$search}%')");
        } else {
            $school_subjects = $wpdb->get_results("SELECT * FROM wp_school_subjects");
        }

        if ($school_subjects) {
            foreach ($school_subjects as $subject) {
                array_push($school_subjects_array, [
                    'code_subject' => $subject->code_subject,
                    'school_subject_id' => $subject->id,
                    'name' => $subject->name,
                    'is_elective' => $subject->is_elective,
                ]);
            }
        }

        return $school_subjects_array;
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

        $data_school_subjects = $this->get_school_subject_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_school_subjects;

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $this->items = $data;
    }

}

function get_subject_details($subject_id)
{
    global $wpdb;
    $table_school_subjects = $wpdb->prefix.'school_subjects';

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id={$subject_id}");
    return $subject;
}
