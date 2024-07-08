<?php

add_action('woocommerce_account_academic-services_endpoint', function(){

    global $current_user;
    $roles = $current_user->roles;
    include(plugin_dir_path(__FILE__).'templates/academic-services.php');
});