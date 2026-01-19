<div>
    <?php

        global $woocommerce;
        $cart = $woocommerce->cart;
        $site_mode = get_option('site_mode');

        // excluye los productos de fee
        $separate_program_fee = $_COOKIE['separate_program_fee'] ?? false;
        $fees = $_COOKIE['separate_program_fee'] ? json_decode($_COOKIE['separate_program_fee']) : [];

        $product_id = null;
        foreach( $cart->get_cart() as $cart_item_key => $item ) {

            $is_program = has_term('programs', 'product_cat', (int) $item['product_id']);
            if ( $is_program ) {

                $product_id = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];

            } else if( in_array($item['product_id'], [$fees]) ) {

                if( isset($item['program_data'])  ){

                    $program_data = $item['program_data'];
                    $product_id = (int) $program_data['variation_id'] ? $program_data['variation_id'] : $program_data['product_id'];
                    
                }
            }

        }
        
        $product = wc_get_product($product_id);

    ?>

    <?php if ( $product && !isset($_COOKIE['is_scholarship']) ): ?>

        <?php
        $cookie_name = 'fixed_fee_inscription';

        // Check if the cookie does NOT exist
        $cookie_does_not_exist = !isset($_COOKIE[$cookie_name]);

        // Check if the cookie exists AND is NOT empty AND is NOT 'true'
        $cookie_exists_and_condition_met = (
            isset($_COOKIE[$cookie_name]) &&
            !empty($_COOKIE[$cookie_name]) &&
            $_COOKIE[$cookie_name] !== 'true'
        );

        if ((!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) && ($cookie_does_not_exist || $cookie_exists_and_condition_met)): ?>

            <div>
                <div style="margin-bottom: 10px !important; text-align: center">
                    <label class="fee-container">
                        <strong><?= __('Registration fee', 'edusystem') ?></strong>
                        <br>
                        <span><?= __('(You can pay it before starting classes in your account)', 'edusystem') ?></span>

                        <input name="fee" type="checkbox" checked="checked">
                        <span class="checkmark"></span>
                    </label>
                </div>
            </div>
        <?php endif ?>

        <?php if ($site_mode != 'UNI') { ?>
            <div class="text-center elements-quote-hidden" style="padding: 18px 0px;">
                <label><?= __('Apply to get the discount', 'edusystem') ?></label>
                <div id="button-schoolship"></div>
            </div>
        <?php } ?>

        <?= do_shortcode('[PROGRAM-QUOTAS]') ?>

        <input type="hidden" name="submit" value="Apply Scholarship">
    <?php endif; ?>
</div>

<?php







