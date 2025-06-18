<?php

function add_admin_institutes_content()
{

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
            $institute_id = $_POST['institute_id'];

            $name = $_POST['name'];
            $phone = $_POST['phone_hidden'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $level = $_POST['level'];
            $fee = str_replace('%', '', $_POST['fee']);
            $rector_name = $_POST['rector_name'];
            $rector_last_name = $_POST['rector_last_name'];
            $rector_phone = $_POST['rector_phone_hidden'];
            $contact_name = $_POST['contact_name'];
            $contact_last_name = $_POST['contact_last_name'];
            $contact_phone = $_POST['contact_phone_hidden'];
            $address = $_POST['address'];
            $description = $_POST['description'];
            $reference = $_POST['reference'];
            $business_name = $_POST['business_name'];
            $alliance_id = $_POST['alliance'];
            $type_calendar = $_POST['type_calendar'];
            $lower_text = $_POST['lower_text'];
            $middle_text = $_POST['middle_text'];
            $upper_text = $_POST['upper_text'];
            $graduated_text = $_POST['graduated_text'];

            //update
            if (isset($institute_id) && !empty($institute_id)) {

                $wpdb->update($table_institutes, [
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
                    'alliance_id' => $alliance_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $institute_id]);

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id=' . $institute_id));
                exit;


                //insert
            } else {

                $user = get_user_by('email', $email);

                if (!$user) {

                    $wpdb->insert($table_institutes, [
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
                        'alliance_id' => $alliance_id,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $institute_id = $wpdb->insert_id;
                    $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
                    create_user_institute($data_institute);

                    $email_approved_institute = WC()->mailer()->get_emails()['WC_Approved_Institution_Email'];
                    $email_approved_institute->trigger($institute_id);
                    setcookie('message', $name, time() + 3600, '/');
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
            setcookie('message-delete', $data_institute->name, time() + 3600, '/');
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
        global $wpdb;
        $table_institutes = $wpdb->prefix . 'institutes';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {

            $search = $_POST['s'];

            $institutes = $wpdb->get_results(
                "SELECT SQL_CALC_FOUND_ROWS * 
                FROM {$table_institutes} WHERE 
                `status` = 0  AND 
                ( `name` LIKE '%{$search}%' || 
                email LIKE '%{$search}%' || 
                name_rector LIKE '%{$search}%' || 
                lastname_rector LIKE '%{$search}%' || 
                name_contact LIKE '%{$search}%' || 
                lastname_contact LIKE '%{$search}%') LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        } else {
            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status` = 0 LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        }

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $institutes, 'total_count' => $total_count];
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

        global $wpdb;
        $table_institutes = $wpdb->prefix . 'institutes';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {

            $search = $_POST['s'];

            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * 
                FROM {$table_institutes}
                WHERE `status`=1
                AND 
                ( `name` LIKE '%{$search}%' ||
                   email LIKE '%{$search}%' || 
                   name_rector LIKE '%{$search}%' || 
                   lastname_rector LIKE '%{$search}%' || 
                   name_contact LIKE '%{$search}%' || 
                   lastname_contact LIKE '%{$search}%'
                ) LIMIT {$per_page} OFFSET {$offset}", 'ARRAY_A');

        } else {
            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status`=1 LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        }

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $institutes, 'total_count' => $total_count];
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

        global $wpdb;
        $table_institutes = $wpdb->prefix . 'institutes';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {

            $search = $_POST['s'];

            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * 
                FROM {$table_institutes}
                WHERE `status`=2
                AND 
                ( `name` LIKE '%{$search}%' ||
                   email LIKE '%{$search}%' || 
                   name_rector LIKE '%{$search}%' || 
                   lastname_rector LIKE '%{$search}%' || 
                   name_contact LIKE '%{$search}%' || 
                   lastname_contact LIKE '%{$search}%'
                ) LIMIT {$per_page} OFFSET {$offset}", 'ARRAY_A');

        } else {
            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status`=2 LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        }

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $institutes, 'total_count' => $total_count];
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

        global $wpdb;
        $table_institutes = $wpdb->prefix . 'institutes';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {

            $search = $_POST['s'];

            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * 
                FROM {$table_institutes}
                WHERE `status`=3
                AND 
                ( `name` LIKE '%{$search}%' ||
                   email LIKE '%{$search}%' || 
                   name_rector LIKE '%{$search}%' || 
                   lastname_rector LIKE '%{$search}%' || 
                   name_contact LIKE '%{$search}%' || 
                   lastname_contact LIKE '%{$search}%'
                ) LIMIT {$per_page} OFFSET {$offset}", 'ARRAY_A');

        } else {
            $institutes = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes} WHERE `status`=3 LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        }

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $institutes, 'total_count' => $total_count];
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

        $password = generate_password_user();

        $userdata = [
            'user_login' => $institute->email,
            'user_pass' => $password,
            'user_email' => $institute->email,
            'first_name' => $institute->name,
        ];

        $user_id = wp_insert_user($userdata);
        $user = new WP_User($user_id);
        $user->remove_role('subscriber');
        $user->add_role('institutes');

        update_user_meta($user_id, 'institute_id', $institute->id);

        wp_new_user_notification($user_id, null, 'both');
    } else {
        update_user_meta($user->id, 'institute_id', $institute->id);
    }
}

function get_students_institute($institute_id) {
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $query = $wpdb->prepare(
        "SELECT * FROM {$table_students} WHERE institute_id = %d ORDER BY id DESC",
        $institute_id
    );
    $students = $wpdb->get_results($query);
    return $students;
}

function get_all_students_institute($institute_id) {
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