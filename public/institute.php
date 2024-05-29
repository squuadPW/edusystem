<?php

function form_register_agreement(){
    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/register-agreement.php');
}

add_shortcode('form_register_agreement', 'form_register_agreement');

function save_institute(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'save_institute'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';

            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $address = $_POST['address'];
            $level = $_POST['level'];
            $rector_name = $_POST['rector_name'];
            $rector_lastname = $_POST['rector_lastname'];
            $rector_phone = $_POST['rector_phone'];
            $reference = $_POST['reference'];

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
                'created_at' => date('Y-m-d H:i:s')
            ]);

            wc_add_notice(__( 'Registration sent. Wait for confirmation.', 'aes' ), 'success' );
            wp_redirect(home_url('registration-agreement'));
            exit;
        }
    }
}

add_action('wp_loaded','save_institute');