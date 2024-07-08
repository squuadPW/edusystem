<?php

function form_register_agreement(){
    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/register-agreement.php');
}

add_shortcode('form_register_agreement', 'form_register_agreement');

function save_institute(){

    if(isset($_POST['action']) && !empty($_POST['action'])){

        if($_POST['action'] == 'save_institute'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';

            $name = strtolower($_POST['name_institute']);
            $phone = $_POST['number_phone_hidden'];
            $email = $_POST['current_email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = strtolower($_POST['city']);
            $address = $_POST['address'];
            $level = $_POST['level'];
            $rector_name = strtolower($_POST['rector_name']);
            $rector_lastname = strtolower($_POST['rector_lastname']);
            $rector_phone = $_POST['number_rector_phone_hidden'];
            $reference = $_POST['reference'];

            $user = get_user_by('email',$email);

            if(!$user){

                $wpdb->insert($table_institutes,[
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'address' => $address,
                    'level_id' => $level,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_lastname,
                    'phone_rector' => $rector_phone,
                    'reference' => $reference,
                    'status' => 0,
                    'fee' => 5.0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);


                $new_institute = WC()->mailer()->get_emails()['WC_Registered_Institution_Email'];
                $new_institute->trigger($wpdb->insert_id);
                wc_add_notice(__( 'Registration sent. Wait for confirmation.', 'aes' ), 'success' );
            }else{
                wc_add_notice(__( 'Existing email, please enter another email.', 'aes' ), 'error' );
            }
        }
    }
}

add_action('wp_loaded','save_institute');

function get_list_institutes_active(){

    global $wpdb;
    $table_institutes =  $wpdb->prefix.'institutes';

    $list_institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status=1");
    return $list_institutes;
}

function set_institute_in_order($order){

    if(isset($_COOKIE['institute_id']) && !empty($_COOKIE['institute_id'])){

        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institutes';
        $table_alliances =  $wpdb->prefix.'alliances';

        $data = $wpdb->get_row("SELECT id,fee,alliance_id FROM {$table_institutes} WHERE id={$_COOKIE['institute_id']}");

        if($data){

            $order->update_meta_data('institute_id',$_COOKIE['institute_id']);

            $fee_institute = $data->fee;
            $total_order = $order->get_total();

            $total_institute_fee = ($fee_institute * $order->get_total()) / 100;
            $order->update_meta_data('institute_fee',$total_institute_fee);
           

            // si tiene alianza
            if($data->alliance_id != ''){

                $alliance_id = $data->alliance_id;
                $order->update_meta_data('alliance_id',$data->alliance_id);
                $data_alliance = $wpdb->get_row("SELECT fee FROM {$table_alliances} WHERE id={$alliance_id}");

                if(!empty($data_alliance)){

                    $fee_alliance = $data_alliance->fee;
                    $total_alliance_fee = ($fee_alliance * $order->get_total()) / 100;
                    $order->update_meta_data('alliance_fee',$total_alliance_fee);
                }
            }

            $order->save();
        }
    }
}