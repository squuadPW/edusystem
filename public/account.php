<?php

function fee_inscription_payment(){
    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'fee_inscription_payment'){

            // Check if fee_student_id is set
            if (isset($_GET['fee_student_id']) && !empty($_GET['fee_student_id'])) {
                $fee_student_id = $_GET['fee_student_id'];

                // Set the cookie
                setcookie('fee_student_id', $fee_student_id, time() + (86400 * 30), '/'); // expires in 30 days
            }

            global $woocommerce;

            //clear cart
            $woocommerce->cart->empty_cart(); 

            $woocommerce->cart->add_to_cart(FEE_INSCRIPTION, 1);

            wp_redirect(wc_get_checkout_url());
            exit;
        };

    }
}

add_action('wp_loaded','fee_inscription_payment');