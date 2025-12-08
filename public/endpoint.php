<?php

function wp_api()
{
    // register_rest_route('api', '/adjust-projection-student', array(
    //     'methods' => 'POST',
    //     'callback' => 'adjust_projection_student',
    //     'permission_callback' => '__return_true'
    // ));
}
add_action('rest_api_init', 'wp_api');
