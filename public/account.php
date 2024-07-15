<?php

function fee_inscription_payment(){
    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'fee_inscription_payment'){

            global $woocommerce;

            //clear cart
            $woocommerce->cart->empty_cart(); 

            $woocommerce->cart->add_to_cart(AES_FEE_INSCRIPTION, 1);

            wp_redirect(wc_get_checkout_url());
            exit;
        };

    }
}

add_action('wp_loaded','fee_inscription_payment');