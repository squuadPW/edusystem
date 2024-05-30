<?php

function form_alliance_registration(){
    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/alliance-registration.php');
}

add_shortcode('form_alliance_registration','form_alliance_registration');

function save_partner(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'save_alliances'){

            global $wpdb;
            $table_alliances =  $wpdb->prefix.'alliances';

            $name = $_POST['name'];
            $last_name = $_POST['last_name'];
            $name_legal = $_POST['name_legal'];
            $number_phone = $_POST['number_phone'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $address = $_POST['address'];

            $wpdb->insert($table_alliances,[
                'name' => $name,
                'last_name' => $last_name,
                'name_legal' => $name_legal,
                'phone' => $number_phone,
                'email' => $email,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'address' => $address,
                'type' => 0,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

    
            wc_add_notice(__( 'Registration sent. Wait for confirmation.', 'aes' ), 'success' );
            wp_redirect(home_url('alliance-registration'));
            exit;

        }
    }
}

add_action('wp_loaded','save_partner');