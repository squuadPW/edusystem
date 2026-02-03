<?php

function add_admin_form_academic_periods_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'generate_next_period') {
            try {
                global $wpdb;

                $table_academic_periods = $wpdb->prefix . 'academic_periods';
                $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

                // 1. Fetch the latest period
                $period = $wpdb->get_row("SELECT * FROM {$table_academic_periods} ORDER BY `year` DESC, code DESC LIMIT 1", OBJECT);

                if (!$period) {
                    setcookie('error', __('Period not available.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                    exit;
                }

                // --- DYNAMIC CODE AND NAME CALCULATION ---

                $new_year = (int) $period->year + 1;
                $original_code = $period->code;
                $original_name = $period->name;
                $new_code = '';
                $new_code_next = '';
                $new_name = '';

                // Check for YYYY format (e.g., 20252026)
                if (is_numeric($original_code) && strlen($original_code) === 8) {
                    $new_start_code = (int) substr($original_code, 0, 4) + 1;
                    $new_end_code = (int) substr($original_code, 4, 4) + 1;
                    $new_code = (string) $new_start_code . (string) $new_end_code;
                    $new_code_next = (string) ($new_start_code + 1) . (string) ($new_end_code + 1);

                    // Assuming name format is 'Academic Year YYYY-YYYY'
                    $new_name = 'Academic Year ' . (string) $new_start_code . '-' . (string) $new_end_code;
                }
                // Check for TEXTYYYY or TEXTYYYYTEXT (hybrid format, e.g., FALL2025, YYYY-YYYY, 2025-2026)
                else if (preg_match('/(\d{4})/', $original_code, $matches)) {
                    $start_year = (int) $matches[1];
                    $next_start_year = $start_year + 1;

                    // Reconstruct Code: Replace the first occurrence of the year with the incremented year
                    // This handles codes like FALL2025, SEMESTER_2025, or even 2025_A
                    $new_code = preg_replace('/' . preg_quote((string) $start_year, '/') . '/', (string) $next_start_year, $original_code, 1);

                    // Calculate code_next: If current code has two year components (like 2025-2026), this is complex.
                    // For safety and simplicity, if a clear YYYY-YYYY pattern is not found, we base code_next
                    // on the first found year + 2 years, or set it to null/empty if unclear.
                    if (preg_match('/(\d{4})-(\d{4})/', $original_code, $name_matches)) {
                        // If it looks like '2025-2026', calculate the next one properly
                        $start_code_next = (int) $name_matches[1] + 2;
                        $end_code_next = (int) $name_matches[2] + 2;
                        $new_code_next = (string) $start_code_next . (string) $end_code_next;
                    } else {
                        // For FALL2025 or other single year codes, next next is less clear, use default
                        $new_code_next = '';
                    }

                    // Reconstruct Name: Replace the year in the name.
                    if (preg_match('/(\d{4})/', $original_name, $name_matches)) {
                        $new_name = preg_replace('/' . preg_quote((string) $name_matches[1], '/') . '/', (string) $next_start_year, $original_name, 1);

                        // If the name contains two year parts (e.g., 2025-2026), ensure both are incremented
                        if (preg_match('/(\d{4})-(\d{4})/', $original_name)) {
                            $new_end_year = (int) $name_matches[1] + 2; // Assuming the second year is the first year + 1, so new end year is (start_year+1)+1
                            $new_name = preg_replace('/(\d{4})-(\d{4})/', (string) $next_start_year . '-' . (string) $new_end_year, $new_name);
                        }
                    } else {
                        // Fallback for name calculation if year is not found
                        $new_name = $original_name . ' - Next Period ' . $new_year;
                    }
                }
                // Fallback for codes that don't contain a clear 4-digit year (e.g., TERM_A)
                else {
                    $new_code = $original_code . '_' . $new_year; // e.g., TERM_A_2026
                    $new_name = $original_name . ' (' . $new_year . ')';
                }

                // --- End of Dynamic Code Calculation ---

                // Date calculations: Add 1 year to all relevant dates (same robust logic)
                $new_start_date = (new DateTime($period->start_date))->modify('+1 year')->format('Y-m-d');
                $new_end_date = (new DateTime($period->end_date))->modify('+1 year')->format('Y-m-d');
                $new_start_date_inscription = $period->start_date_inscription ? (new DateTime($period->start_date_inscription))->modify('+1 year')->format('Y-m-d') : null;
                $new_end_date_inscription = $period->end_date_inscription ? (new DateTime($period->end_date_inscription))->modify('+1 year')->format('Y-m-d') : null;
                $new_start_date_pre_inscription = $period->start_date_pre_inscription ? (new DateTime($period->start_date_pre_inscription))->modify('+1 year')->format('Y-m-d') : null;
                $new_end_date_pre_inscription = $period->end_date_pre_inscription ? (new DateTime($period->end_date_pre_inscription))->modify('+1 year')->format('Y-m-d') : null;

                // Insertion of new academic period
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
                    'status_id' => $period->status_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'current' => 0
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

                // Fetch previous cuts, using $wpdb->prepare for security
                $cuts_query = $wpdb->prepare(
                    "SELECT * FROM {$table_academic_periods_cut} WHERE code = %s ORDER BY cut ASC",
                    $period->code
                );
                $cuts = $wpdb->get_results($cuts_query);

                if ($new_period_id && $cuts) {
                    foreach ($cuts as $cut) {
                        // Note: Assuming cut dates (start_date, end_date, max_date) are also 'Y-m-d' strings
                        // and should be incremented by one year, even in hybrid codes.
                        $wpdb->insert($table_academic_periods_cut, [
                            'code' => $new_code,
                            'cut' => $cut->cut,
                            'start_date' => (new DateTime($cut->start_date))->modify('+1 year')->format('Y-m-d'),
                            'end_date' => (new DateTime($cut->end_date))->modify('+1 year')->format('Y-m-d'),
                            'max_date' => (new DateTime($cut->max_date))->modify('+1 year')->format('Y-m-d'),
                        ], [
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                        ]);
                    }
                }

                setcookie('message', __('Period generated correctly.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            } catch (\Throwable $th) {
                setcookie('error', __('Error generating period: ', 'edusystem') . $th->getMessage(), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {

        if ($_GET['section_tab'] == 'period_details' || $_GET['section_tab'] == 'add_period') {
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

            if ($cuts) {
                foreach ($cuts as $cut) {

                    if ($cut['id']) {
                        $wpdb->update($table_academic_periods_cut, [
                            'cut' =>  $cut['cut'],
                            'start_date' => $cut['start_date'],
                            'end_date' => $cut['end_date'],
                            'max_date' => $cut['max_date'],
                        ], ['id' => $cut['id'],]);
                    } else {
                        $wpdb->insert($table_academic_periods_cut, [
                            'code' => $code,
                            'cut' =>  $cut['cut'],
                            'start_date' => $cut['start_date'],
                            'end_date' => $cut['end_date'],
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
        } else if ($_GET['action'] == 'delete_period') {

            $period_id = $_POST['period_id'];
            $period_code = $_POST['period_code'];

            global $wpdb;
            $student_period_inscriptions = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(id) FROM `{$wpdb->prefix}student_period_inscriptions` WHERE code_period LIKE %s",
                $period_code,
            ));

            // Si no hay registros en tabla, proceder a eliminar
            if ($student_period_inscriptions == 0) {

                // elimina los cortes
                $deleted = $wpdb->delete(
                    $wpdb->prefix . "academic_periods_cut",
                    ['code' => $period_code],
                    ['%s']
                );

                // eleimina el periodo
                $deleted = $wpdb->delete(
                    $wpdb->prefix . "academic_periods",
                    ['id' => $period_id],
                    ['%d']
                );

                if ($deleted) {
                    setcookie('message', __('The period has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The period was not removed correctly.', 'edusystem'), time() + 10, '/');
                }
            } else {
                setcookie('message-error', __('The period contains enrolled students.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
            exit;
        } else if ($_GET['action'] == 'delete_cut') {

            $cut_id = $_POST['cut_id'];
            $cut = $_POST['cut'];
            $period_code = $_POST['period_code'];

            global $wpdb;
            $student_period_inscriptions = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(id) FROM `{$wpdb->prefix}student_period_inscriptions` WHERE code_period LIKE %s AND cut_period LIKE %s",
                $period_code,
                $cut
            ));

            // Si no hay registros en tabla, proceder a eliminar
            if ($student_period_inscriptions == 0) {

                $deleted = $wpdb->delete(
                    $wpdb->prefix . "academic_periods_cut",
                    ['id' => $cut_id],
                    ['%d']
                );

                if ($deleted) {
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
            include(plugin_dir_path(__FILE__) . 'templates/modal-delete-period.php');
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
                $deleted = "<a class='button button-danger' data-period_id='{$item['academic_period_id']}' data-period_code='{$item['academic_period_code']}' onclick='modal_delete_period_js( this )' ><span class='dashicons dashicons-trash'></span></a>";
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $item['academic_period_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>" . $deleted;
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

function get_period_cut_details_code($code, $cut)
{

    global $wpdb;
    $table_periods_cuts = $wpdb->prefix . 'academic_periods_cut';

    $period = $wpdb->get_row("SELECT * FROM {$table_periods_cuts} WHERE code='{$code}' AND cut='{$cut}'");
    return $period;
}

function get_period_details_cuts($code)
{

    global $wpdb;
    $table_periods_cuts = $wpdb->prefix . 'academic_periods_cut';

    $cuts = $wpdb->get_results("SELECT * FROM {$table_periods_cuts} WHERE code = '{$code}' ORDER BY cut ASC");
    return $cuts;
}

/**
 * Verifica si un codigo de un periodo ya existe en la base de datos.
 * Esta función se utiliza para comprobar la disponibilidad de un codigo
 * antes de que se realice una acción que dependa de su unicidad.
 * 
 * @return void
 * 
 * @throws WP_Error Si el codigo no es proporcionado o está vacío.
 * 
 * La función envía una respuesta JSON indicando si el codigo ya está en uso
 * o si está disponible. La respuesta incluye un mensaje que puede ser utilizado
 * para informar al usuario sobre el estado del identificador.
 */
add_action('wp_ajax_check_period_code_exists', 'check_period_code_exists');
add_action('wp_ajax_nopriv_check_period_code_exists', 'check_period_code_exists');
function check_period_code_exists()
{

    if (!isset($_POST['code']) || empty($_POST['code'])) {
        wp_send_json_error(__('code not provided', 'edusystem'));
    }

    $code = sanitize_text_field($_POST['code']);

    global $wpdb;
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM `{$wpdb->prefix}academic_periods` WHERE code LIKE %s",
        $code
    ));

    if ($exists) {
        wp_send_json_success([
            'exists' => true,
            'message' => __('Code in use, please choose another.', 'edusystem'),
        ]);
    } else {
        wp_send_json_success([
            'exists' => false,
            'message' => __('Code is not in use.', 'edusystem'),
        ]);
    }
}
