<?php

function form_alliance_registration(){
    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/alliance-registration.php');
}

add_shortcode('form_alliance_registration','form_alliance_registration');

function save_partner(){

    if(isset($_POST['action']) && !empty($_POST['action'])){

        if($_POST['action'] == 'save_alliances'){

            global $wpdb;
            $table_alliances =  $wpdb->prefix.'alliances';

            $name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $name_legal = $_POST['name_legal'];
            $number_phone = $_POST['number_phone_hidden'];
            $email = $_POST['current_email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $address = $_POST['address'];

            $user = get_user_by('email',$email);

            if(!$user){

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
                $new_alliance = WC()->mailer()->get_emails()['WC_Registered_Partner_Email'];
                $new_alliance->trigger($wpdb->insert_id);
                wc_add_notice(__( 'Registration sent. Wait for confirmation.', 'aes' ), 'success' );
            }else{
                wc_add_notice(__( 'Existing email, please enter another email', 'aes' ), 'error' );
            }
        }
    }
}

add_action('wp_loaded','save_partner');