<?php

function add_admin_form_academic_periods_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'generate_next_period') {
            try {
                global $wpdb;

                $table_academic_periods = $wpdb->prefix . 'academic_periods';
                $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
                $period = $wpdb->get_row("SELECT * FROM {$table_academic_periods} ORDER BY `year` DESC, code DESC LIMIT 1", OBJECT);

                if ($period) {
                    $new_year = (int) $period->year + 1;
                    $new_start_code = (int) substr($period->code, 0, 4) + 1;
                    $new_end_code = (int) substr($period->code, 4, 4) + 1;
                    $new_code = (string) $new_start_code . (string) $new_end_code;
                    $new_code_next = (string) ($new_start_code + 1) . (string) ($new_end_code + 1);
                    $new_name = 'Academic Year ' . (string) $new_start_code . '-' . (string) $new_end_code;
                    $new_start_date = (new DateTime($period->start_date))->modify('+1 year')->format('Y-m-d');
                    $new_end_date = (new DateTime($period->end_date))->modify('+1 year')->format('Y-m-d');
                    $new_start_date_inscription = $period->start_date_inscription ? (new DateTime($period->start_date_inscription))->modify('+1 year')->format('Y-m-d') : null;
                    $new_end_date_inscription = $period->end_date_inscription ? (new DateTime($period->end_date_inscription))->modify('+1 year')->format('Y-m-d') : null;
                    $new_start_date_pre_inscription = $period->start_date_pre_inscription ? (new DateTime($period->start_date_pre_inscription))->modify('+1 year')->format('Y-m-d') : null;
                    $new_end_date_pre_inscription = $period->end_date_pre_inscription ? (new DateTime($period->end_date_pre_inscription))->modify('+1 year')->format('Y-m-d') : null;

                    $wpdb->insert($table_academic_periods, [
                        'name' => $new_name,
                        'code' => $new_code,
                        'code_next' => $new_code_next,
                        'year' => $new_year,
                        'start_date' => $new_start_date,
                        'end_date' => $new_end_date,
                        'start_date_inscription' => $new_start_date_inscription,
                        'end_date_inscription' => $new_end_date_inscription,
                        'start_date_pre_inscription' => $new_start_date_pre_inscription,
                        'end_date_pre_inscription' => $new_end_date_pre_inscription,
                        'status_id' => $period->status_id, // Keep the same status or change it as needed
                        'created_at' => date('Y-m-d H:i:s'),
                        'current' => 0 // New period should probably not be 'current' initially
                    ], [
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%d'
                    ]);

                    $new_period_id = $wpdb->insert_id;
                    $cuts = $wpdb->get_results("SELECT * FROM {$table_academic_periods_cut} where code = '{$period->code}' order by cut asc");
                    if ($new_period_id && isset($cuts)) {
                        foreach ($cuts as $cut) {
                            $wpdb->insert($table_academic_periods_cut, [
                                'code' => $new_code,
                                'cut' => $cut->cut,
                                'start_date' => (new DateTime($cut->start_date))->modify('+1 year')->format('Y-m-d'),
                                'end_date' => (new DateTime($cut->end_date))->modify('+1 year')->format('Y-m-d'),
                                'max_date' => (new DateTime($cut->max_date))->modify('+1 year')->format('Y-m-d'),
                            ]);
                        }

                        setcookie('message', __('Period generated correctly.', 'edusystem'), time() + 10, '/');
                    }

                } else {
                    setcookie('error', __('Period not available.', 'edusystem'), time() + 10, '/');
                }

                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            } catch (\Throwable $th) {
                // Use setcookie for error message to allow wp_redirect to work
                setcookie('error', __('Error generating period: ', 'edusystem') . $th->getMessage(), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'period_details') {
            $period = $_GET['period_id'];
            $period = get_period_details($period);
            $cuts = get_period_details_cuts($period->code);
            include(plugin_dir_path(__FILE__) . 'templates/academic-period-detail.php');
        }
        if ($_GET['section_tab'] == 'add_period') {
            include(plugin_dir_path(__FILE__) . 'templates/academic-period-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_period_details') {
            global $wpdb;
            $table_periods = $wpdb->prefix . 'academic_periods';
            $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

            $period_id = $_POST['period_id'];
            $name = $_POST['name'];
            $code = $_POST['code'];
            $code_next = $_POST['code_next'];
            $old_code = $_POST['old_code'];
            $year = $_POST['year'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $start_date_inscriptions = $_POST['start_date_inscriptions'] ?? null;
            $end_date_inscriptions = $_POST['end_date_inscriptions'] ?? null;
            $start_date_pre_inscriptions = $_POST['start_date_pre_inscriptions'] ?? null;
            $end_date_pre_inscriptions = $_POST['end_date_pre_inscriptions'] ?? null;
            $status_id = $_POST['status_id'] ?? 0;
            $cuts = ['A', 'B', 'C', 'D', 'E'];
            $cuts_arr = [
                'A' => [
                    'start_date' => $_POST['start_date_A'],
                    'end_date' => $_POST['end_date_A'],
                    'max_date' => $_POST['max_date_A'],
                ],
                'B' => [
                    'start_date' => $_POST['start_date_B'],
                    'end_date' => $_POST['end_date_B'],
                    'max_date' => $_POST['max_date_B'],
                ],
                'C' => [
                    'start_date' => $_POST['start_date_C'],
                    'end_date' => $_POST['end_date_C'],
                    'max_date' => $_POST['max_date_C'],
                ],
                'D' => [
                    'start_date' => $_POST['start_date_D'],
                    'end_date' => $_POST['end_date_D'],
                    'max_date' => $_POST['max_date_D'],
                ],
                'E' => [
                    'start_date' => $_POST['start_date_E'],
                    'end_date' => $_POST['end_date_E'],
                    'max_date' => $_POST['max_date_E'],
                ],
            ];

            //update
            if (isset($period_id) && !empty($period_id)) {

                $wpdb->update($table_periods, [
                    'name' => $name,
                    'code' => $code,
                    'code_next' => $code_next,
                    'year' => $year,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status_id' => $status_id,
                    'start_date_inscription' => $start_date_inscriptions,
                    'end_date_inscription' => $end_date_inscriptions,
                    'start_date_pre_inscription' => $start_date_pre_inscriptions,
                    'end_date_pre_inscription' => $end_date_pre_inscriptions,
                ], ['id' => $period_id]);

                foreach ($cuts as $key => $cut) {
                    $start_date = $cuts_arr[$cut]['start_date'];
                    $end_date = $cuts_arr[$cut]['end_date'];
                    $max_date = $cuts_arr[$cut]['max_date'];

                    $wpdb->update($table_academic_periods_cut, [
                        'code' => $code,
                        'cut' => $cut,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'max_date' => $max_date,
                    ], ['code' => $old_code, 'cut' => $cut]);
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $period_id));
                exit;
            } else {

                $wpdb->insert($table_periods, [
                    'name' => $name,
                    'code' => $code,
                    'code_next' => $code_next,
                    'year' => $year,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date_inscription' => $start_date_inscriptions,
                    'end_date_inscription' => $end_date_inscriptions,
                    'start_date_pre_inscription' => $start_date_pre_inscriptions,
                    'end_date_pre_inscription' => $end_date_pre_inscriptions,
                    'status_id' => $status_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                foreach ($cuts as $key => $cut) {
                    $wpdb->insert($table_academic_periods_cut, [
                        'code' => $code,
                        'cut' => $cut,
                        'start_date' => $cuts_arr[$cut]['start_date'],
                        'end_date' => $cuts_arr[$cut]['end_date'],
                        'max_date' => $cuts_arr[$cut]['max_date'],
                    ]);
                }

                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;

            }
        } else {
            $list_academic_periods = new TT_academic_period_all_List_Table;
            $list_academic_periods->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-academic-periods.php');
        }
    }
}

class TT_academic_period_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'academic_period_',
                'plural' => 'academic_period_s',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'academic_period_id':
                return '#' . $item[$column_name];
            case 'name':
                return ucwords($item[$column_name]);
            case 'status_id':
                switch ($item[$column_name]) {
                    case 1:
                        return 'Active';
                        break;

                    default:
                        return 'Inactive';
                        break;
                }
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $item['academic_period_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'academic_period_code' => __('Period', 'edusystem'),
            'name' => __('Description', 'edusystem'),
            'status_id' => __('Status', 'edusystem'),
            'date' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_academic_period_pendings()
    {
        global $wpdb;
        $academic_periods_array = [];

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $search = $_POST['s'];
            $academic_periods = $wpdb->get_results("SELECT * FROM wp_academic_periods WHERE (`name` LIKE '%{$search}%' || code LIKE '%{$search}%')");
        } else {
            $academic_periods = $wpdb->get_results("SELECT * FROM wp_academic_periods");
        }


        if ($academic_periods) {
            foreach ($academic_periods as $academic_period) {
                array_push($academic_periods_array, [
                    'academic_period_code' => $academic_period->code,
                    'academic_period_id' => $academic_period->id,
                    'name' => $academic_period->name,
                    'status_id' => $academic_period->status_id,
                    'date' => $academic_period->created_at,
                ]);
            }
        }

        return $academic_periods_array;
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

        $data_academic_periods = $this->get_academic_period_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_academic_periods;

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

function get_period_details($period_id)
{

    global $wpdb;
    $table_periods = $wpdb->prefix . 'academic_periods';

    $period = $wpdb->get_row("SELECT * FROM {$table_periods} WHERE id={$period_id}");
    return $period;
}


function get_period_details_code($code)
{

    global $wpdb;
    $table_periods = $wpdb->prefix . 'academic_periods';

    $period = $wpdb->get_row("SELECT * FROM {$table_periods} WHERE code='{$code}'");
    return $period;
}

function get_period_details_cuts($code)
{

    global $wpdb;
    $table_periods_cuts = $wpdb->prefix . 'academic_periods_cut';

    $cuts = $wpdb->get_results("SELECT * FROM {$table_periods_cuts} WHERE code={$code} ORDER BY cut ASC");
    return $cuts;
}