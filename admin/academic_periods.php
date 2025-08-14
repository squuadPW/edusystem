<?php

function add_admin_form_academic_periods_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_academic_period') {
            try {
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {

        if ($_GET['section_tab'] == 'period_details' || $_GET['section_tab'] == 'add_period' ) {
            $period = $_GET['period_id'];
            $period = get_period_details($period);
            $cuts = get_period_details_cuts($period->code);
            include(plugin_dir_path(__FILE__) . 'templates/academic-period-detail.php');
            include(plugin_dir_path(__FILE__) . 'templates/modal-delete-cut.php');
        }

    } else {

        if ($_GET['action'] == 'save_period_details') {

            global $wpdb;
            $table_periods = $wpdb->prefix . 'academic_periods';
            $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

            $period_id = $_POST['period_id'] ?? null;

            $status_id = $_POST['status_id'] ?? 0;
            $code = $_POST['code'];
            $name = $_POST['name'];
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $year = $_POST['year'] ?? null;
            $code_next = $_POST['code_next'];

            $start_date_inscriptions = $_POST['start_date_inscriptions'] ?? null;
            $end_date_inscriptions = $_POST['end_date_inscriptions'] ?? null;
            $start_date_pre_inscriptions = $_POST['start_date_pre_inscriptions'] ?? null;
            $end_date_pre_inscriptions = $_POST['end_date_pre_inscriptions'] ?? null;

            $cuts = $_POST['cuts'] ?? null;

            if( $cuts ){
                foreach ($cuts as $cut) {

                    if( $cut['id'] ) {
                        $wpdb->update($table_academic_periods_cut, [
                            'cut' =>  $cut['cut'],
                            'start_date' => $cut['start_date'],
                            'end_date' =>$cut['end_date'],
                            'max_date' => $cut['max_date'],
                        ], ['id' => $cut['id'], ]);

                    } else {
                        $wpdb->insert($table_academic_periods_cut, [
                            'code' => $code,
                            'cut' =>  $cut['cut'],
                            'start_date' => $cut['start_date'],
                            'end_date' =>$cut['end_date'],
                            'max_date' => $cut['max_date'],
                        ]);
                    }
                }
            }

            //update
            if (isset($period_id) && !empty($period_id)) {

                $wpdb->update($table_periods, [
                    'name' => $name,
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

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');

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
                ]);

                $period_id = $wpdb->insert_id;

                setcookie('message', __('A period has been successfully created.', 'edusystem'), time() + 10, '/');

            }

            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $period_id));
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
                    setcookie('message', __('The cut has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The cut was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The cut contains enrolled students.', 'edusystem'), time() + 10, '/');
            }
        
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

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

    $cuts = $wpdb->get_results("SELECT * FROM {$table_periods_cuts} WHERE code = '{$code}' ORDER BY cut ASC");
    return $cuts;
}