<?php

function fee_inscription_payment(){
    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'fee_inscription_payment'){

            global $woocommerce;

            //clear cart
            $woocommerce->cart->empty_cart(); 

            setcookie('fee_student_id',$_GET['fee_student_id'],time() + 3600);

            // EN LOCAL JOSE MORA
            $woocommerce->cart->add_to_cart(484, 1);

            // EN AWS
            // $woocommerce->cart->add_to_cart(63, 1);

            wp_redirect(wc_get_checkout_url());
            exit;
        };

    }
}

add_action('wp_loaded','fee_inscription_payment');